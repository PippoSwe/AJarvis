<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require  FCPATH . '/vendor/autoload.php';
require  FCPATH . '/application/core/MY_Standup.php';

use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\Speech\SpeechClient;
use Google\Cloud\Language\LanguageClient;

function standup_resource($var)
{
    $path_info = pathinfo($var);
    if(strpos($var, 'standup-') === false)
        return false;
    return (strtolower($path_info['extension']) == 'flac');
}

class Google extends MY_Standup
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Config_model', 'configs', TRUE);
        $this->load->model('Standup_model', 'standups', TRUE);
        $this->load->model('ProjectKeyword_model', 'projects_keywords', TRUE);
        $this->load->model('ProjectMember_model', 'projects_members', TRUE);
        $this->load->library('Uuid');
        $this->load->helper(array('directory', 'google_nlp_helper'));
    }

    public function index() {
        $entry = $this->configs->get("service_type");
        if(is_null($entry))
            show_error("Google Service Type is not defined");

        if($entry->value != 'local')
            show_error("You need to declare 'local' conversion service");

        $voice_pause = 2.0;
        $entry = $this->configs->get("silence_tolerance");
        if(!is_null($entry))
            $voice_pause = floatval($entry->value);

        $entry = $this->configs->get("key_file");
        if(is_null($entry)) {
            show_error("Google Cloud key is missing");
            exit;
        }

        // Google cloud
        $json_key = json_decode($entry->value, true);

        // Google storage Bucket
        $entry = $this->configs->get("bucket_name");
        if(is_null($entry)) {
            show_error("Google Storage Bucket is missing");
            exit;
        }
        $bucket_name = $entry->value;

        //php index.php batch/speech index
        $path = realpath("./application/cron_files");
        $audio_files = directory_map($path, 1);
        $flac_files = array_values(array_filter($audio_files, "standup_resource"));
        if(sizeof($flac_files) == 0)
            exit;

        // File to start the processing
        log_message('info', 'Processing '.$flac_files[0].'...');
        $input = realpath($path."/".$flac_files[0]);

        // id to perform the indexing
        $gs_url = "gs://".$bucket_name."/".$flac_files[0];
        $id = str_replace("standup-", "", $flac_files[0]);
        $id = explode(".", $id)[0];

        // Prima di cominciare rinomino il file
        $file_uuid = $this->uuid->v4();
        $new_input = $path."/".$file_uuid.".FLAC";
        rename($input, $new_input);
        if(!file_exists($new_input)) {
            show_error("Some process work on this file before you do that ...");
            exit;
        }
        // Adesso il nuovo file è quello con l'uuid
        $input = realpath($new_input);

        // Keywords per il progetto
        $phrases = array();
        $standup_entry = $this->standups->get($id);
        if(!is_null($standup_entry)) {
            // Keywords
            foreach($this
                        ->projects_keywords
                        ->find($standup_entry->project_id)
                    as $keyword_entry) {
                $phrases[] = $keyword_entry->keyword;

            }
            // Members
            foreach($this
                        ->projects_members
                        ->find($standup_entry->project_id)
                    as $member_entry) {
                $member = $member_entry->firstname;
                if(!empty($member_entry->lastname) && !empty($member))
                    $member .= " ";
                $member .= $member_entry->lastname;
                $phrases[] = $member;
            }
        }

        try {
            $speech = new SpeechClient([
                'keyFile' => $json_key,
                'languageCode' => 'it-IT'
            ]);
            // Start nlp
            $options = array();
            $options['enableWordTimeOffsets'] = true;
            if(sizeof($phrases) > 0)
                $options['speechContexts'] = array(
                    array(
                        'phrases' => $phrases
                    )
                );

            $operation = $speech->beginRecognizeOperation(
                $gs_url,
                $options
            );
        }
        catch(ServiceException $e) {
            $this->set_stt_status($id,
                ["status" => "Failed", "logs" => $e->getTraceAsString()]);
            show_error($e);
            exit;
        }

        $isComplete = $operation->isComplete();
        while (!$isComplete) {
            sleep(1); // let's wait for a moment...
            $operation->reload();
            $isComplete = $operation->isComplete();
        }
        // start with global transcription
        // $this->voice_pause definisce i millisecondi per spezzare le frasi
        $transcription = "";
        foreach ($operation->results() as $result) {
            foreach ($result->alternatives() as $alternative) {
                foreach ($alternative['words'] as $wordInfo) {
                    $transcription .= $wordInfo['word'];
                    if((floatval($wordInfo['endTime']) - floatval($wordInfo['startTime'])) > $voice_pause)
                        $transcription .= ".";
                    $transcription .= " ";
                }
            }
        }

        // non è riuscito a truadurre nulla
        if(empty($transcription)) {
            $this->set_stt_status($id,
                ["status" => "Failed", "logs" => "Non sono riuscito a trascrivere l'audio in testo"]);
            show_error("No transcription found");
            exit;
        }

        $this->set_stt_status($id,
            ["status" => "Success"]);

        // Inizio l'NLP
        $language = new LanguageClient([
            'keyFile' => $json_key
        ]);
        $config = array(
            'features' => ['entities', 'syntax', 'sentiment'],
            'language' => 'it-IT',
            'type' => 'PLAIN_TEXT',
            'encodingType' => 'UTF8'
        );
        $standup_entity = $this->set_nlp($id, json_encode($language->annotateText($transcription, $config)->info()));
        if($standup_entity == null)
            $this->set_nlp_status($id,
                ["status" => "Failed", "logs" => "Nessuna analisi disponibile"]);
        else {
            $this->set_stt_status($id,
            ["status" => "Success"]);
            // Elimino il file audio
            // il processo è andato a buon fine
            unlink($input);
        }
    }

}
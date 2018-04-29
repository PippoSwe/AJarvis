<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Nlp_test extends TestCase
{

    // https://github.com/kenjis/ci-phpunit-test
    // https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md
    private static $fk1_key;
    private static $key;
    private static $fk1_page = 'api/project/';
    private static $page = 'api/standup/';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $CI =& get_instance();
        $CI->load->database();
    }

    public function setUp()
    {
        $this->request->setCallable(
            function ($CI) {
                $CI->load->database();
            }
        );
    }

    public function test_project_post()
    {
        $output = $this->request('POST', self::$fk1_page, [
            'project' => 'Progettone della vita']);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('project', $data);
        $this->assertArrayHasKey('id', $data);
        self::$fk1_key = $data['id'];
    }


    public function test_post()
    {
        /* Necessari aggiunstamenti alla funzione save_audio()
            per consentire il fake upload dei dati */
        copy(realpath(dirname(__FILE__))."/fixtures/sample.wav",
            "/var/www/html/uploads/test.wav");
        $files = [
            'file' => [
                'name'     => "test.wav",
                'type'     => 'audio/wav',
                'tmp_name' => "/var/www/html/uploads/test.wav",
            ],
        ];
        $this->request->setFiles($files);
        $output = $this->request('POST', self::$fk1_page.self::$fk1_key.'/standup/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('project_id', $data);
        $this->assertArrayHasKey('standup', $data);
        $this->assertArrayHasKey('magnitude', $data);
        $this->assertArrayHasKey('score', $data);
        $this->assertArrayHasKey('end', $data);
        $this->assertArrayHasKey('id', $data);
        self::$key = $data['id'];
    }

    public function test_analysis() {

        // invio per analisi nlp
        $filename = realpath(dirname(__FILE__))."/fixtures/nlp.json";
        $handle = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);

        $output = $this->request('POST', self::$page.self::$key.'/nlp/',
            json_encode(json_decode($contents)));
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('project_id', $data);
        $this->assertArrayHasKey('standup', $data);
        $this->assertArrayHasKey('magnitude', $data);
        $this->assertArrayHasKey('score', $data);
        $this->assertArrayHasKey('end', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('sentence_count', $data);
        $this->assertArrayHasKey('entities_count', $data);

    }

    public function test_pie()
    {
        $output = $this->request('GET', self::$page.self::$key.'/pie/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('labels', $data);
        $this->assertArrayHasKey('series', $data);
    }

    public function test_flow()
    {
        $output = $this->request('GET', self::$page.self::$key.'/flow/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('series', $data);
    }

    public function test_sentences()
    {
        
        $output = $this->request('GET', self::$page.self::$key.'/sentences/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        foreach($data as $value){
            $this->assertTrue( property_exists($value, 'sentence') );
            $this->assertTrue( property_exists($value, 'score') );
            $this->assertTrue( property_exists($value, 'magnitude') );
            $this->assertTrue( property_exists($value, 'color') );
        }
    }

    public function test_sentences_good()
    {
        $output = $this->request('GET', self::$page.self::$key.'/sentences/good/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);

        foreach($data as $value){
            $this->assertTrue( property_exists($value, 'sentence') );
            $this->assertTrue( property_exists($value, 'score') );
            $this->assertTrue( property_exists($value, 'magnitude') );
        }
    }

    public function test_sentences_bad()
    {
        $output = $this->request('GET', self::$page.self::$key.'/sentences/bad/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);

        foreach($data as $value){
            $this->assertTrue( property_exists($value, 'sentence') );
            $this->assertTrue( property_exists($value, 'score') );
            $this->assertTrue( property_exists($value, 'magnitude') );
        }
    }

    public function test_entities()
    {
        $output = $this->request('GET', self::$page.self::$key.'/entities/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);

        foreach($data as $value){
            $this->assertTrue( property_exists($value, 'name') );
            $this->assertTrue( property_exists($value, 'type') );
            $this->assertTrue( property_exists($value, 'salience') );
        }

    }

    public function test_project_flow()
    {
        $output = $this->request('GET', self::$fk1_page.self::$fk1_key.'/flow/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('series', $data);
    }

    public function test_project_sentences_good()
    {
        $output = $this->request('GET', self::$fk1_page.self::$fk1_key.'/sentences/good/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);

        foreach($data as $value){
            $this->assertTrue( property_exists($value, 'sentence') );
            $this->assertTrue( property_exists($value, 'score') );
            $this->assertTrue( property_exists($value, 'magnitude') );
            $this->assertTrue( property_exists($value, 'standup_id') );
        }
    }

    public function test_project_sentences_bad()
    {
        $output = $this->request('GET', self::$fk1_page.self::$fk1_key.'/sentences/bad/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);

        foreach($data as $value){
            $this->assertTrue( property_exists($value, 'sentence') );
            $this->assertTrue( property_exists($value, 'score') );
            $this->assertTrue( property_exists($value, 'magnitude') );
            $this->assertTrue( property_exists($value, 'standup_id') );
        }
    }

    public function test_project_entities()
    {
        $output = $this->request('GET', self::$fk1_page.self::$fk1_key.'/entities/');
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);

        foreach($data as $value){
            $this->assertTrue( property_exists($value, 'name') );
            $this->assertTrue( property_exists($value, 'type') );
            $this->assertTrue( property_exists($value, 'sum_salience') );
            $this->assertTrue( property_exists($value, 'count_salience') );
            $this->assertTrue( property_exists($value, 'standup_id') );
        }
    }

    public function test_delete()
    {
        $output = $this->request('DELETE', self::$fk1_page.self::$fk1_key.'/standup/'.self::$key);
        $this->assertResponseCode(200);
    }

    public function test_project_delete()
    {
        $output = $this->request('DELETE', self::$fk1_page.self::$fk1_key);
        $this->assertResponseCode(200);
    }

}

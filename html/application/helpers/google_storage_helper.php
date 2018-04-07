<?php defined('BASEPATH') OR exit('No direct script access allowed');

# Includes the autoloader for libraries installed with composer
require  FCPATH . '/vendor/autoload.php';

# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * @param string $file_path the path for find the file
 * @param string $file_name the name of the file
 * @return bool the result of the function
 */
function upload_file($file_path, $file_name)
{
    // https://github.com/GoogleCloudPlatform/google-cloud-php/blob/master/src/Storage/StorageClient.php
    $CI = & get_instance();
    $CI->load->model('Config_model', 'configs', TRUE);

    $bucket_name = "ajarvis-storage";
    $entry = $CI->configs->get("key_file");
    if(is_null($entry))
        return;

    $json_key = json_decode($entry->value, true);
    $storage = new StorageClient([
        'keyFile' => $json_key
    ]);

    // open file and instantiate bucket
    $file     = fopen($file_path, 'r');
    $bucket   = $storage->bucket($bucket_name);
    return $bucket->upload($file, [
            'name' => $file_name,
            'metadata' => ['contentType' => 'audio/flac', 'uploadType' => 'resumable']
        ]
    );
}

function checkConnection($key)
{
    if(empty($key))
        return;
    $storage = new StorageClient([
        'keyFile' => json_decode($key, true)
    ]);
    return $storage->buckets();
}
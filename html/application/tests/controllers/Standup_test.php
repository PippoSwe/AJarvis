<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Standup_test extends TestCase
{

    // https://github.com/kenjis/ci-phpunit-test
    // https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md
    private static $fk1_key;
    private static $key;
    private static $fk1_page = 'api/project/';

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

    public function test_cant_post()
    {
        /* Necessari aggiunstamenti alla funzione save_audio()
            per consentire il fake upload dei dati */
        $files = [
            'file' => [
                'name'     => "test.wav",
                'type'     => 'audio/wav',
                'tmp_name' => "/var/www/html/uploads/test.wav",
            ],
        ];
        $this->request->setFiles($files);
        $output = $this->request('POST', self::$fk1_page.self::$fk1_key.'/standup/',
            ['standup' => 'Standup test']);
        $data = (array) json_decode($output);
        $this->assertResponseCode(500);
    }

    public function test_put()
    {
        $output = $this->request('PUT', self::$fk1_page.self::$fk1_key.'/standup/'.self::$key,
            ['standup' => 'Standup test update']);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('project_id', $data);
        $this->assertArrayHasKey('standup', $data);
        $this->assertArrayHasKey('magnitude', $data);
        $this->assertArrayHasKey('score', $data);
        $this->assertArrayHasKey('end', $data);
        $this->assertArrayHasKey('id', $data);
    }

    public function test_view()
    {
        $output = $this->request('GET', self::$fk1_page.self::$fk1_key.'/standup/'.self::$key);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('project_id', $data);
        $this->assertArrayHasKey('standup', $data);
        $this->assertArrayHasKey('magnitude', $data);
        $this->assertArrayHasKey('score', $data);
        $this->assertArrayHasKey('end', $data);
        $this->assertArrayHasKey('id', $data);
    }

    public function test_index()
    {
        $output = $this->request('GET', self::$fk1_page.self::$fk1_key.'/standup/',
            ['limit' => 1]);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertCount(1, $data);
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

<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Config_test extends TestCase
{
    private static $page = 'api/config/';

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

    public function test_read()
    {
        $output = $this->request('GET', self::$page . 'read');
        $data = (array)json_decode($output);
        $this->assertResponseCode(200);

        foreach ($data as $value) {
            $this->assertTrue(property_exists($value, 'key'));
            $this->assertTrue(property_exists($value, 'value'));
        }
    }

    public function test_update()
    {
        $this->request('POST', self::$page . 'update', [
            'key_file' => 'test']);
        $this->assertResponseCode(200);
    }

    public function test_connection_ok()
    {
        $this->request('POST', self::$page . 'checkConnection', [
            'key_file' => '']);
        $this->assertResponseCode(200);
    }

    public function test_connection()
    {
        $this->request('POST', self::$page . 'checkConnection', [
            'key_file' => 'test']);
        $this->assertResponseCode(500);
    }

    public function test_key_delete() {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->model('Config_model', 'configs', TRUE);
        $CI->configs->delete("key_file");
    }


}

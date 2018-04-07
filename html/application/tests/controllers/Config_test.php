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

        public function test_update()
    {
        $this->request('POST', self::$page . 'checkConnection');
        $this->assertResponseCode(500);
    }

}

<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Config_model_test extends TestCase
{

    private static $key;

    public function setUp()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->model('Config_model', 'configs', TRUE);
        $this->obj = $this->CI->configs;
    }

    public function test_get_empty()
    {
        $data = $this->obj->get('');
        $this->assertEquals(null, $data);
    }

}

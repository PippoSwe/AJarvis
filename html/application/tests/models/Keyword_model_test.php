<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Keyword_model_test extends TestCase
{

    private static $key;

    public function setUp()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->model('Keyword_model', 'keywords', TRUE);
        $this->obj = $this->CI->keywords;
    }


	public function test_find()
	{
        $data = $this->obj->find($limit = 1);
        $this->assertInternalType('array', $data);
	}

    public function test_get_empty()
    {
        $data = $this->obj->get(-1);
        $this->assertEquals(null, $data);
    }

}

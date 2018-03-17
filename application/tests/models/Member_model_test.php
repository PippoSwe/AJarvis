<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Member_test extends TestCase
{

    private static $key;
    private static $page = 'api/member/';

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

    public function test_post()
    {
        // https://github.com/kenjis/ci-phpunit-test
        // https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md
        $output = $this->request('POST', self::$page, [
            'firstname' => 'Test','lastname' => 'Testoni']);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('firstname', $data);
        $this->assertArrayHasKey('lastname', $data);
        $this->assertArrayHasKey('id', $data);
        self::$key = $data['id'];
    }

    public function test_index()
    {
        $output = $this->request('GET', self::$page,
            ['limit' => 1]);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertCount(1, $data);
    }

	public function test_view()
	{
		$output = $this->request('GET', self::$page.self::$key);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('firstname', $data);
        $this->assertArrayHasKey('lastname', $data);
        $this->assertArrayHasKey('id', $data);
	}

    public function test_put()
    {
        $output = $this->request('PUT', self::$page.self::$key);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('firstname', $data);
        $this->assertArrayHasKey('lastname', $data);
        $this->assertArrayHasKey('id', $data);
    }

    public function test_delete()
    {
        $output = $this->request('DELETE', self::$page.self::$key);
        $this->assertResponseCode(200);
    }	


}

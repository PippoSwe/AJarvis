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

    // https://github.com/kenjis/ci-phpunit-test
    // https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md
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
        $output = $this->request('POST', self::$page, [
            'firstname' => 'Test','lastname' => 'Testoni','work' => true]);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('firstname', $data);
        $this->assertArrayHasKey('lastname', $data);
        $this->assertArrayHasKey('id', $data);
        self::$key = $data['id'];
    }

    public function test_like_name_surname()
    {
        $output = $this->request('GET', self::$page,
            ['q' => 'Test Te']);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertEquals('Test', $data[0]->firstname );
        $this->assertEquals('Testoni', $data[0]->lastname );
    }

    public function test_like_surname_name()
    {
        $output = $this->request('GET', self::$page,
            ['q' => 'Testoni Te']);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertEquals('Test', $data[0]->firstname );
        $this->assertEquals('Testoni', $data[0]->lastname );
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
        $output = $this->request('PUT', self::$page.self::$key, [
            'firstname' => 'Super', 'lastname' => 'Duper']);
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

<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class ProjectMember_test extends TestCase
{

    // https://github.com/kenjis/ci-phpunit-test
    // https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md
    private static $fk1_key;
    private static $fk2_key;
    private static $fk1_page = 'api/project/';
    private static $fk2_page = 'api/member/';

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

    /* Keywords */
    public function test_member_post()
    {
        $output = $this->request('POST', self::$fk2_page, [
            'firstname' => 'K firstname', 'lastname' => 'K lastname']);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        self::$fk2_key = $data['id'];
    }

    public function test_post()
    {
        $output = $this->request('POST', self::$fk1_page.self::$fk1_key.'/member/', [
            'member_id' => self::$fk2_key]);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('project_id', $data);
        $this->assertArrayHasKey('member_id', $data);
        $this->assertArrayHasKey('id', $data);
    }

    public function test_view()
    {
        $output = $this->request('GET', self::$fk1_page.self::$fk1_key.'/member/'.self::$fk2_key);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertArrayHasKey('project_id', $data);
        $this->assertArrayHasKey('member_id', $data);
        $this->assertArrayHasKey('id', $data);
    }

    public function test_index()
    {
        $output = $this->request('GET', self::$fk1_page.self::$fk1_key.'/member/',
            ['limit' => 1]);
        $data = (array) json_decode($output);
        $this->assertResponseCode(200);
        $this->assertCount(1, $data);
    }

    public function test_delete()
    {
        $output = $this->request('DELETE', self::$fk1_page.self::$fk1_key.'/member/'.self::$fk2_key);
        $this->assertResponseCode(200);
    }

    public function test_member_delete()
    {
        $output = $this->request('DELETE', self::$fk2_page.self::$fk2_key);
        $this->assertResponseCode(200);
    }

    public function test_project_delete()
    {
        $output = $this->request('DELETE', self::$fk1_page.self::$fk1_key);
        $this->assertResponseCode(200);
    }	

}

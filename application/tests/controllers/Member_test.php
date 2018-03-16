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
	public function test_view()
	{
        // inserici il record 30
		$output = $this->request('GET', 'api/member/view/1');
        $data = (array) json_decode($output);
        $this->assertArrayHasKey('firstname', $data);
        $this->assertArrayHasKey('lastname', $data);
        $this->assertArrayHasKey('id', $data);
	}
}

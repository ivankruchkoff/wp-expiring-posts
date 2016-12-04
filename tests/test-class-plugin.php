<?php

namespace Expiring_Posts;

class WP_Test_Expiring_Posts_Plugin extends \WP_UnitTestCase {

	protected $plugin;
	protected $expired_post_status;

	public function setUp() {
		parent::setUp();
		$plugin = new Plugin();
		$this->plugin = $plugin;
		$this->expired_post_status = $plugin::EXPIRED_POST_STATUS;
	}

	public function test_setup() {
		$this->assertInstanceOf( __NAMESPACE__ . '\Admin', $this->plugin->admin );
	}

	public function test_expiring_post_status() {
		$this->assertTrue( array_key_exists( $this->expired_post_status, get_post_stati() ) );
	}

	public function test_expire_post() {
		$post_id = $this->create_post();
		$this->plugin->expire_post( $post_id );
		$this->assertEquals( $this->expired_post_status, get_post_status( $post_id ) );
	}

	public function test_schedule_expire_post() {
		$post_id = $this->create_post();
		$expiry_date = time() + 1;
		$this->plugin->schedule_expire_post( $post_id, $expiry_date );
		$this->assertEquals( $this->plugin->get_expiry_date( $post_id ), date_i18n( 'Y-m-d H:i:s', $expiry_date ) );

		return $post_id;
	}

	public function test_unschedule_expire_post() {
		$post_id = $this->test_schedule_expire_post();
		$this->plugin->unschedule_expire_post( $post_id );
		$this->assertEmpty( $this->plugin->get_expiry_date( $post_id ) );
	}

	public function test_init_cron() {
		$this->assertTrue( array_key_exists( '5min', wp_get_schedules() ) );
	}

	public function test_schedule_cron() {
		$this->assertInternalType( 'int', wp_next_scheduled( 'delete_expired_posts' ) );
	}

	public function test_delete_expired_posts() {
		$post_id = $this->test_schedule_expire_post();
		sleep( 2 );
		$this->plugin->delete_expired_posts();

		$this->assertEquals( $this->expired_post_status, get_post_status( $post_id ) );
	}

	protected function create_post() {
		return wp_insert_post(array(
			'post_title' => 'test',
			'post_content' => '',
			'post_status' => 'publish',
		) );
	}
}

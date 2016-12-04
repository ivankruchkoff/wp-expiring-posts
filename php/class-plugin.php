<?php

namespace Expiring_Posts;

class Plugin
{
	/**
	 * Date format to keep expiry date
	 */
	const DATE_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Expired post status name
	 */
	const EXPIRED_POST_STATUS = 'expired';

	/**
	 * Meta key name used to save expiry date
	 */
	const META_KEY = 'exp_expiration_date';

	/**
	 * Number of posts to process in one cron run
	 */
	const POSTS_PER_RUN = 10;

	/**
	 * Hold the Admin class
	 *
	 * @var Admin
	 */
	public $admin;

	/**
	 * Plugin constructor.
	 */
	function __construct() {

		// Enqueue custom post status
		add_action( 'init', array( $this, 'expiring_post_status' ) );

		// Unschedule exp_expire_post_event for this post if it is deleted
		add_action( 'after_delete_post', array( $this, 'unschedule_expire_post' ) );

		// Action that delete expired posts (runs by WP cron)
		add_action( 'delete_expired_posts', array( $this, 'delete_expired_posts' ) );

		// Add 5 mins interval to WP cron schedule
		// @codingStandardsIgnoreStart
		add_filter( 'cron_schedules', array( $this, 'init_cron' ) );
		// @codingStandardsIgnoreEnd

		// Schedule WP cron
		add_action( 'init', array( $this, 'schedule_cron' ) );

		$this->admin = new Admin( $this );
	}

	/**
	 * Register custom status for expired posts
	 */
	public function expiring_post_status() {
		$args = array(
		'label'                     => _x( 'Expired', 'post' ),
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>' ),
		);

		$args = apply_filters( 'exp_expired_post_status_args', $args );
		register_post_status( self::EXPIRED_POST_STATUS, $args );
	}

	/**
	 * Sets the given posts status to expired
	 *
	 * @param $post_id
	 */
	public function expire_post( $post_id ) {
		wp_update_post(
			array(
			'ID'          => $post_id,
			'post_status' => self::EXPIRED_POST_STATUS,
			)
		);
	}

	/**
	 * Schedule a post to be expired. If the expiration date is not given,
	 * the post will expire immediately.
	 *
	 * @param $post
	 * @param $expiration_date, GMT Timestamp
	 *
	 * @return bool|WP_Error
	 */
	public function schedule_expire_post( $post, $expiration_date = false ) {
		if ( ! $expiration_date ) {
			$expiration_date = time();
		}

		if ( ! is_int( $expiration_date ) ) {
			$expiration_date = strtotime( $expiration_date );
		}

		if ( ! ( $post = get_post( $post ) ) || false === $expiration_date ) {
			return new WP_Error( 'exp_expiring_posts_error', __( 'Either the post or expiration date provided are not valid.' ) );
		}

		$expiration_datef = date_i18n( 'Y-m-d H:i:s', $expiration_date );

		update_post_meta( $post->ID, self::META_KEY, sanitize_text_field( $expiration_datef ) );

		if ( $expiration_date <= time() ) {
			$this->expire_post( $post->ID );
		}

		return true;
	}

	/**
	 * Unschedule a post from expiring
	 *
	 * @param $post_id
	 */
	public function unschedule_expire_post( $post_id ) {
		delete_post_meta( $post_id, self::META_KEY );
	}

	/**
	 * Add 5 min interval to WP cron schedule
	 *
	 * @param $schedules
	 * @return mixed
	 */
	public function init_cron( $schedules ) {
		if ( ! isset( $schedules['5min'] ) ) {
			$schedules['5min'] = array(
				'interval' => 5 * MINUTE_IN_SECONDS,
				'display' => __( 'Once every 5 minutes' ),
			);
		}

		return $schedules;
	}

	/**
	 * Schedule delete_expired_posts action
	 */
	public function schedule_cron() {
		if ( ! wp_next_scheduled( 'delete_expired_posts' ) ) {
			wp_schedule_event( time(), '5min', 'delete_expired_posts' );
		}
	}

	/**
	 * Change status of expired posts
	 */
	public function delete_expired_posts() {
		$post_statuses = get_post_stati();
		unset( $post_statuses[ self::EXPIRED_POST_STATUS ] );

		// @codingStandardsIgnoreStart
		$wp_query = new \WP_Query();

		$query_vars = array(
			'es'                  => true,
			'fields'              => 'ids',
			'meta_key'            => self::META_KEY,
			'meta_value'          => date( 'Y-m-d H:i:s' ),
			'meta_compare'        => '<',
			'meta_type'           => 'DATETIME',
			'posts_per_page'      => self::POSTS_PER_RUN,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'post_status'         => array_keys( $post_statuses ),
			'orderby'             => 'meta_value',
		);

		$posts = $wp_query->query( $query_vars );
		// @codingStandardsIgnoreEnd

		foreach ( $posts as $post_id ) {
			$this->expire_post( $post_id );
		}
	}

	public function get_expiry_date( $post_id ) {
		return get_post_meta( $post_id, 'exp_expiration_date', true );
	}
}

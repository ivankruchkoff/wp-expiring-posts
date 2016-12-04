<?php

namespace Expiring_Posts;

class Admin
{
	/**
	 * Hold Plugin instance.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * @var string
	 */
	protected $plugin_url;

	/**
	 * Admin constructor.
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;

		$this->plugin_url = plugins_url( '../', __FILE__ );

		// make sure expired post meta field follows directly after publish field
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_meta_box' ), 5 );

		// enqueue admin JS and CSS for styling and DOM Voodoo
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Update all posts view to show expired status
		add_action( 'display_post_states', array( $this, 'add_expiry_post_states' ), 10, 2 );

		// Save expired posts meta
		add_action( 'save_post', array( $this, 'save_expiration_date' ) );
	}

	/**
	 * Save date for expiring posts
	 * Called from save_post
	 *
	 * @param $post_id
	 */
	public function save_expiration_date( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$expiry_date = filter_input( INPUT_POST, 'expiry', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$expiry_disabled = filter_input( INPUT_POST, 'expiry-disable' );

		if ( $expiry_disabled ) {
			$this->plugin->unschedule_expire_post( $post_id );
			return;
		}

		if ( ! $expiry_date ) {
			return;
		}

		check_admin_referer( 'save_expiration_post_meta', 'expiring_posts_nonce' );

		$post_status = get_post_status( $post_id );

		if ( 'expired' === $post_status ) {
			return;
		}

		$fields = array( 'year', 'month', 'day', 'hour', 'minute' );
		foreach ( $fields as  $field ) {
			if ( ! array_key_exists( $field, $expiry_date ) || ! $value = intval( $expiry_date[ $field ] ) ) {
				return;
			}

			${$field} = intval( $value );
		}

		$plugin = $this->plugin;
		if ( $minute < 10 ) {
			$minute = '0' . $minute;
		}

		$expiration_date = \DateTime::createFromFormat( $plugin::DATE_FORMAT, "$year-$month-$day $hour:$minute:00" );
		$last_error = \DateTime::getLastErrors();

		if ( ! $expiration_date || $last_error['warning_count'] || $last_error['error_count'] ) {
			return;
		}

		$this->plugin->schedule_expire_post( $post_id, $expiration_date->getTimestamp() );
	}

	/**
	 * Enqueue scripts for arranging elements
	 */
	public function admin_scripts( $page ) {

		if ( 'post.php' !== $page && 'post-new.php' !== $page ) {
			return;
		}

		wp_enqueue_script( 'admin-expiring-posts', $this->plugin_url . '/inc/js/expiring-posts.js', array( 'jquery' ) );

		$plugin = $this->plugin;
		wp_localize_script(
			'admin-expiring-posts', 'AdminExpiringPosts', array(
			'expires_text'     => __( 'Expires: ' ),
			'expired_text'   => __( 'Expired' ),
			'save_text'      => __( 'Save Post' ),
			'expired_status' => $plugin::EXPIRED_POST_STATUS,
			'post_status'    => get_post_status(),
			)
		);

		wp_enqueue_style( 'expiring-posts-css', $this->plugin_url . '/inc/css/expiring-posts.css' );
	}

	/**
	 * Handle new Expired metabox
	 * Called from post_submitbox_misc_actions
	 */
	public function post_meta_box( \WP_Post $post ) {
		$post_type_object = get_post_type_object( $post->post_type );
		$can_publish = current_user_can( $post_type_object->cap->publish_posts );

		$expiration_date = $this->plugin->get_expiry_date( $post->ID );

		require __DIR__ . '/views/expiry-form.php';
	}

	/**
	 * Display expired/expiring status in All Posts view
	 *
	 * @param $states
	 */
	public function add_expiry_post_states( array $states, \WP_Post $post ) {
		$plugin = $this->plugin;
		$is_expired = get_post_status( $post->ID ) === $plugin::EXPIRED_POST_STATUS;
		$expiration_date = $plugin->get_expiry_date( $post->ID );

		if ( $is_expired || $expiration_date ) {
			$states[] = __( '<span class="expiry">' . ( $is_expired ? 'Expired' : "Expiring: $expiration_date" ) . '</span>' );
		}

		return $states;
	}
}

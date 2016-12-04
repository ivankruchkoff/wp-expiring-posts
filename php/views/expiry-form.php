<?php

namespace Expiring_Posts;

/**
 * @var \WP_Post  $post Post object or NULL if it is a new post
 * @var int       $expiration_date Expiry timestamp
 **/

?>

<?php wp_nonce_field( 'save_expiration_post_meta', 'expiring_posts_nonce' ); ?>

<div class="misc-pub-section curtime">
	<span id="exp-timestamp">
	<?php
		$date = date_i18n( 'M j, Y @ G:i', strtotime( $expiration_date ) );

		if ( 'expired' === $post->post_status ) { ?>
			Expired on: <b><?php echo esc_html_e( $date );?></b>
		<?php } else if ( $expiration_date ) { ?>
			Expires on: <b><?php echo esc_html_e( $date );?></b>
		<?php } else { ?>
			Expires <b>never</b>
		<?php } ?>
	</span>
	<a href="#" id="exp-edit-timestamp" class="hide-if-no-js"><?php esc_html_e( 'Edit' ) ?></a>
	<div class="hide-if-js" id="exp-timestampdiv">

		<input type="checkbox" value="1" name="expiry-disable" id="expiry-disable" <?php checked( ! $expiration_date ) ?> />
		<label for="expiry-disable"><?php esc_html_e( 'Never expire' ); ?></label>

		<?php

			if ( $expiration_date ) {
				$expiration_date = strtotime( $expiration_date );
			} else {
				$expiration_date = time() + ( DAY_IN_SECONDS * 5 );
			}

			$expiry_day    = date( 'd', $expiration_date );
			$expiry_month  = date( 'n', $expiration_date );
			$expiry_year   = date( 'Y', $expiration_date );
			$expiry_hour   = date( 'G', $expiration_date );
			$expiry_minute = date( 'i', $expiration_date );
		?>

		<div class="exp-date">
			<select name="expiry[month]" id="expiry_month">
			<?php for ( $month = 1; $month <= 12; $month++ ) {
				$date = mktime(0, 0, 0, $month, 1, $expiry_year);
				echo '<option data-short="' . esc_attr( date('M', $date) ) . '" value="' . $month . '"' . ($month === intval($expiry_month) ? ' selected="selected"' : '') . '>';
				echo esc_attr( date('m-M', $date) );
				echo '</option>';
			} ?>
			</select>

			<input type="text" id="expiry_day" name="expiry[day]" value="<?php echo esc_attr( $expiry_day )?>" size="2" maxlength="2" />,
			<input type="text" id="expiry_year" name="expiry[year]" value="<?php echo esc_attr( $expiry_year )?>" size="4" maxlength="4" class="exp-year"  />@
			<input type="text" id="expiry_hour" name="expiry[hour]" value="<?php echo esc_attr( $expiry_hour )?>" size="2" maxlength="2" />:
			<input type="text" id="expiry_minute" name="expiry[minute]" value="<?php echo esc_attr( $expiry_minute )?>" size="2" maxlength="2" />
		</div>

		<div class="exp-buttons">
			<a href="#exp-edit_timestamp" class="exp-save-timestamp hide-if-no-js button"><?php esc_html_e( 'OK' ); ?></a>
			<a href="#exp-edit_timestamp" class="exp-cancel-timestamp hide-if-no-js"><?php esc_html_e( 'Cancel' ); ?></a>
		</div>
	</div>
</div>
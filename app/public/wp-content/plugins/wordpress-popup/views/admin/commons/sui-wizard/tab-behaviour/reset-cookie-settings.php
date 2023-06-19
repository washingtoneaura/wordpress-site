<?php
/**
 * Block for Reset Coockie settings.
 *
 * @uses ../reset-cookie-settings/
 *
 * @package Hustle
 */

	$expiration_key      = $option_prefix . 'expiration';
	$expiration_unit_key = $option_prefix . 'expiration_unit';
	$expiration_unit     = $settings[ $expiration_unit_key ];
?>

<div class="sui-border-frame" style="margin-bottom: 5px;" data-field-content="<?php echo esc_attr( $data_field_content ); ?>">

	<label class="sui-label"><?php esc_html_e( 'Reset this after', 'hustle' ); ?></label>

	<div class="sui-row">

		<div class="sui-col-md-6">

			<input type="number"
				value="<?php echo esc_attr( $settings[ $expiration_key ] ); ?>"
				min="0"
				class="sui-form-control"
				data-attribute="<?php echo esc_attr( $expiration_key ); ?>" />

		</div>

		<div class="sui-col-md-6">

			<select data-attribute="<?php echo esc_attr( $expiration_unit_key ); ?>" >

				<option value="seconds"
					<?php selected( $expiration_unit, 'seconds' ); ?>>
					<?php esc_html_e( 'second(s)', 'hustle' ); ?>
				</option>

				<option value="minutes"
					<?php selected( $expiration_unit, 'minutes' ); ?>>
					<?php esc_html_e( 'minute(s)', 'hustle' ); ?>
				</option>

				<option value="hours"
					<?php selected( $expiration_unit, 'hours' ); ?>>
					<?php esc_html_e( 'hour(s)', 'hustle' ); ?>
				</option>

				<option value="days"
					<?php selected( $expiration_unit, 'days' ); ?>>
					<?php esc_html_e( 'day(s)', 'hustle' ); ?>
				</option>

				<option value="weeks"
					<?php selected( $expiration_unit, 'weeks' ); ?>>
					<?php esc_html_e( 'week(s)', 'hustle' ); ?>
				</option>

				<option value="months"
					<?php selected( $expiration_unit, 'months' ); ?>>
					<?php esc_html_e( 'month(s)', 'hustle' ); ?>
				</option>

			</select>

		</div>

		<div class="sui-col-md-12">
			<span class="sui-description"><?php echo esc_html( $description ); ?></span>
		</div>

	</div>

	<?php Opt_In_Utils::get_cookie_saving_notice(); ?>

</div>
<?php

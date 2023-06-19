<?php
/**
 * Tracking section under the "general" tab.
 *
 * @package Hustle
 * @since 4.6.1
 */

?>
<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Global Tracking', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Enable or disable views & conversion tracking for all modules.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-form-field">

			<label
				for="hustle-global-tracking-disabled"
				class="sui-toggle hustle-toggle-with-container"
				data-toggle-on="global-tracking-disabled"
			>

				<input
					type="checkbox"
					name="global_tracking_disabled"
					value="1"
					id="hustle-global-tracking-disabled"
					data-attribute="global_tracking_disabled"
					aria-labelledby="hustle-global-tracking-disabled-label"
					aria-describedby="hustle-global-tracking-disabled-description"
					<?php checked( $settings['global_tracking_disabled'] ); ?>
				/>

				<span class="sui-toggle-slider" aria-hidden="true"></span>

				<span id="hustle-global-tracking-disabled-label" class="sui-toggle-label"><?php esc_html_e( 'Disable Tracking', 'hustle' ); ?></span>

				<span id="hustle-global-tracking-disabled-description" class="sui-description"><?php esc_html_e( 'Views and conversions of all modules are tracked by default. If you don\'t want to track views and conversions at all, you can disable that here.', 'hustle' ); ?></span>

			</label>

		</div>

	</div>

</div>

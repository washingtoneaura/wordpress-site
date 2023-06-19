<?php
/**
 * Available integrations list wrapper.
 *
 * @package Hustle
 * @since 4.0.0
 */

?>
<div class="sui-box">

	<div class="sui-box-header">

		<h2 class="sui-box-title"><i class="sui-icon-thumbnails" aria-hidden="true"></i> <?php esc_html_e( 'Available Apps', 'hustle' ); ?></h2>

	</div>

	<div class="sui-box-body">

		<p><?php /* translators: Plugin name */ echo esc_html( sprintf( __( "%s integrates with your favorite email and data collection apps. Here's a list of all the available apps that you can connect to.", 'hustle' ), Opt_In_Utils::get_plugin_name() ) ); ?></p>

		<div id="hustle-not-connected-providers-section">
			<div class="hustle-integrations-display"></div>
		</div>

	</div>

</div>

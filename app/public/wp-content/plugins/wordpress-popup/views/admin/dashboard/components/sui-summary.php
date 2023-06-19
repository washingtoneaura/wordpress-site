<?php
/**
 * SUI Summary
 *
 * @package Hustle
 * @since 4.3.0
 */

?>

<h2 class="sui-screen-reader-text"><?php esc_html_e( 'Quick Summary', 'hustle' ); ?></h2>

<div class="<?php echo esc_attr( implode( ' ', $sui['summary']['classes'] ) ); ?>">

	<div class="sui-summary-image-space" style="<?php echo esc_attr( $sui['summary']['style'] ); ?>" aria-hidden="true"></div>

	<div class="sui-summary-segment">

		<div class="sui-summary-details">

			<?php /* translators: 1. Plugin name 2. Active modules total number */ ?>
			<p class="sui-screen-reader-text"><?php echo esc_html( sprintf( __( '%1$s has %2$s active modules', 'hustle' ), Opt_In_Utils::get_plugin_name(), esc_attr( $active_modules ) ) ); ?></p>

			<span class="sui-summary-large" aria-hidden="true"><?php echo esc_html( $active_modules ); ?></span>
			<span class="sui-summary-sub" aria-hidden="true"><?php esc_html_e( 'Active Modules', 'hustle' ); ?></span>

			<?php if ( Hustle_Settings_Admin::global_tracking() ) { ?>
				<?php /* translators: active modules total number */ ?>
				<p class="sui-screen-reader-text"><?php printf( esc_html__( 'Last conversion: %s', 'hustle' ), esc_html( $last_conversion ) ); ?></p>

				<span class="sui-summary-detail" aria-hidden="true"><?php echo esc_html( $last_conversion ); ?></span>
				<span class="sui-summary-sub" aria-hidden="true"><?php esc_html_e( 'Last Conversion', 'hustle' ); ?></span>
			<?php } ?>

		</div>

	</div>

	<div class="sui-summary-segment">

		<?php if ( is_array( $metrics ) && ! empty( $metrics ) ) : ?>

			<ul class="sui-list">

				<?php foreach ( $metrics as $key => $data ) : ?>

					<li class="hustle-<?php echo esc_attr( $key ); ?>">
						<span class="sui-list-label"><?php echo esc_html( $data['label'] ); ?></span>
						<span class="sui-list-detail"><?php echo wp_kses_post( $data['value'] ); ?></span>
					</li>

				<?php endforeach; ?>

			</ul>

		<?php else : ?>

			<p class="sui-description" aria-hidden="true"><?php esc_html_e( 'No data to display.', 'hustle' ); ?></p>

		<?php endif; ?>

	</div>

</div>

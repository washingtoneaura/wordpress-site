<?php
/**
 * Displays the listing page view when there are not modules.
 *
 * @package Hustle
 * @since 4.0.0
 */

?>

<div class="sui-box sui-message sui-message-lg">


	<?php
	if ( ! $this->is_branding_hidden ) :
		$image_attrs = array(
			'path'        => self::$plugin_url . 'assets/images/hustle-welcome.png',
			'retina_path' => self::$plugin_url . 'assets/images/hustle-welcome@2x.png',
		);
	else :
		$image_attrs = array(
			'path'   => $this->branding_image,
			'width'  => 172,
			'height' => 192,
		);
	endif;
	$image_attrs['class'] = 'sui-image';

	// Image markup.
	$this->render( 'admin/image-markup', $image_attrs );
	?>

	<div class="sui-message-content">

		<?php if ( isset( $message ) && '' !== $message ) { ?>

			<p><?php echo esc_html( $message ); ?></p>

		<?php } else { ?>

			<p><?php esc_html_e( "You don't have any module yet. Click on create button to start.", 'hustle' ); ?></p>

		<?php } ?>

		<?php if ( $capability['hustle_create'] ) { ?>

			<p>
				<button
					id="hustle-create-new-module"
					class="sui-button sui-button-blue hustle-create-module"
				>
					<span class="sui-icon-plus" aria-hidden="true"></span> <?php esc_html_e( 'Create', 'hustle' ); ?>
				</button>

				<button
					class="sui-button hustle-import-module-button"
				>
					<span class="sui-icon-upload-cloud" aria-hidden="true"></span> <?php esc_html_e( 'Import', 'hustle' ); ?>
				</button>
			</p>

		<?php } ?>

	</div>

</div>

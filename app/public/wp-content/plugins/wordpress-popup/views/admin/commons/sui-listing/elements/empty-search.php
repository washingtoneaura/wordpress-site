<?php
/**
 * Displays the listing page view when there are not found modules.
 *
 * @package Hustle
 */

?>

<div class="sui-box sui-message sui-message-lg">

	<?php
	if ( ! $this->is_branding_hidden ) :
		$image_attrs = array(
			'path'        => self::$plugin_url . 'assets/images/hustle-empty-message.png',
			'retina_path' => self::$plugin_url . 'assets/images/hustle-empty-message@2x.png',
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

		<h2>
			<?php /* translators: search keyword */ ?>
			<?php printf( esc_html__( 'No results for "%s"', 'hustle' ), esc_html( $search_keyword ) ); ?>
		</h2>

		<?php /* translators: module type */ ?>
		<p><?php echo esc_html( sprintf( __( "We couldn't find any %s matching your search keyword. Perhaps try again?", 'hustle' ), $capitalize_plural ) ); ?></p>

	</div>

</div>

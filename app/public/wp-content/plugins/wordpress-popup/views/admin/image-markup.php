<?php
/**
 * Common image markup.
 *
 * @package Hustle
 */

// If image is not set return empty string.
if ( empty( $path ) ) {
	return '';
}
?>
<img src="<?php echo esc_url( $path ); ?>"
title="<?php /* translators: Plugin name */ echo esc_attr( sprintf( __( '%s image', 'hustle' ), Opt_In_Utils::get_plugin_name() ) ); ?>"
alt="<?php /* translators: Plugin name */ echo esc_attr( sprintf( __( '%1$s image commonly %1$s-Man doing something fun', 'hustle' ), Opt_In_Utils::get_plugin_name() ) ); ?>"

<?php if ( ! empty( $retina_path ) ) { ?>
	srcset="<?php echo esc_url( $path ) . ' 1x, ' . esc_url( $retina_path ) . ' 2x'; ?>"
<?php } ?>

<?php if ( ! empty( $class ) ) { ?>
	class="<?php echo esc_attr( $class ); ?>"
<?php } ?>

<?php
// Add styles to image.
if ( ! empty( $width ) || ! empty( $height ) ) {
	echo ' style="';

	if ( ! empty( $width ) ) {
		echo 'max-width: ' . esc_attr( 'auto' === $width ? 'auto' : intval( $width ) ) . 'px;';
	}

	if ( ! empty( $height ) ) {
		echo 'max-height: ' . intval( $height ) . 'px;';
	}

	echo '"';
}
?>

aria-hidden="true" />

<?php
/**
 * Close Button.
 *
 * @package Hustle
 * @since 4.3.0
 */

$component = 'button.hustle-button-close';

// SETTINGS: Colors.
$color_default    = $colors['close_button_static_color'];
$color_hover      = $colors['close_button_hover_color'];
$color_focus      = $colors['close_button_active_color'];
$color_background = $colors['close_button_static_background'];

// SETTINGS: Icon style.
$close_icon_size        = $design['close_icon_size'];
$close_icon_size_mobile = ( '' !== $design['close_icon_size_mobile'] && $is_mobile_enabled ) ? $design['close_icon_size_mobile'] : $design['close_icon_size'];
$icon_style             = $design['close_icon_style'];
$icon_style_mobile      = ( '' !== $design['close_icon_style_mobile'] && $is_mobile_enabled ) ? $design['close_icon_style_mobile'] : $design['close_icon_style'];

// SETTINGS: Position.
$position           = $design['close_icon_position'];
$position_mobile    = ( '' !== $design['close_icon_position_mobile'] && $is_mobile_enabled ) ? $design['close_icon_position_mobile'] : $design['close_icon_position'];
$alignment_x        = $design['close_icon_alignment_x'];
$alignment_x_mobile = ( '' !== $design['close_icon_alignment_x_mobile'] && $is_mobile_enabled ) ? $design['close_icon_alignment_x_mobile'] : $design['close_icon_alignment_x'];
$alignment_y        = $design['close_icon_alignment_y'];
$alignment_y_mobile = ( '' !== $design['close_icon_alignment_y_mobile'] && $is_mobile_enabled ) ? $design['close_icon_alignment_y_mobile'] : $design['close_icon_alignment_y'];

if ( ! $is_embed && ! $is_vanilla ) {

	$style .= '';

	$style     .= $component . ' .hustle-icon-close:before {';
		$style .= 'font-size: inherit;';
	$style     .= '}';

	// Mobile styles.
	$style     .= $prefix_mobile . $component . ' {';
		$style .= 'color: ' . $color_default . ';';
		$style .= ( ! empty( $color_background ) && 'flat' !== $icon_style_mobile ) ? 'background: ' . $color_background . ';' : 'background: transparent;';
		$style .= ( 'circle' === $icon_style_mobile ) ? 'border-radius: 100%;' : 'border-radius: 0;';
		$style .= 'position: absolute;';
		$style .= 'z-index: 1;';
		$style .= ( 'hidden' === $position_mobile ) ? 'display: none;' : 'display: block;';
		$style .= 'width: ' . ( $close_icon_size_mobile + 20 ) . 'px;';
		$style .= 'height: ' . ( $close_icon_size_mobile + 20 ) . 'px;';

	// Alignment x axis.
	if ( 'center' === $alignment_x_mobile ) {
		$style .= 'left: 50%;';
		$style .= 'right: auto;';
		$style .= 'transform: translateX(-50%);';
	} else {
		if ( 'outside' === $position_mobile && 'center' === $alignment_y_mobile ) {
			$style .= $alignment_x_mobile . ': -' . ( $close_icon_size_mobile + 20 ) . 'px;';
		} else {
			$style .= $alignment_x_mobile . ': 0;';
		}
		$style .= ( 'left' === $alignment_x_mobile ) ? 'right: auto;' : 'left: auto;';
	}

	// Alignment y axis.
	if ( 'center' === $alignment_y_mobile ) {
		$style .= 'top: 50%;';
		$style .= 'transform: ' . ( ( 'center' === $alignment_x_mobile ) ? 'translate(-50%, -50%);' : 'translateY(-50%);' );
	} else {
		$style .= $alignment_y_mobile . ': 0;';
		$style .= ( 'top' === $alignment_y_mobile ) ? 'bottom: auto;' : 'top: auto;';
	}

	if ( 'center' !== $alignment_x_mobile && 'center' !== $alignment_y_mobile ) {
		$style .= 'transform: unset;';
	}

	$style .= '}';

	$style     .= $prefix_mobile . $component . ' .hustle-icon-close {';
		$style .= 'font-size: ' . $close_icon_size_mobile . 'px;';
	$style     .= '}';

	$style     .= $prefix_mobile . $component . ':hover {';
		$style .= 'color: ' . $color_hover . ';';
	$style     .= '}';

	$style     .= $prefix_mobile . $component . ':focus {';
		$style .= 'color: ' . $color_focus . ';';
	$style     .= '}';

	// Desktop styles.
	if ( $is_mobile_enabled ) {
		$style .= $breakpoint . ' {';

			$style     .= $prefix_desktop . $component . ' .hustle-icon-close {';
				$style .= 'font-size: ' . $close_icon_size . 'px;';
			$style     .= '}';

			$style     .= $prefix_desktop . $component . ' {';
				$style .= ( ! empty( $color_background ) && 'flat' !== $icon_style ) ? 'background: ' . $color_background . ';' : 'background: transparent;';
				$style .= ( 'circle' === $icon_style ) ? 'border-radius: 100%;' : 'border-radius: 0;';
				$style .= ( 'hidden' === $position ) ? 'display: none;' : 'display: block;';
				$style .= 'width: ' . ( $close_icon_size + 20 ) . 'px;';
				$style .= 'height: ' . ( $close_icon_size + 20 ) . 'px;';

		// Alignment x axis.
		if ( 'center' === $alignment_x ) {
			$style .= 'left: 50%;';
			$style .= 'right: auto;';
			$style .= 'transform: translateX(-50%);';
		} else {
			if ( 'outside' === $position && 'center' === $alignment_y ) {
				$style .= $alignment_x . ': -' . ( $close_icon_size + 20 ) . 'px;';
			} else {
				$style .= $alignment_x . ': 0;';
			}
			$style .= ( 'left' === $alignment_x ) ? 'right: auto;' : 'left: auto;';
		}

		// Alignment y axis.
		if ( 'center' === $alignment_y ) {
			$style .= 'top: 50%;';
			$style .= 'transform: ' . ( ( 'center' === $alignment_x ) ? 'translate(-50%, -50%);' : 'translateY(-50%);' );
		} else {
			$style .= $alignment_y . ': 0;';
			$style .= ( 'top' === $alignment_y ) ? 'bottom: auto;' : 'top: auto;';
		}

		if ( 'center' !== $alignment_x && 'center' !== $alignment_y ) {
			$style .= 'transform: unset;';
		}

		$style .= '}';
		$style .= '}';
	}
}

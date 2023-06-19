<?php
/**
 * CTA Helper text.
 *
 * @package Hustle
 * @since 6.2.0
 */

$component = '.hustle-layout p.hustle-cta-helper-text';

// Component States.
$state_default = $component;

$font_family     = $typography['cta_help_font_family'];
$font_size       = $typography['cta_help_font_size'];
$font_size       = ( '' !== $font_size ) ? $font_size . $typography['cta_help_font_size_unit'] : '0';
$font_weight     = $typography['cta_help_font_weight'];
$font_style      = 'normal';
$alignment       = $typography['cta_help_alignment'];
$line_height     = $typography['cta_help_line_height'];
$line_height     = ( '' !== $line_height ) ? $line_height . $typography['cta_help_line_height_unit'] : '0';
$letter_spacing  = $typography['cta_help_letter_spacing'];
$letter_spacing  = ( '' !== $letter_spacing ) ? $letter_spacing . $typography['cta_help_letter_spacing_unit'] : '0';
$text_transform  = $typography['cta_help_text_transform'];
$text_decoration = $typography['cta_help_text_decoration'];

if ( 'custom' === $font_family ) {
	$font_family = ( '' !== $typography['cta_help_custom_font_family'] ) ? $typography['cta_help_custom_font_family'] : 'inherit';
}

if ( 'regular' === $font_weight ) {
	$font_weight = 'normal';
} elseif ( 'italic' === $font_weight ) {
	$font_weight = 'normal';
	$font_style  = 'italic';
} else {

	// Check if font weight is italic.
	if ( preg_match( '/(italic)/', $font_weight ) ) {
		$font_weight = str_replace( 'italic', '', $font_weight );
		$font_style  = 'italic';
	}
}

$mobile_font_size       = $typography['cta_help_font_size_mobile'];
$mobile_font_size       = ( '' !== $mobile_font_size ) ? $mobile_font_size . $typography['cta_help_font_size_unit_mobile'] : $font_size;
$mobile_font_weight     = $typography['cta_help_font_weight_mobile'];
$mobile_font_style      = 'normal';
$mobile_alignment       = $typography['cta_help_alignment_mobile'];
$mobile_line_height     = $typography['cta_help_line_height_mobile'];
$mobile_line_height     = ( '' !== $mobile_line_height ) ? $mobile_line_height . $typography['cta_help_line_height_unit_mobile'] : $line_height;
$mobile_letter_spacing  = $typography['cta_help_letter_spacing_mobile'];
$mobile_letter_spacing  = ( '' !== $mobile_letter_spacing ) ? $mobile_letter_spacing . $typography['cta_help_letter_spacing_unit_mobile'] : $letter_spacing;
$mobile_text_transform  = $typography['cta_help_text_transform'];
$mobile_text_decoration = $typography['cta_help_text_decoration_mobile'];

if ( 'regular' === $mobile_font_weight ) {
	$mobile_font_weight = 'normal';
} elseif ( 'italic' === $font_weight ) {
	$font_weight = 'normal';
	$font_style  = 'italic';
} else {

	// Check if font weight is italic.
	if ( preg_match( '/(italic)/', $mobile_font_weight ) ) {
		$mobile_font_weight = str_replace( 'italic', '', $mobile_font_weight );
		$mobile_font_style  = 'italic';
	}
}

if ( ! $is_mobile_enabled || ( $is_mobile_enabled && $default_typography ) ) {
	$mobile_font_size       = $font_size;
	$mobile_font_weight     = $font_weight;
	$mobile_alignment       = $alignment;
	$mobile_line_height     = $line_height;
	$mobile_letter_spacing  = $letter_spacing;
	$mobile_text_transform  = $text_transform;
	$mobile_text_decoration = $text_decoration;
}

// ==================================================
// Check if "Call to Action" button is enabled.
if ( '0' !== $content['show_cta'] && '1' === $content['cta_helper_show'] && $content['cta_helper_text'] ) {

	$style .= ' ';

	// Mobile styles.
	$style     .= $prefix_mobile . $state_default . ' {';
		$style .= ( 'inherit' !== $font_family ) ? 'font: ' . $mobile_font_weight . ' ' . $mobile_font_size . '/' . $mobile_line_height . ' ' . $font_family . ';' : '';
		$style .= ( 'inherit' === $font_family ) ? 'font-size: ' . $mobile_font_size . ';' : '';
		$style .= ( 'inherit' === $font_family ) ? 'line-height: ' . $mobile_line_height . ';' : '';
		$style .= ( 'inherit' === $font_family ) ? 'font-weight: ' . $mobile_font_weight . ';' : '';
		$style .= 'font-style: ' . $mobile_font_style . ';';
		$style .= 'letter-spacing: ' . $mobile_letter_spacing . ';';
		$style .= 'text-transform: ' . $mobile_text_transform . ';';
		$style .= 'text-decoration: ' . $mobile_text_decoration . ';';
		$style .= 'text-align: ' . $mobile_alignment . ';';
	$style     .= '}';

	// Desktop styles.
	if ( $is_mobile_enabled ) {

		$style         .= $breakpoint . ' {';
			$style     .= $prefix_desktop . $state_default . ' {';
				$style .= ( 'inherit' !== $font_family ) ? 'font: ' . $font_weight . ' ' . $font_size . '/' . $line_height . ' ' . $font_family . ';' : '';
				$style .= ( 'inherit' === $font_family ) ? 'font-size: ' . $font_size . ';' : '';
				$style .= ( 'inherit' === $font_family ) ? 'line-height: ' . $line_height . ';' : '';
				$style .= ( 'inherit' === $font_family ) ? 'font-weight: ' . $font_weight . ';' : '';
				$style .= 'font-style: ' . $font_style . ';';
				$style .= 'letter-spacing: ' . $letter_spacing . ';';
				$style .= 'text-transform: ' . $text_transform . ';';
				$style .= 'text-decoration: ' . $text_decoration . ';';
				$style .= 'text-align: ' . $alignment . ';';
			$style     .= '}';
		$style         .= '}';

	}
}

<?php
/**
 * CTA Button Alignment.
 *
 * @package Hustle
 * @since 4.3.0
 */

global $wp_locale;

$is_rtl = $wp_locale->is_rtl();

$container = '.hustle-layout .hustle-cta-container';
$component = $container . ' .hustle-button-cta';

// CONDITIONALS: Check if module has mobile appearance settings enabled.
$is_mobile_enabled  = ( '1' === $design['enable_mobile_settings'] );
$is_mobile_disabled = ( '1' !== $design['enable_mobile_settings'] );

// CONDITIONALS: Has button.
$has_cta = ( '0' !== $content['show_cta'] && ( '' !== $content['cta_label'] && '' !== $content['cta_url'] ) );

// SETTINGS: Alignment.
$alignment          = ( ! $is_rtl ) ? $design['cta_buttons_alignment'] : 'right';
$mobile_alignment   = ( $is_mobile_enabled ) ? $design['cta_buttons_alignment_mobile'] : $alignment;
$align_items        = ( 'left' === $alignment ) ? 'flex-start' : ( ( 'right' === $alignment ) ? 'flex-end' : 'center' );
$mobile_align_items = ( $is_mobile_enabled ) ? ( 'left' === $mobile_alignment ) ? 'flex-start' : ( ( 'right' === $mobile_alignment ) ? 'flex-end' : 'center' ) : $align_items;

// SETTINGS: Gap.
$gap_value        = ( '' !== $design['cta_buttons_layout_gap_value'] ) ? $design['cta_buttons_layout_gap_value'] : '0';
$mobile_gap_value = ( '' !== $design['cta_buttons_layout_gap_value_mobile'] ) ? $design['cta_buttons_layout_gap_value_mobile'] : '0';
$gap_unit         = ( '' !== $design['cta_buttons_layout_gap_unit'] ) ? $design['cta_buttons_layout_gap_unit'] : 'px';
$mobile_gap_unit  = ( '' !== $design['cta_buttons_layout_gap_unit_mobile'] ) ? $design['cta_buttons_layout_gap_unit_mobile'] : 'px';
$gap              = $gap_value . $gap_unit;
$mobile_gap       = ( $is_mobile_enabled ) ? $mobile_gap_value . $mobile_gap_unit : $gap;

// SETTINGS: Layout.
$layout        = ( '' !== $design['cta_buttons_layout_type'] ) ? $design['cta_buttons_layout_type'] : 'inline';
$mobile_layout = ( $is_mobile_enabled ) ? $design['cta_buttons_layout_type_mobile'] : $layout;

// ==================================================
// Check if call to action button exists.
if ( $has_cta ) {

	$style .= ' ';

	// Mobile styles.
	if ( 'full' === $mobile_alignment ) {
		$style     .= $prefix_mobile . $container . ' {';
			$style .= 'display: flex;';
			$style .= ( 'stacked' === $mobile_layout ) ? 'flex-direction: column;' : '';
		$style     .= '}';
		$style     .= $prefix_mobile . $component . ' {';
			$style .= 'width: 100%;';
			$style .= 'display: block;';
		$style     .= '}';

		if ( '2' === $content['show_cta'] ) {
			$style     .= $prefix_mobile . $component . ':last-child {';
				$style .= ( 'inline' !== $mobile_layout ) ? 'margin:' . $mobile_gap . ' 0 0 0;' : 'margin: 0 0 0 ' . $mobile_gap . ';';
			$style     .= '}';
		}
	} else {
		$style     .= $prefix_mobile . $container . ' {';
			$style .= ( 'inline' === $mobile_layout ) ? 'justify-content: ' . $mobile_alignment . ';' : 'align-items: ' . $mobile_align_items . ';';
			$style .= ( 'stacked' === $mobile_layout ) ? 'flex-direction: column;' : '';
			$style .= 'display: flex;';
		$style     .= '}';
		$style     .= $prefix_mobile . $component . ' {';
			$style .= 'width: auto;';
			$style .= 'display: inline-block;';
		$style     .= '}';

		if ( '2' === $content['show_cta'] ) {
			$style     .= $prefix_mobile . $component . ':last-child {';
				$style .= ( 'inline' === $mobile_layout ) ? 'margin: 0 0 0 ' . $mobile_gap . ';' : 'margin: ' . $mobile_gap . ' 0 0 0;';
			$style     .= '}';
		}
	}

	// Desktop styles.
	if ( $is_mobile_enabled ) {

		if ( 'full' === $alignment ) {
			$style     .= $breakpoint . ' {';
				$style .= $prefix_desktop . $container . ' {';
				$style .= 'display: flex;';
				$style .= ( 'stacked' === $layout ) ? 'flex-direction: column;' : 'flex-direction: unset;';
			$style     .= '}';
			$style     .= '}';

			$style         .= $breakpoint . ' {';
				$style     .= $prefix_desktop . $component . ' {';
					$style .= 'width: 100%;';
					$style .= 'display: block;';
				$style     .= '}';
			$style         .= '}';

			if ( '2' === $content['show_cta'] ) {
				$style         .= $breakpoint . ' {';
					$style     .= $prefix_mobile . $component . ':last-child {';
						$style .= ( 'inline' !== $layout ) ? 'margin:' . $gap . ' 0 0 0;' : 'margin: 0 0 0 ' . $gap . ';';
					$style     .= '}';
				$style         .= '}';
			}
		} else {
			$style         .= $breakpoint . ' {';
				$style     .= $prefix_desktop . $container . ' {';
					$style .= ( 'inline' === $layout ) ? 'justify-content: ' . $alignment . ';' : 'align-items: ' . $align_items . ';';
					$style .= 'display: flex;';
					$style .= ( 'stacked' === $layout ) ? 'flex-direction: column;' : 'flex-direction: unset;';
				$style     .= '}';
				$style     .= $prefix_desktop . $component . ' {';
					$style .= 'width: auto;';
					$style .= 'display: inline-block;';
				$style     .= '}';
			$style         .= '}';

			if ( '2' === $content['show_cta'] ) {
				$style         .= $breakpoint . ' {';
					$style     .= $prefix_mobile . $component . ':last-child {';
						$style .= ( 'inline' === $layout ) ? 'margin: 0 0 0 ' . $gap . ';' : 'margin: ' . $gap . ' 0 0 0;';
					$style     .= '}';
				$style         .= '}';
			}
		}
	}
}

<?php
/**
 * Typography row.
 *
 * @uses ./typography/options-container
 * @uses admin/global/sui-components/sui-tabs
 * @uses admin/global/sui-components/sui-settings-row
 *
 * @package Hustle
 * @since 4.3.0
 */

$device_suffix = empty( $device ) ? '' : '_' . $device;

$options_args = array(
	'settings'            => $settings,
	'is_optin'            => $is_optin,
	'device'              => $device,
	'smallcaps_singular'  => $smallcaps_singular,
	'capitalize_singular' => $capitalize_singular,
);

$options_container = $this->render(
	'admin/commons/sui-wizard/tab-appearance/row-typography/options-container',
	$options_args,
	true
);

// Tabs wrapper.
$content = $this->render(
	'admin/global/sui-components/sui-tabs',
	array(
		'name'        => 'customize_typography' . $device_suffix,
		'saved_value' => $settings[ 'customize_typography' . $device_suffix ],
		'radio'       => true,
		'options'     => array(
			'default' => array(
				'value' => '0',
				'label' => esc_html__( 'Default', 'hustle' ),
			),
			'custom'  => array(
				'value'   => '1',
				'label'   => esc_html__( 'Custom', 'hustle' ),
				'content' => $options_container,
			),
		),
		'sidetabs'    => true,
		'content'     => true,
	),
	true
);

// Main wrapper.
$this->render(
	'admin/global/sui-components/sui-settings-row',
	array(
		'label'        => __( 'Typography', 'hustle' ),
		'vanilla_hide' => true,
		'description'  => sprintf(
			/* translators: 1. module type in lowercase and singular. 2. Open a tag. 3. Close a tag. */
			esc_html__( 'Your %1$s has default font styles. However, you can switch to Custom and use any GDPR compliant %2$sBunny Font%3$s that you prefer, and also customize the font styles.', 'hustle' ),
			esc_html( $smallcaps_singular ),
			'<a href="https://fonts.bunny.net/about" target="_blank">',
			'</a>'
		),
		'content'      => $content,
	)
);

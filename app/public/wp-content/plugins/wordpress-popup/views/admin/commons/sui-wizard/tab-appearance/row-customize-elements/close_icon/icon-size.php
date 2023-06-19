<?php
/**
 * Icon style row
 *
 * @package Hustle
 */

// TODO: USE THE SUI COMPONENT.

$position = $settings[ 'close_icon_position' . ( $device ? '_' . $device : '' ) ];

$name     = 'close_icon_size' . ( $device ? '_' . $device : '' );
$settings = $settings[ $name ];

$units = array(
	'12' => '12 x 12',
	'16' => '16 x 16',
	'24' => '24 x 24',
	'32' => '32 x 32',
	'40' => '40 x 40',
	'48' => '48 x 48',
	'56' => '56 x 56',
	'64' => '64 x 64',
	'72' => '72 x 72',
	'80' => '80 x 80',
);

?>

<div class="sui-box-settings-row hustle-close_icon_size" style="<?php echo ( 'hidden' === $position ) ? 'display: none;' : ''; ?>">

	<div class="sui-box-settings-col-2">

		<h5 class="sui-settings-label sui-dark" style="font-size: 13px;"><?php esc_html_e( 'Icon Size', 'hustle' ); ?></h5>

		<p class="sui-description"><?php esc_html_e( 'Choose an icon size for the close icon.', 'hustle' ); ?></p>

		<div class="sui-form-field">

			<label id="hustle-<?php echo esc_attr( $name ); ?>-label" class="sui-label">
				<?php esc_html_e( 'Size (width x height)', 'hustle' ); ?>
				<span class="sui-label-note">px</span>
			</label>
			<?php
			Hustle_Layout_Helper::get_html_for_options(
				array(
					array(
						'type'       => 'select',
						'name'       => $name,
						'options'    => $units,
						'class'      => 'sui-select',
						'id'         => 'hustle-' . $name,
						'selected'   => $settings,
						'attributes' => array(
							'data-attribute'  => $name,
							'aria-labelledby' => 'hustle-' . $name . '-label',
						),
					),
				)
			);
			?>
		</div>

	</div>

</div>

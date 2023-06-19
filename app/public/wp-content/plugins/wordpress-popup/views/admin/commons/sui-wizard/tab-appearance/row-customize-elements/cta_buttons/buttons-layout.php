<?php
/**
 * Buttons Layout settings
 *
 * @package Hustle
 */

$device_suffix = $device ? '_' . $device : '';

$layout_type      = 'cta_buttons_layout_type' . $device_suffix;
$layout_gap_value = 'cta_buttons_layout_gap_value' . $device_suffix;
$layout_gap_unit  = 'cta_buttons_layout_gap_unit' . $device_suffix;
?>

<div class="sui-box-settings-row hustle-cta_button_layout" style="<?php echo ( '2' !== $show_cta ) ? 'display: none;' : ''; ?>">

	<div class="sui-box-settings-col-2">

		<h5 class="sui-settings-label sui-dark" style="font-size: 13px;"><?php esc_html_e( 'Buttons Layout', 'hustle' ); ?></h5>

		<p class="sui-description"><?php esc_html_e( 'Choose the layout of CTA buttons and the gap between them.', 'hustle' ); ?></p>

		<div class="hui-fields-row">

			<?php // COL: Layout. ?>
			<div class="hui-fields-col" data-field-size="90">

				<div class="sui-form-field">

					<label class="sui-label"><?php esc_html_e( 'Layout', 'hustle' ); ?></label>

					<div class="sui-tabs sui-side-tabs">

						<input
							type="radio"
							name="<?php echo esc_attr( $layout_type ); ?>"
							value="inline"
							id="hustle-<?php echo esc_attr( $layout_type ); ?>--inline"
							class="sui-screen-reader-text hustle-tabs-option"
							data-attribute="<?php echo esc_attr( $layout_type ); ?>"
							aria-hidden="true"
							tabindex="-1"
							<?php checked( $settings[ $layout_type ], 'inline' ); ?>
						/>

						<input
							type="radio"
							name="<?php echo esc_attr( $layout_type ); ?>"
							value="stacked"
							id="hustle-<?php echo esc_attr( $layout_type ); ?>--stacked"
							class="sui-screen-reader-text hustle-tabs-option"
							data-attribute="<?php echo esc_attr( $layout_type ); ?>"
							aria-hidden="true"
							tabindex="-1"
							<?php checked( $settings[ $layout_type ], 'stacked' ); ?>
						/>

						<div role="tablist" class="sui-tabs-menu">

							<button
								type="button"
								role="tab"
								id="tab-<?php echo esc_attr( $layout_type ); ?>--inline"
								class="sui-tab-item active"
								data-label-for="hustle-<?php echo esc_attr( $layout_type ); ?>--inline"
								aria-selected="true"
							>
								<span class="hui-tab-icon-position-inline" aria-hidden="true"></span>
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Inline', 'hustle' ); ?></span>
							</button>

							<button
								type="button"
								role="tab"
								id="tab-<?php echo esc_attr( $layout_type ); ?>--stacked"
								class="sui-tab-item"
								aria-selected="false"
								data-label-for="hustle-<?php echo esc_attr( $layout_type ); ?>--stacked"
								tabindex="-1"
							>
								<span class="hui-tab-icon-position-stacked" aria-hidden="true"></span>
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Stacked', 'hustle' ); ?></span>
							</button>

						</div>

					</div>

				</div>

			</div>

			<?php // COL: Gap. ?>
			<div class="hui-fields-col" data-field-size="100">

				<div class="sui-form-field">

					<?php
					$this->render(
						'admin/global/components/select-units',
						array(
							'label'    => esc_html__( 'Gap', 'hustle' ),
							'name'     => $layout_gap_unit,
							'selected' => $settings[ $layout_gap_unit ],
						)
					);
					?>

					<?php
					Hustle_Layout_Helper::get_html_for_options(
						array(
							array(
								'type'       => 'number',
								'name'       => $layout_gap_value,
								'min'        => '0',
								'value'      => $settings[ $layout_gap_value ],
								'id'         => 'hustle-' . $layout_gap_value,
								'attributes' => array(
									'data-attribute' => $layout_gap_value,
									'aria-label'     => esc_html__( 'Insert buttons layout gap value', 'hustle' ),
								),
							),
						)
					);
					?>

				</div>

			</div>

		</div>

	</div>

</div>

<?php
/**
 * CTA section.
 *
 * @package Hustle
 * @since 4.0.0
 */

?>

<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Call to Action', 'hustle' ); ?></span>
		<?php /* translators: module type in small caps and in singular */ ?>
		<span class="sui-description"><?php printf( esc_html__( 'Add a call to action button on your %s, set an action for them, and optionally add a helper text below them.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-form-field">

			<?php
			$one_button = array(
				array(
					'key'     => 'button_one',
					'title'   => esc_html__( 'Button 1', 'hustle' ),
					'content' => $this->render(
						'admin/commons/sui-wizard/tab-content/cta-tab-option',
						array(
							'label'       => array(
								'key'     => 'cta_label',
								'content' => $settings['cta_label'],
							),
							'action'      => array(
								'key'     => 'cta_target',
								'content' => $settings['cta_target'],
							),
							'url'         => array(
								'key'     => 'cta_url',
								'content' => $settings['cta_url'],
							),
							'whole_cta'   => empty( $settings['cta_whole'] ) ? '' : $settings['cta_whole'],
							'module_name' => $smallcaps_singular,
						),
						true
					),
				),
			);

			$two_buttons = array(
				array(
					'key'     => 'button_one',
					'title'   => esc_html__( 'Button 1', 'hustle' ),
					'content' => $this->render(
						'admin/commons/sui-wizard/tab-content/cta-tab-option',
						array(
							'label'       => array(
								'key'     => 'cta_label',
								'content' => $settings['cta_label'],
							),
							'action'      => array(
								'key'     => 'cta_target',
								'content' => $settings['cta_target'],
							),
							'url'         => array(
								'key'     => 'cta_url',
								'content' => $settings['cta_url'],
							),
							'module_name' => $smallcaps_singular,
						),
						true
					),
				),
				array(
					'key'     => 'button_two',
					'title'   => esc_html__( 'Button 2', 'hustle' ),
					'content' => $this->render(
						'admin/commons/sui-wizard/tab-content/cta-tab-option',
						array(
							'label'       => array(
								'key'     => 'cta_two_label',
								'content' => $settings['cta_two_label'],
							),
							'action'      => array(
								'key'     => 'cta_two_target',
								'content' => $settings['cta_two_target'],
							),
							'url'         => array(
								'key'     => 'cta_two_url',
								'content' => $settings['cta_two_url'],
							),
							'module_name' => $smallcaps_singular,
						),
						true
					),
				),
			);

			$this->render(
				'admin/global/sui-components/sui-tabs',
				array(
					'name'        => 'show_cta',
					'saved_value' => $settings['show_cta'],
					'radio'       => true,
					'sidetabs'    => true,
					'content'     => true,
					'options'     => array(
						'one'  => array(
							'value'   => '1',
							'label'   => esc_html__( '1 Button', 'hustle' ),
							'content' => $this->render(
								'admin/global/sui-components/sui-accordion',
								array(
									'options' => $one_button,
									'flushed' => false,
									'reset'   => false,
								),
								true
							),
						),
						'two'  => array(
							'value'   => '2',
							'label'   => esc_html__( '2 Buttons', 'hustle' ),
							'content' => $this->render(
								'admin/global/sui-components/sui-accordion',
								array(
									'options' => $two_buttons,
									'flushed' => false,
									'reset'   => false,
								),
								true
							),
						),
						'none' => array(
							'value' => '0',
							'label' => esc_html__( 'None', 'hustle' ),
						),
					),
				)
			);
			?>

		</div>
		<div id="hustle-cta-helper-text">

			<label for="hustle-integrations-allow-subscribed-users" class="sui-settings-label"><?php esc_html_e( 'Helper text', 'hustle' ); ?></label>

			<span class="sui-description" style="margin-bottom: 10px;"><?php esc_html_e( 'Helper text is placed under your call to action to boost its conversion. Choose whether you want to enable the helper text or not.', 'hustle' ); ?></span>

			<?php
				ob_start();
			?>
				<div class="sui-form-field">
					<label class="sui-label"><?php esc_html_e( 'Helper text', 'hustle' ); ?></label>
					<input type="text"
						name="cta_helper_text"
						data-attribute="cta_helper_text"
						placeholder="<?php esc_html_e( 'Eg. No credit card required', 'hustle' ); ?>"
						value="<?php echo esc_attr( $settings['cta_helper_text'] ); ?>"
						class="sui-form-control" />
				</div>
			<?php
			$helper_text = ob_get_clean();

			$options = array(
				'1' => array(
					'value'   => '1',
					'label'   => __( 'Enable', 'hustle' ),
					'boxed'   => true,
					'content' => $helper_text,
				),
				'0' => array(
					'value' => '0',
					'label' => __( 'Disable', 'hustle' ),
				),
			);

			$this->render(
				'admin/global/sui-components/sui-tabs',
				array(
					'name'        => 'cta_helper_show',
					'radio'       => true,
					'saved_value' => $settings['cta_helper_show'],
					'sidetabs'    => true,
					'content'     => true,
					'options'     => $options,
				)
			);
			?>
		</div>

	</div>

</div>

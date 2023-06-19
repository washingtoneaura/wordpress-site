<?php
/**
 * SUI Tab Options.
 *
 * @package Hustle
 * @since 4.6.0
 */

?>

<div class="sui-box">
	<div class="sui-box-body" id="hustle_<?php echo esc_attr( $action['key'] ); ?>">
		<div class="sui-row">
			<div class="sui-col-md-4">
				<div class="sui-form-field">
					<label class="sui-label"><?php echo esc_html__( 'Button Label', 'hustle' ); ?></label>
					<input type="text"
						name="<?php echo esc_attr( $label['key'] ); ?>"
						placeholder="<?php esc_html_e( 'Label', 'hustle' ); ?>"
						value="<?php echo esc_attr( $label['content'] ); ?>"
						id="hustle_module_<?php echo esc_attr( $label['key'] ); ?>"
						class="sui-form-control"
						data-attribute="<?php echo esc_attr( $label['key'] ); ?>" />
				</div>
			</div>
			<div class="sui-col-md-8">
				<div class="sui-form-field">
					<label class="sui-label"><?php echo esc_html__( 'Action', 'hustle' ); ?></label>
					<select class="sui-select" name="<?php echo esc_attr( $action['key'] ); ?>" data-search="false" data-attribute="<?php echo esc_attr( $action['key'] ); ?>">
						<option value="blank"
							<?php selected( $action['content'], 'blank' ); ?>>
							<?php esc_attr_e( 'Redirect-New window', 'hustle' ); ?>
						</option>
						<option value="self"
							<?php selected( $action['content'], 'self' ); ?>>
							<?php esc_attr_e( 'Redirect-Same window', 'hustle' ); ?>
						</option>
						<?php if ( 'embed' !== $module_name ) { ?>
							<option value="close"
								<?php selected( $action['content'], 'close' ); ?>>
								<?php
									'slide-in' === $module_name ?
										esc_attr_e( 'Close Slide-in', 'hustle' ) : esc_attr_e( 'Close Pop-up', 'hustle' );
								?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="sui-form-field hustle-url-field <?php echo 'close' === $action['content'] ? 'sui-hidden' : ''; ?>">
			<label for="hustle-cta-url" class="sui-label"><?php esc_html_e( 'Redirect URL', 'hustle' ); ?></label>
			<input type="url"
				name="cta_url"
				value="<?php echo esc_attr( $url['content'] ); ?>"
				placeholder="<?php esc_attr_e( 'E.g. https://website.com', 'hustle' ); ?>"
				id="hustle-<?php echo esc_attr( $url['key'] ); ?>"
				class="sui-form-control"
				data-attribute="<?php echo esc_attr( $url['key'] ); ?>" />
			<span class="sui-error" style="display: none;"><?php esc_html_e( "That's not a valid URL. Please, try again.", 'hustle' ); ?></span>
		</div>
		<div class="sui-form-field <?php echo ! isset( $whole_cta ) ? 'sui-hidden' : ''; ?>">
			<label for="hustle-whole-cta" class="sui-toggle">
				<input type="checkbox"
					name="cta_whole"
					data-attribute="cta_whole"
					id="hustle-whole-cta"
					aria-labelledby="hustle-whole-cta-label"
					aria-describedby="hustle-whole-cta-description"
					<?php isset( $whole_cta ) && checked( $whole_cta, '1' ); ?>
					/>
				<span class="sui-toggle-slider" aria-hidden="true"></span>

				<span id="hustle-whole-cta-label" class="sui-toggle-label"><?php esc_html_e( 'Make whole module a clickable CTA', 'hustle' ); ?></span>
				<span id="hustle-whole-cta-description" class="sui-description"><?php esc_html_e( 'With this option enabled, your visitors can click anywhere in the module to trigger your call to action.', 'hustle' ); ?></span>
			</label>
		</div>
	</div>
</div>

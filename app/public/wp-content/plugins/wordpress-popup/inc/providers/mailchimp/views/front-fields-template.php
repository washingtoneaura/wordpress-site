<?php
/**
 * Front field template
 *
 * @package Hustle
 */

global $wp_locale;

$is_rtl = $wp_locale->is_rtl();

$display_none = 'hidden' === $group_type ? ' style="display:none;"' : '';
?>
<div class="hustle-form-options"<?php echo esc_html( $display_none ); ?>>

	<input
		type="hidden"
		name="mailchimp_group_id"
		class="mailchimp_group_id"
		value="<?php echo esc_attr( $group_id ); ?>"
	/>

	<?php if ( 'hidden' !== $group_type ) : ?>

		<span class="hustle-group-title"><?php echo esc_html( $group_name ); ?></span>

		<?php if ( 'checkboxes' === $group_type ) { ?>

			<?php foreach ( $interest_options as $option_id => $option_name ) { ?>

				<label
					for="hustle-module-<?php echo esc_attr( $module_id ); ?>-checkbox-option-<?php echo esc_attr( $option_id ); ?>"
					class="hustle-checkbox hustle-checkbox-inline"
				>

					<input
						type="checkbox"
						name="mailchimp_group_interest[]"
						value="<?php echo esc_attr( $option_id ); ?>"
						id="hustle-module-<?php echo esc_attr( $module_id ); ?>-checkbox-option-<?php echo esc_attr( $option_id ); ?>"
						<?php checked( in_array( $option_id, $selected_interest, true ) ); ?>
					/>

					<span aria-hidden="true"></span>

					<span><?php echo esc_attr( $option_name ); ?></span>

				</label>

			<?php } ?>

		<?php } elseif ( 'radio' === $group_type ) { ?>

			<?php foreach ( $interest_options as $option_id => $option_name ) { ?>

				<label
					for="hustle-module-<?php echo esc_attr( $module_id ); ?>-radio-option-<?php echo esc_attr( $option_id ); ?>"
					class="hustle-radio hustle-radio-inline"
				>

					<input
						type="radio"
						name="mailchimp_group_interest[]"
						value="<?php echo esc_attr( $option_id ); ?>"
						id="hustle-module-<?php echo esc_attr( $module_id ); ?>-radio-option-<?php echo esc_attr( $option_id ); ?>"
						<?php checked( $option_id, $selected_interest ); ?>
					/>

					<span aria-hidden="true"></span>

					<span><?php echo esc_attr( $option_name ); ?></span>

				</label>

			<?php } ?>

		<?php } elseif ( 'dropdown' === $group_type ) { ?>

			<select
				name="mailchimp_group_interest"
				class="hustle-select2"
				data-rtl-support=<?php echo ( ! $is_rtl ) ? 'false' : 'true'; ?>
				data-language="en"
				data-placeholder="<?php echo esc_attr( $dropdown_placeholder ); ?>"
			>

				<?php
				/**
				 * Placeholder
				 * If no option is pre-selected a placeholder will be shown
				 *
				 * @since 4.0.3
				 */
				?>
				<option value=""></option>

				<?php foreach ( $interest_options as $option_id => $option_name ) { ?>

					<option
						value="<?php echo esc_attr( $option_id ); ?>"
						id="hustle-module-<?php echo esc_attr( $module_id ); ?>-dropdown-option-<?php echo esc_attr( $option_id ); ?>"
						<?php selected( $selected_interest, $option_id ); ?>
					>
						<?php echo esc_attr( $option_name ); ?>
					</option>

				<?php } ?>

			</select>

		<?php } ?>

	<?php else : ?>

		<?php if ( isset( $interest_options[ $selected_interest ] ) ) : ?>

			<input
				type="hidden"
				name="mailchimp_group_interest"
				value="<?php echo esc_attr( $selected_interest ); ?>"
			/>

		<?php endif; ?>

	<?php endif; ?>

</div>

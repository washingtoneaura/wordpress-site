<?php
/**
 * CTA button settings
 *
 * @package Hustle
 */

?>
<div class="sui-box">

	<div class="sui-box-body">

		<?php
		// ROW: Buttons Layout.
		$this->render(
			'admin/commons/sui-wizard/tab-appearance/row-customize-elements/cta_buttons/buttons-layout',
			array(
				'key'                => $key,
				'settings'           => $settings,
				'is_optin'           => $is_optin,
				'device'             => $device,
				'smallcaps_singular' => $smallcaps_singular,
				'show_cta'           => $show_cta,
			)
		);

		// ROW: Alignment.
		$this->render(
			'admin/commons/sui-wizard/tab-appearance/row-customize-elements/cta_buttons/alignment',
			array(
				'key'                => $key,
				'settings'           => $settings,
				'is_optin'           => $is_optin,
				'device'             => $device,
				'smallcaps_singular' => $smallcaps_singular,
			)
		);
		?>

	</div>

</div>

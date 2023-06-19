<?php
/**
 * SUI Box Header.
 *
 * @package Hustle
 * @since 4.3.0
 */

?>

<div class="sui-box-header">

	<?php
	echo '<h2 class="sui-box-title">';

	if ( ! empty( $icon ) ) {
		echo '<span class="sui-icon-' . esc_attr( $icon ) . ' sui-lg" aria-hidden="true"></span>';
	}

	echo esc_html( $title ) . '</h2>';

	if ( isset( $pro_tag ) && true === $pro_tag ) {
		echo '<span class="sui-tag sui-tag-pro" style="margin-left: 10px" aria-hidden="true">Pro</span>';
	}
	?>

</div>

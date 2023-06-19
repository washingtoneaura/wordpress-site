<?php
/**
 * List of the providers that are globally connected.
 *
 * @package Hustle
 * @since 4.0.0
 */

?>
<?php if ( 0 === count( $providers ) ) : ?>

	<div class="sui-notice sui-notice-error">

		<div class="sui-notice-content">

			<div class="sui-notice-message">

				<span class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></span>
				<p><?php /* translators: Plugin name */ echo esc_html( sprintf( __( "You need at least one active app to send your opt-in's submissions to. If you don't want to use any third-party app, you can always use the Local %s List to save the submissions.", 'hustle' ), Opt_In_Utils::get_plugin_name() ) ); ?></p>

			</div>
		</div>
	</div>

<?php else : ?>

	<table class="sui-table hui-table--apps">

		<tbody>

			<?php foreach ( $providers as $provider ) : ?>

				<?php
				$this->render(
					'admin/integrations/integration-row',
					array(
						'provider' => $provider,
					)
				);
				?>

			<?php endforeach; ?>

		</tbody>

	</table>
<?php endif; ?>

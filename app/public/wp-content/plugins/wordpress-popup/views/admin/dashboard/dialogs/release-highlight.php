<?php
/**
 * Welcome dialog for fresh installs.
 *
 * @package Hustle
 * @since 4.0.0
 */

$user     = wp_get_current_user();
$username = ! empty( $user->user_firstname ) ? $user->user_firstname : $user->user_login;
$url      = 'https://wpmudev.com/docs/wpmu-dev-plugins/hustle/#integrations';
?>

<div class="sui-modal sui-modal-md">

	<div
		role="dialog"
		id="hustle-dialog--release-highlight"
		class="sui-modal-content"
		aria-modal="true"
		aria-labelledby="hustle-dialog--release-highlight-title"
		aria-describedby="hustle-dialog--release-highlight-description"
		data-name="<?php echo esc_attr( Hustle_Dashboard_Admin::HIGHLIGHT_MODAL_NAME ); ?>"
	>

		<div class="sui-box" style="margin-bottom: 10px;">

			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-right--30  sui-spacing-left--30">

				<button class="sui-button-icon sui-button-float--right hustle-modal-close" style="z-index: 2;" data-modal-close>
					<span class="sui-icon-close sui-md" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

				<figure role="banner" class="sui-box-banner" aria-hidden="true">
					<?php
					$image_attrs = array(
						'path'        => self::$plugin_url . 'assets/images/release-highlight-header.png',
						'retina_path' => self::$plugin_url . 'assets/images/release-highlight-header@2x.png',
						'class'       => 'sui-image sui-image-center',
					);

					$this->render( 'admin/image-markup', $image_attrs );
					?>
				</figure>

				<h3 id="hustle-dialog--release-highlight-title" class="sui-box-title sui-lg"><?php esc_html_e( 'Accessibility and Visibility Improvements', 'hustle' ); ?></h3>

				<p id="hustle-dialog--release-highlight-description" class="sui-description">
					<?php /* translators: 1. open link 2. close link 3. Plugin name */ ?>
					<?php printf( esc_html__( 'Hey %1$s, Know what? %2$s is now accessible like never before! You can now navigate %2$s modules with screen readers as well as the tab key, making it more accessible and inclusive', 'hustle' ), esc_html( $username ), esc_html( Opt_In_Utils::get_plugin_name() ) ); ?>
				</p>

				<?php /* translators: 1. open 'b' tag  2. close 'b' tag */ ?>
				<p class="sui-description"><?php printf( esc_html__( 'Not just that! You can now schedule your Hustle modules (slide-ins and popups) to display a specified number of times in a %1$sday/week/month/year%2$s. Target your audience with precision, without overwhelming them with too many popups', 'hustle' ), '<b>', '</b>' ); ?></p>


			</div>

			<div class="sui-box-footer sui-flatten sui-content-center sui-spacing-bottom--50">

				<button id="hustle-release-highlight-action-button" class="sui-button" data-modal-close>
					<?php esc_html_e( 'Got It', 'hustle' ); ?>
				</button>

			</div>

		</div>

		<button class="sui-modal-skip" data-modal-close><?php esc_html_e( "I'll check this later", 'hustle' ); ?></button>

	</div>

</div>

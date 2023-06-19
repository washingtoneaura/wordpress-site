<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_Local_List_Form_Hooks class
 *
 * @package Hustle
 */

/**
 * Class Hustle_Local_List_Form_Hooks
 * Define the form hooks that are used by Local List
 *
 * @since 4.0
 */
class Hustle_Local_List_Form_Hooks extends Hustle_Provider_Form_Hooks_Abstract {

	/**
	 * Check whether the email is already subscribed.
	 *
	 * @since 4.0
	 *
	 * @param array $submitted_data Submitted data.
	 * @param bool  $allow_subscribed Allow already subscribed.
	 * @return bool
	 */
	public function on_form_submit( $submitted_data, $allow_subscribed = true ) {
		$is_success = true;

		$module_id              = $this->module_id;
		$form_settings_instance = $this->form_settings_instance;

		if ( ! $allow_subscribed ) {

			/**
			 * Filter submitted form data to be processed
			 *
			 * @since 4.0
			 *
			 * @param array                                    $submitted_data
			 * @param int                                      $module_id                current module_id
			 * @param Hustle_Local_List_Form_Settings $form_settings_instance
			 */
			$submitted_data = apply_filters(
				'hustle_provider_local_list_form_submitted_data_before_validation',
				$submitted_data,
				$module_id,
				$form_settings_instance
			);

			$is_subscribed = Hustle_Entry_Model::is_email_subscribed_to_module_id( $module_id, $submitted_data['email'] );

			// Subscribe only if the email wasn't subscribed already.
			if ( $is_subscribed ) {
				$is_success = self::ALREADY_SUBSCRIBED_ERROR;
			}
		}

		/**
		 * Return `true` if success, or **(string) error message** on fail
		 *
		 * @since 4.0
		 *
		 * @param bool                                     $is_success
		 * @param int                                      $module_id                current module_id
		 * @param array                                    $submitted_data
		 * @param Hustle_Local_List_Form_Settings $form_settings_instance
		 */
		$is_success = apply_filters(
			'hustle_provider_local_list_form_submitted_data_after_validation',
			$is_success,
			$module_id,
			$submitted_data,
			$form_settings_instance
		);

		// process filter.
		if ( true !== $is_success ) {
			// only update `submit_form_error_message` when not empty.
			if ( ! empty( $is_success ) ) {
				$this->submit_form_error_message = (string) $is_success;
			}

			return $is_success;
		}

		return true;
	}

	/**
	 * We're adding the local list's entries in the front-ajax file because
	 * we need all integrations' hook to run first in order to add their data
	 * to entries. Move that behavior to this file if we want to do it here instead,
	 * as it should be.
	 * Hustle_Module_Modal::add_local_subscription() doesn't exist anymore.
	 * We're handling entries with hustle_Entry_Model class.
	 */

}

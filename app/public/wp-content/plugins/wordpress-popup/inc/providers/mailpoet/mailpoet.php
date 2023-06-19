<?php
/**
 * Main file for MailPoet
 *
 * @package Hustle
 */

/**
 * Direct Load
 */
require_once dirname( __FILE__ ) . '/class-hustle-mailpoet.php';
require_once dirname( __FILE__ ) . '/class-hustle-mailpoet-form-settings.php';
require_once dirname( __FILE__ ) . '/class-hustle-mailpoet-form-hooks.php';
Hustle_Providers::get_instance()->register( 'Hustle_Mailpoet' );

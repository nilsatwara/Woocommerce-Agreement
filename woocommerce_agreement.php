<?php

/**
 * Plugin Name:  Woocommerce Agreement
 * Plugin URI: http://www.worldwebtechnology.com/
 * Description:  Introducing WooCommerce Agreement, the smart solution for seamless agreements in your WooCommerce store. After checkout, customers get an instant download link for personalized PDF agreements. Enhance trust, ensure compliance, and simplify your processes with WooCommerce Agreement. Upgrade your WooCommerce store today and revolutionize your agreement experience.
 * 
 * Version: 1.0.0
 * Author: World Web Technology
 * Author URI: http://www.worldwebtechnology.com
 * Text Domain: w_agree
 * Domain Path: languages
 * 
 * @package Woocommerce Agreement
 * @category Core
 * @author WPWeb
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Create constant for use in whole plugin 
 * 
 * @package Woocommerce Agreement
 * @since 1.0.0
 */
if (!defined('W_AGREE_VERSION')) {
	define('W_AGREE_VERSION', '1.0.0'); //version of plugin
}
if (!defined('W_AGREE_DIR')) {
	define('W_AGREE_DIR', dirname(__FILE__)); // plugin dir
}
if (!defined('W_AGREE_ADMIN_DIR')) {
	define('W_AGREE_ADMIN_DIR', W_AGREE_DIR . '/includes/admin');  // Plugin admin dir
}
if (!defined('W_AGREE_PLUGIN_URL')) {
	define('W_AGREE_PLUGIN_URL', plugin_dir_url(__FILE__)); // plugin url



}
if (!defined('W_AGREE_DIR_META_PREFIX')) {
	define('W_AGREE_META_PREFIX', '_w_agree_'); // Option and Post Meta prefix
}
if (!defined('W_AGREE_PLUGIN_BASENAME')) {
	define('W_AGREE_PLUGIN_BASENAME', basename(W_AGREE_DIR)); //Plugin base name
}

/**
 * Load Text Domain
 * 
 * This gets the plugin ready for translation.
 * 
 * @package Woocommerce Agreement
 * @since 1.0.0
 */
function w_agree_load_textdomain()
{

	// Set filter for plugin's languages directory
	$w_agree_lang_dir	= dirname(plugin_basename(__FILE__)) . '/languages/';
	$w_agree_lang_dir	= apply_filters('w_agree_lang_directory', $w_agree_lang_dir);

	// Traditional WordPress plugin locale filter
	$locale	= apply_filters('plugin_locale',  get_locale(), 'w_agree');
	$mofile	= sprintf('%1$s-%2$s.mo', 'w_agree', $locale);

	// Setup paths to current locale file
	$mofile_local	= $w_agree_lang_dir . $mofile;
	$mofile_global	= WP_LANG_DIR . '/' . W_AGREE_PLUGIN_BASENAME . '/' . $mofile;

	if (file_exists($mofile_global)) {
		load_textdomain('w_agree', $mofile_global);
	} elseif (file_exists($mofile_local)) {
		load_textdomain('w_agree', $mofile_local);
	} else { // Load the default language files
		load_plugin_textdomain('w_agree', false, $w_agree_lang_dir);
	}
}


/**
 * Activation Hook
 * 
 * Initial setup of the plugin setting default options 
 * and database tables creations.
 * 
 * @package Woocommerce Agreement
 * @since 1.0.0
 */
function w_agree_install()
{
	global $wpdb;
}
register_activation_hook(__FILE__, 'w_agree_install');

/**
 * Deactivation Hook
 * 
 * Does the drop tables in the database and
 * delete  plugin options.
 *
 * @package Woocommerce Agreement
 * @since 1.0.0
 */
function w_agree_uninstall()
{
	global $wpdb;
}
register_deactivation_hook(__FILE__, 'w_agree_uninstall');

/**
 * Load Plugin
 * 
 * Handles to load plugin after
 * dependent plugin is loaded
 * successfully
 * 
 * @package Woocommerce Agreement
 * @since 1.0.0
 */
function w_agree_plugin_loaded()
{

	// load first plugin text domain
	w_agree_load_textdomain();
}
add_action('plugins_loaded', 'w_agree_plugin_loaded');

/**
 * Initialize all global variables
 * 
 * @package Woocommerce Agreement
 * @since 1.0.0
 */
global $w_agree_admin, $w_agree_front, $w_agree_scripts;

/**
 * Includes
 *
 * Includes all the needed files for our plugin
 *
 * @package Woocommerce Agreement
 * @since 1.0.0
 */

// Admin class handles most of admin panel functionalities of plugin
include_once(W_AGREE_ADMIN_DIR . '/class_woocommerce_agreement_admin.php');
$w_agree_admin = new Woocommerce_Agreement_Admin();
$w_agree_admin->add_hooks();

// Front view class handles most of view side functionalities of plugin
include_once(W_AGREE_DIR . '/public/class_woocommerce_agreement_front.php');
$w_agree_front = new Woocommerce_Agreement_Front();
$w_agree_front->add_hooks();

// Script class
include_once(W_AGREE_DIR . '/includes/class_woocommerce_agreement_script.php');
$w_agree_scripts = new Woocommerce_Agreement_Scripts();
$w_agree_scripts->add_hooks();

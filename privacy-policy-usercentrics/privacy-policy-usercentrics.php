<?php
/**
 * Plugin Name:       Usercentrics Privacy Policy Generator
 * Plugin URI:        https://usercentrics.com/privacy-policy-generator
 * Description:       Easily integrate Usercentrics privacy policies. No manual script insertion required.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Usercentrics
 * Author URI:        https://usercentrics.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       privacy-policy-usercentrics
 * Domain Path:       /languages
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'PPGUC_PLUGIN_FILE', __FILE__ );
define( 'PPGUC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PPGUC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PPGUC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Get version from plugin header (single source of truth).
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$ppguc_plugin_data = get_plugin_data( __FILE__, false, false );
define( 'PPGUC_VERSION', $ppguc_plugin_data['Version'] );

// Asset paths.
define( 'PPGUC_IMAGES_URL', PPGUC_PLUGIN_URL . 'assets/images/' );

// Load Composer autoloader.
if ( file_exists( PPGUC_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once PPGUC_PLUGIN_DIR . 'vendor/autoload.php';
}

// Activation/Deactivation hooks.
register_activation_hook( __FILE__, array( Core\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Core\Deactivator::class, 'deactivate' ) );

// Initialize plugin.
add_action(
	'plugins_loaded',
	function () {
		$plugin = new Core\Plugin();
		$plugin->init();
	}
);

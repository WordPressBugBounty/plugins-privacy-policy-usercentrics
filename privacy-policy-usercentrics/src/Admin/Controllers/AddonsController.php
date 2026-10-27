<?php
/**
 * Addons Controller
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Controllers
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Admin\Controllers;

use Usercentrics\PrivacyPolicyGenerator\Services\PluginInstallerService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Addons controller class
 * Handles the addons page for installing recommended plugins
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Controllers
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class AddonsController extends BaseController {

	/**
	 * Plugin installer service instance
	 *
	 * @var PluginInstallerService
	 */
	private PluginInstallerService $plugin_installer;

	/**
	 * Constructor
	 *
	 * @param \Usercentrics\PrivacyPolicyGenerator\Settings\SettingsService $settings_service   Settings service instance.
	 * @param PluginInstallerService                                        $plugin_installer   Plugin installer service instance.
	 */
	public function __construct(
		\Usercentrics\PrivacyPolicyGenerator\Settings\SettingsService $settings_service,
		PluginInstallerService $plugin_installer
	) {
		parent::__construct( $settings_service );
		$this->plugin_installer = $plugin_installer;
	}

	/**
	 * Initialize AJAX handlers
	 *
	 * @return void
	 */
	public function initAjax(): void {
		add_action( 'wp_ajax_ppguc_install_plugin', array( $this, 'handleInstallPlugin' ) );
		add_action( 'wp_ajax_ppguc_activate_plugin', array( $this, 'handleActivatePlugin' ) );
	}

	/**
	 * Render addons page
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! $this->canManageOptions() ) {
			$this->unauthorizedAccess();
		}

		// Get plugin information.
		$cookiebot_plugin = $this->plugin_installer->getPluginInfo( 'cookiebot' );

		// Load template with plugin data.
		$this->loadTemplate(
			'addons-page',
			compact( 'cookiebot_plugin' )
		);
	}

	/**
	 * Handle AJAX request to install plugin
	 *
	 * @return void
	 */
	public function handleInstallPlugin(): void {
		check_ajax_referer( 'ppg-addons-nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You do not have permission to install plugins.', 'privacy-policy-usercentrics' ),
				)
			);
		}

		$plugin_slug = isset( $_POST['plugin_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_slug'] ) ) : '';

		if ( empty( $plugin_slug ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid plugin slug.', 'privacy-policy-usercentrics' ),
				)
			);
		}

		$result = $this->plugin_installer->installPlugin( $plugin_slug );

		if ( is_wp_error( $result ) ) {
			// Track failed installation.
			$this->trackPluginEvent( 'install_failed', $plugin_slug, $result->get_error_message() );

			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
				)
			);
		}

		// Track successful installation event.
		$this->trackPluginEvent( 'install', $plugin_slug );

		wp_send_json_success(
			array(
				'message' => __( 'Plugin installed successfully.', 'privacy-policy-usercentrics' ),
			)
		);
	}

	/**
	 * Handle AJAX request to activate plugin
	 *
	 * @return void
	 */
	public function handleActivatePlugin(): void {
		check_ajax_referer( 'ppg-addons-nonce', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You do not have permission to activate plugins.', 'privacy-policy-usercentrics' ),
				)
			);
		}

		$plugin_slug = isset( $_POST['plugin_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_slug'] ) ) : '';

		if ( empty( $plugin_slug ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid plugin slug.', 'privacy-policy-usercentrics' ),
				)
			);
		}

		$result = $this->plugin_installer->activatePlugin( $plugin_slug );

		if ( is_wp_error( $result ) ) {
			// Track failed activation.
			$this->trackPluginEvent( 'activate_failed', $plugin_slug, $result->get_error_message() );

			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
				)
			);
		}

		// Track successful activation event.
		$this->trackPluginEvent( 'activate', $plugin_slug );

		wp_send_json_success(
			array(
				'message' => __( 'Plugin activated successfully.', 'privacy-policy-usercentrics' ),
			)
		);
	}

	/**
	 * Track plugin event for analytics
	 *
	 * @param string      $action       The action performed (install, activate, install_failed, activate_failed).
	 * @param string      $plugin_slug  The plugin slug.
	 * @param string|null $error_message Optional error message for failed actions.
	 *
	 * @return void
	 */
	private function trackPluginEvent( string $action, string $plugin_slug, ?string $error_message = null ): void {
		// Prepare event data.
		$event_data = array(
			'action'      => $action,
			'plugin_slug' => $plugin_slug,
			'timestamp'   => current_time( 'mysql' ),
			'user_id'     => get_current_user_id(),
		);

		// Add error message if this is a failed action.
		if ( $error_message ) {
			$event_data['error_message'] = $error_message;
		}

		// Store event in database for tracking.
		$events   = get_option( 'ppguc_addon_events', array() );
		$events[] = $event_data;

		// Keep only last 500 events (increased from 100 for better data retention).
		if ( count( $events ) > 500 ) {
			$events = array_slice( $events, -500 );
		}

		$saved = update_option( 'ppguc_addon_events', $events );

		// Update option returns false on failure or if value unchanged.
		// No logging needed as this is not a critical error.

		// Fire action hook for additional tracking integrations.
		do_action( 'ppguc_addon_event', $action, $plugin_slug, $error_message );
	}
}

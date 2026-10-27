<?php
/**
 * Main Menu
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Admin;

use Usercentrics\PrivacyPolicyGenerator\Admin\Controllers\DashboardController;
use Usercentrics\PrivacyPolicyGenerator\Admin\Controllers\SettingsController;
use Usercentrics\PrivacyPolicyGenerator\Admin\Controllers\SupportController;
use Usercentrics\PrivacyPolicyGenerator\Admin\Controllers\AddonsController;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main menu class
 * Responsible ONLY for registering WordPress admin menu items
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class MainMenu {

	/**
	 * Dashboard controller instance
	 *
	 * @var DashboardController
	 */
	private DashboardController $dashboard_controller;

	/**
	 * Settings controller instance
	 *
	 * @var SettingsController
	 */
	private SettingsController $settings_controller;

	/**
	 * Support controller instance
	 *
	 * @var SupportController
	 */
	private SupportController $support_controller;

	/**
	 * Addons controller instance
	 *
	 * @var AddonsController
	 */
	private AddonsController $addons_controller;

	/**
	 * Upgrade URL for Pro link
	 *
	 * @var string
	 */
	private string $upgrade_url = '';

	/**
	 * Constructor
	 *
	 * @param DashboardController $dashboard_controller Dashboard controller instance.
	 * @param SettingsController  $settings_controller  Settings controller instance.
	 * @param SupportController   $support_controller   Support controller instance.
	 * @param AddonsController    $addons_controller    Addons controller instance.
	 */
	public function __construct(
		DashboardController $dashboard_controller,
		SettingsController $settings_controller,
		SupportController $support_controller,
		AddonsController $addons_controller
	) {
		$this->dashboard_controller = $dashboard_controller;
		$this->settings_controller  = $settings_controller;
		$this->support_controller   = $support_controller;
		$this->addons_controller    = $addons_controller;

		// Hook to display transient admin notices.
		add_action( 'admin_notices', array( $this, 'displayAdminNotices' ) );

		// Add target="_blank" to upgrade menu link.
	}

	/**
	 * Display admin notices from transients
	 *
	 * @return void
	 */
	public function displayAdminNotices(): void {
		// Only on our plugin pages.
		$screen = get_current_screen();
		if ( ! $screen || strpos( $screen->id, 'ppg' ) === false ) {
			return;
		}

		$notice = get_transient( 'ppguc_admin_notice' );
		if ( $notice && is_array( $notice ) && isset( $notice['type'], $notice['message'] ) ) {
			$type    = sanitize_key( $notice['type'] );
			$message = esc_html( $notice['message'] );

			printf(
				'<div class="notice notice-%s is-dismissible ppg-notice"><p>%s</p></div>',
				esc_attr( $type ),
				wp_kses_post( $message )
			);

			// Delete transient after displaying.
			delete_transient( 'ppguc_admin_notice' );
		}
	}

	/**
	 * Register menu pages
	 *
	 * @return void
	 */
	public function registerMenu(): void {
		// Main menu.
		add_menu_page(
			__( 'Usercentrics Privacy Policy Generator', 'privacy-policy-usercentrics' ),
			__( 'Usercentrics Privacy Policy Generator', 'privacy-policy-usercentrics' ),
			'manage_options',
			'privacy-policy-usercentrics',
			array( $this->dashboard_controller, 'render' ),
			$this->getMenuIcon()
		);

		// Overview submenu (same as main).
		add_submenu_page(
			'privacy-policy-usercentrics',
			__( 'Dashboard', 'privacy-policy-usercentrics' ),
			__( 'Dashboard', 'privacy-policy-usercentrics' ),
			'manage_options',
			'privacy-policy-usercentrics',
			array( $this->dashboard_controller, 'render' )
		);

		// Settings submenu.
		add_submenu_page(
			'privacy-policy-usercentrics',
			__( 'Settings', 'privacy-policy-usercentrics' ),
			__( 'Settings', 'privacy-policy-usercentrics' ),
			'manage_options',
			'ppg-settings',
			array( $this->settings_controller, 'render' )
		);

		// Addons submenu (Enabled - comment to disable).
		// add_submenu_page(
		// 	'privacy-policy-usercentrics',
		// 	__( 'Addons', 'privacy-policy-usercentrics' ),
		// 	__( 'Addons', 'privacy-policy-usercentrics' ),
		// 	'manage_options',
		// 	'ppg-addons',
		// 	array( $this->addons_controller, 'render' )
		// );

	// Support submenu.
	add_submenu_page(
		'privacy-policy-usercentrics',
		__( 'Support', 'privacy-policy-usercentrics' ),
		__( 'Support', 'privacy-policy-usercentrics' ),
		'manage_options',
		'ppg-support',
		array( $this->support_controller, 'render' )
	);

	// Upgrade to Pro submenu - external link styled as a button.
	$upgrade_utm = 'utm_source=wordpress&utm_medium=referral&utm_campaign=ppg_upgrade_menu&utm_content=upgrade_to_pro_button#uc-ppg';
	$this->upgrade_url = 'https://usercentrics.com/pricing/?' . $upgrade_utm;
	
	add_submenu_page(
		'privacy-policy-usercentrics',
		'', // Empty title so it doesn't show in page title.
		'<span class="ppg-upgrade-link">' . esc_html__( 'Upgrade to Pro', 'privacy-policy-usercentrics' ) . '</span>',
		'manage_options',
		esc_url( $this->upgrade_url ),
		'__return_null' // No callback needed for external links.
	);
	
	// Enqueue script to make upgrade link work.
	add_action( 'admin_enqueue_scripts', array( $this, 'enqueueUpgradeLinkScript' ) );
}

	/**
	 * Enqueue upgrade link script
	 * Makes the "Upgrade to Pro" menu item work as an external link.
	 *
	 * @return void
	 */
	public function enqueueUpgradeLinkScript(): void {
		// Enqueue on all admin pages (menu is visible everywhere).
		if ( ! is_admin() ) {
			return;
		}

		wp_enqueue_script(
			'ppguc-menu-upgrade-link',
			PPGUC_PLUGIN_URL . 'assets/dist/js/menu-upgrade-link.js',
			array(),
			PPGUC_VERSION,
			true
		);
		
		wp_localize_script(
			'ppguc-menu-upgrade-link',
			'ppgucUpgradeLink',
			array(
				'url' => $this->upgrade_url,
			)
		);
	}

	/**
	 * Get menu icon
	 *
	 * @return string SVG icon as data URI.
	 */
	private function getMenuIcon(): string {
		// Usercentrics shield icon for privacy.
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="#fff">
		<g clip-path="url(#clip0_6481_31815)">
		<path fill-rule="evenodd" clip-rule="evenodd" d="M5.8616 5.8052V19.8114C5.8616 27.5363 12.2039 33.8176 20.0039 33.8176C27.8038 33.8176 34.1462 27.5363 34.1462 19.8114V5.8052H5.8616ZM20 39.6228C8.97638 39.6207 0 30.7371 0 19.8114V0H40V19.8114C40 30.7294 31.0314 39.6207 20 39.6228ZM22.6894 26.9486L22.6888 26.9499H15.92L15.9312 26.9266L9.98921 16.5821H16.7657L19.0106 20.4902L23.7122 10.6631H30.4888L22.7035 26.9192L22.7204 26.9486H22.6894Z" fill="#fff"/>
		</g>
		<defs>
		<clipPath id="clip0_6481_31815">
		<rect width="40" height="40" fill="#fff"/>
		</clipPath>
		</defs>
		</svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}
}

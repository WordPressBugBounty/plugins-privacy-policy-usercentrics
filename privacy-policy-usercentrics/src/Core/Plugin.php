<?php
/**
 * Main Plugin Class
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Core
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Core;

use Usercentrics\PrivacyPolicyGenerator\Admin\MainMenu;
use Usercentrics\PrivacyPolicyGenerator\Admin\AssetsManager;
use Usercentrics\PrivacyPolicyGenerator\Admin\EditorContent;
use Usercentrics\PrivacyPolicyGenerator\Admin\NoticeHandler;
use Usercentrics\PrivacyPolicyGenerator\Admin\BannerDismissHandler;
use Usercentrics\PrivacyPolicyGenerator\Admin\Controllers\DashboardController;
use Usercentrics\PrivacyPolicyGenerator\Admin\Controllers\SettingsController;
use Usercentrics\PrivacyPolicyGenerator\Admin\Controllers\SupportController;
use Usercentrics\PrivacyPolicyGenerator\Admin\Controllers\AddonsController;
use Usercentrics\PrivacyPolicyGenerator\Admin\Actions\PolicyActions;
use Usercentrics\PrivacyPolicyGenerator\Admin\Actions\PageActions;
use Usercentrics\PrivacyPolicyGenerator\Frontend\PolicyContent;
use Usercentrics\PrivacyPolicyGenerator\Settings\SettingsService;
use Usercentrics\PrivacyPolicyGenerator\Services\PluginInstallerService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin orchestrator class
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Core
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class Plugin {

	/**
	 * Assets manager instance
	 *
	 * @var AssetsManager
	 */
	private AssetsManager $_assets_manager;

	/**
	 * Settings service instance
	 *
	 * @var SettingsService
	 */
	private SettingsService $_settings_service;

	/**
	 * Main menu instance
	 *
	 * @var MainMenu
	 */
	private MainMenu $_main_menu;

	/**
	 * Settings controller instance
	 *
	 * @var SettingsController
	 */
	private SettingsController $_settings_controller;

	/**
	 * Editor content instance
	 *
	 * @var EditorContent
	 */
	private EditorContent $_editor_content;

	/**
	 * Policy content instance
	 *
	 * @var PolicyContent
	 */
	private PolicyContent $_policy_content;

	/**
	 * Addons controller instance
	 *
	 * @var AddonsController
	 */
	private AddonsController $_addons_controller;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Initialize services.
		$this->_assets_manager   = new AssetsManager();
		$this->_settings_service = new SettingsService();
		$plugin_installer        = new PluginInstallerService();
		$notice_handler          = new NoticeHandler();
		$notice_handler->init();
		$banner_dismiss_handler = new BannerDismissHandler();
		$banner_dismiss_handler->init();

		// Initialize action handlers.
		$policy_actions = new PolicyActions( $this->_settings_service );
		$policy_actions->initAjax(); // Register AJAX hooks
		$page_actions = new PageActions( $this->_settings_service );

		// Initialize controllers.
		$dashboard_controller = new DashboardController( $this->_settings_service );
		$settings_controller  = new SettingsController(
			$this->_settings_service,
			$policy_actions,
			$page_actions
		);
		$support_controller   = new SupportController( $this->_settings_service );
		$addons_controller    = new AddonsController( $this->_settings_service, $plugin_installer );

		// Initialize main menu with controllers.
		$this->_main_menu           = new MainMenu(
			$dashboard_controller,
			$settings_controller,
			$support_controller,
			$addons_controller
		);
		$this->_settings_controller = $settings_controller;
		$this->_addons_controller   = $addons_controller;

		// Initialize content handlers.
		$this->_editor_content = new EditorContent( $this->_settings_service );
		$this->_policy_content = new PolicyContent( $this->_settings_service );
	}

	/**
	 * Initialize plugin
	 *
	 * @return void
	 */
	public function init(): void {
		// Admin hooks - EARLY form handling BEFORE any output.
		add_action( 'admin_init', array( $this->_settings_controller, 'handleFormActions' ), 5 ); // Priority 5 = EARLY
		add_action( 'admin_menu', array( $this->_main_menu, 'registerMenu' ) );
		add_action( 'admin_init', array( $this->_settings_service, 'registerSettings' ) );
		add_action( 'admin_init', array( $this, 'handleActivationRedirect' ) );
		add_action( 'admin_enqueue_scripts', array( $this->_assets_manager, 'enqueueAdminAssets' ) );
		add_action( 'admin_enqueue_scripts', array( $this->_assets_manager, 'enqueueGlobalMenuStyles' ) );

		// Initialize AJAX handlers for addons.
		$this->_addons_controller->initAjax();

		// Editor content hooks.
		$this->_editor_content->init();

		// Frontend policy injection hooks.
		$this->_policy_content->init();

		// Load text domain.
		add_action( 'init', array( $this, 'loadTextdomain' ) );
	}

	/**
	 * Handle activation redirect to overview page
	 *
	 * @return void
	 */
	public function handleActivationRedirect(): void {
		if ( get_transient( 'ppguc_activation_redirect' ) ) {
			delete_transient( 'ppguc_activation_redirect' );

			if ( ! get_option( 'ppguc_usercentrics_onboarding_complete' ) ) {
				// Mark as onboarded and redirect to overview.
				update_option( 'ppguc_usercentrics_onboarding_complete', '1' );
				wp_safe_redirect( admin_url( 'admin.php?page=privacy-policy-usercentrics' ) );
				exit;
			}
		}
	}

	/**
	 * Load plugin text domain
	 *
	 * WordPress.org automatically loads translations for plugins hosted on WordPress.org since 4.6.
	 * We keep this method for potential future use with custom translation files.
	 *
	 * @return void
	 */
	public function loadTextdomain(): void {
		// WordPress.org auto-loads translations - no action needed for hosted plugins.
		// Custom translations can be added to wp-content/languages/plugins/ directory.
	}
}

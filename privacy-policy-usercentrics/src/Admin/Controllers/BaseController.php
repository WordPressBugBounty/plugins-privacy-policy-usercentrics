<?php
/**
 * Base Controller
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Controllers
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Admin\Controllers;

use Usercentrics\PrivacyPolicyGenerator\Settings\SettingsService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base controller class
 * Provides common functionality for all controllers
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Controllers
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
abstract class BaseController {

	/**
	 * Settings service instance
	 *
	 * @var SettingsService
	 */
	protected SettingsService $settings_service;

	/**
	 * Constructor
	 *
	 * @param SettingsService $settings_service Settings service instance.
	 */
	public function __construct( SettingsService $settings_service ) {
		$this->settings_service = $settings_service;
	}

	/**
	 * Check if current user has required capabilities
	 *
	 * @return bool True if user can manage options.
	 */
	protected function canManageOptions(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Render unauthorized access error
	 *
	 * @return void
	 */
	protected function unauthorizedAccess(): void {
		wp_die( esc_html__( 'Unauthorized access.', 'privacy-policy-usercentrics' ) );
	}

	/**
	 * Set admin notice via transient
	 *
	 * @param string $message Notice message.
	 * @param string $type    Notice type (success, error, warning, info).
	 * @param int    $expire  Expiration time in seconds.
	 *
	 * @return void
	 */
	protected function setNotice( string $message, string $type = 'success', int $expire = 30 ): void {
		set_transient(
			'ppguc_admin_notice',
			array(
				'type'    => $type,
				'message' => $message,
			),
			$expire
		);
	}

	/**
	 * Redirect to admin page
	 *
	 * @param string $page Page slug.
	 *
	 * @return void
	 */
	protected function redirectToPage( string $page ): void {
		wp_safe_redirect( admin_url( 'admin.php?page=' . $page ) );
		exit;
	}

	/**
	 * Load template file
	 *
	 * @param string $template_name Template file name.
	 * @param array  $data          Data to extract for template.
	 *
	 * @return void
	 */
	protected function loadTemplate( string $template_name, array $data = array() ): void {
		// Make data available to template.
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Safe use with EXTR_SKIP for template rendering
		if ( ! empty( $data ) ) {
			extract( $data, EXTR_SKIP ); // phpcs:ignore
		}

		$template_path = PPGUC_PLUGIN_DIR . 'templates/admin/' . $template_name . '.php';

		if ( file_exists( $template_path ) ) {
			include $template_path;
		}
	}
}

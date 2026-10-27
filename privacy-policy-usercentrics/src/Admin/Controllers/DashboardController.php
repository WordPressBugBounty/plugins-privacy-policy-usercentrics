<?php
/**
 * Dashboard Controller
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Controllers
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Admin\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dashboard controller class
 * Handles the overview/dashboard page
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Controllers
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class DashboardController extends BaseController {

	/**
	 * Render overview/dashboard page
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! $this->canManageOptions() ) {
			$this->unauthorizedAccess();
		}

		// Get current state.
		$current_state = $this->settings_service->getCurrentState();
		$policy_id     = $this->settings_service->getPolicyId();
		$display_page  = $this->settings_service->getDisplayPage();

		// Get page details if published.
		$page_title = '';
		$page_url   = '';
		if ( $display_page > 0 ) {
			$page = get_post( $display_page );
			if ( $page ) {
				$page_title = $page->post_title;
				$page_url   = get_permalink( $display_page );
			}
		}

		// Load template with data.
		$this->loadTemplate(
			'overview-page',
			compact( 'current_state', 'policy_id', 'display_page', 'page_title', 'page_url' )
		);
	}
}

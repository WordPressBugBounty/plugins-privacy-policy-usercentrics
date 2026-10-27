<?php
/**
 * Settings Controller
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
use Usercentrics\PrivacyPolicyGenerator\Admin\Actions\PolicyActions;
use Usercentrics\PrivacyPolicyGenerator\Admin\Actions\PageActions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings controller class
 * Handles the settings page and form submissions
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Controllers
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class SettingsController extends BaseController {

	/**
	 * Policy actions instance
	 *
	 * @var PolicyActions
	 */
	private PolicyActions $policy_actions;

	/**
	 * Page actions instance
	 *
	 * @var PageActions
	 */
	private PageActions $page_actions;

	/**
	 * Constructor
	 *
	 * @param SettingsService $settings_service Settings service instance.
	 * @param PolicyActions   $policy_actions   Policy actions instance.
	 * @param PageActions     $page_actions     Page actions instance.
	 */
	public function __construct(
		SettingsService $settings_service,
		PolicyActions $policy_actions,
		PageActions $page_actions
	) {
		parent::__construct( $settings_service );
		$this->policy_actions = $policy_actions;
		$this->page_actions   = $page_actions;
	}

	/**
	 * Render settings page
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! $this->canManageOptions() ) {
			$this->unauthorizedAccess();
		}

		// Get current values.
		$current_state = $this->settings_service->getCurrentState();
		$policy_id     = $this->settings_service->getPolicyId();
		$display_page  = $this->settings_service->getDisplayPage();
		$page_option   = get_option( 'ppguc_usercentrics_page_option', 'create' );
		$pages         = get_pages(
			array(
				'post_status' => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			)
		);
		$is_connected  = $this->settings_service->isConnected();

		// Get page details if published.
		$page_title = '';
		$page_url   = '';
		if ( 'published' === $current_state && $display_page ) {
			$page       = get_post( $display_page );
			$page_title = $page ? $page->post_title : '';
			$page_url   = $page ? get_permalink( $page ) : '';
		}

		// Sort pages with privacy policy prioritization.
		$pages = $this->sortPagesByPrivacyPriority( $pages );

		// Load template with data.
		$this->loadTemplate(
			'settings-page',
			compact( 'current_state', 'policy_id', 'display_page', 'page_option', 'pages', 'is_connected', 'page_title', 'page_url' )
		);
	}

	/**
	 * Handle all form actions
	 *
	 * This method routes form actions to appropriate handlers.
	 * Nonce verification is performed in individual action handlers (PolicyActions, PageActions).
	 *
	 * @return void
	 */
	public function handleFormActions(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		// Only handle if we have an action.
		if ( ! isset( $_POST['ppguc_action'] ) ) {
			return;
		}

		// Only on our plugin pages.
		if ( ! isset( $_GET['page'] ) || strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'ppg' ) === false ) {
			return;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended

		// Check user permissions.
		if ( ! $this->canManageOptions() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in action handlers.
		$action = sanitize_text_field( wp_unslash( $_POST['ppguc_action'] ) );

		// Route to appropriate action handler.
		switch ( $action ) {
			case 'connect':
				$this->policy_actions->connect();
				break;
			case 'disconnect':
				$this->policy_actions->disconnect();
				break;
			case 'preview':
				$this->page_actions->preview();
				break;
			case 'publish':
				$this->page_actions->publish();
				break;
		}
	}

	/**
	 * Sort pages with privacy policy prioritization
	 *
	 * Priority order:
	 * 1. WordPress designated privacy policy page (get_privacy_policy_url)
	 * 2. Pages with "privacy" or "policy" in the title
	 * 3. All other pages (alphabetically)
	 *
	 * @param array $pages Array of page objects.
	 *
	 * @return array Sorted array of pages.
	 */
	private function sortPagesByPrivacyPriority( array $pages ): array {
		if ( empty( $pages ) ) {
			return $pages;
		}

		// Get WordPress designated privacy policy page ID.
		$privacy_policy_page_id = (int) get_option( 'wp_page_for_privacy_policy', 0 );

		// Categorize pages into priority groups.
		$priority_page = null;  // WordPress privacy policy page
		$privacy_pages = array(); // Pages with privacy/policy in name
		$regular_pages = array(); // All other pages

		foreach ( $pages as $page ) {
			// Priority 1: WordPress designated privacy policy page.
			if ( $privacy_policy_page_id > 0 && $page->ID === $privacy_policy_page_id ) {
				$priority_page = $page;
				continue;
			}

			// Priority 2: Pages with "privacy" or "policy" in title.
			$title_lower = strtolower( $page->post_title );
			if ( strpos( $title_lower, 'privacy' ) !== false || strpos( $title_lower, 'policy' ) !== false ) {
				$privacy_pages[] = $page;
				continue;
			}

			// Priority 3: Regular pages.
			$regular_pages[] = $page;
		}

		// Sort privacy and regular pages alphabetically by title.
		usort(
			$privacy_pages,
			function ( $a, $b ) {
				return strcasecmp( $a->post_title, $b->post_title );
			}
		);

		usort(
			$regular_pages,
			function ( $a, $b ) {
				return strcasecmp( $a->post_title, $b->post_title );
			}
		);

		// Combine all arrays in priority order.
		$sorted_pages = array();

		if ( $priority_page ) {
			$sorted_pages[] = $priority_page;
		}

		$sorted_pages = array_merge( $sorted_pages, $privacy_pages );
		$sorted_pages = array_merge( $sorted_pages, $regular_pages );

		return $sorted_pages;
	}
}

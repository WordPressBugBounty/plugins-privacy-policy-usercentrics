<?php
/**
 * Page Actions
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Actions
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Admin\Actions;

use Usercentrics\PrivacyPolicyGenerator\Settings\SettingsService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page actions class
 * Handles page-related actions (create, publish, preview)
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Actions
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class PageActions {

	/**
	 * Settings service instance
	 *
	 * @var SettingsService
	 */
	private SettingsService $settings_service;

	/**
	 * Constructor
	 *
	 * @param SettingsService $settings_service Settings service instance.
	 */
	public function __construct( SettingsService $settings_service ) {
		$this->settings_service = $settings_service;
	}

	/**
	 * Handle preview action
	 *
	 * @return void
	 */
	public function preview(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified below.
		if ( ! $this->verifyNonce( 'ppguc_publish_nonce', 'ppguc_publish_policy' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'privacy-policy-usercentrics' ) );
		}

		$page_id = $this->getOrCreatePage();

		if ( $page_id ) {
			// Update display page option so editor preview works.
			$this->settings_service->updateOption( 'ppguc_usercentrics_display_page', $page_id );

			// Force update page meta (in case option value didn't change).
			update_post_meta( $page_id, '_ppguc_usercentrics_page', '1' );

			// Save the page option choice (create or existing).
			$page_option = isset( $_POST['ppguc_page_option'] )
				? sanitize_text_field( wp_unslash( $_POST['ppguc_page_option'] ) )
				: 'create';
			$this->settings_service->updateOption( 'ppguc_usercentrics_page_option', $page_option );

			// Redirect to WordPress editor in preview mode.
			wp_safe_redirect( admin_url( 'post.php?post=' . $page_id . '&action=edit' ) );
			exit;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Handle publish action
	 *
	 * @return void
	 */
	public function publish(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified below.
		if ( ! $this->verifyNonce( 'ppguc_publish_nonce', 'ppguc_publish_policy' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'privacy-policy-usercentrics' ) );
		}

		$page_id = $this->getOrCreatePage();

		if ( $page_id ) {
			// Update display page option.
			$this->settings_service->updateOption( 'ppguc_usercentrics_display_page', $page_id );

			// Force update page meta (in case option value didn't change).
			update_post_meta( $page_id, '_ppguc_usercentrics_page', '1' );

			// Save the page option choice (create or existing).
			$page_option = isset( $_POST['ppguc_page_option'] )
				? sanitize_text_field( wp_unslash( $_POST['ppguc_page_option'] ) )
				: 'create';
			$this->settings_service->updateOption( 'ppguc_usercentrics_page_option', $page_option );

			// Publish the page if it's not already.
			$page = get_post( $page_id );
			if ( $page && 'publish' !== $page->post_status ) {
				wp_update_post(
					array(
						'ID'          => $page_id,
						'post_status' => 'publish',
					)
				);
			}

			$this->setNotice(
				__( 'Privacy Policy published successfully!', 'privacy-policy-usercentrics' ),
				'success'
			);

			// Redirect to overview.
			wp_safe_redirect( admin_url( 'admin.php?page=privacy-policy-usercentrics' ) );
			exit;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Get or create page based on form data
	 *
	 * Called after nonce verification in preview() and publish() methods.
	 *
	 * @return int|false Page ID or false on failure.
	 */
	private function getOrCreatePage() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Called after nonce verification.
		$page_option = isset( $_POST['ppguc_page_option'] )
			? sanitize_text_field( wp_unslash( $_POST['ppguc_page_option'] ) )
			: 'existing';

		if ( 'existing' === $page_option ) {
			// Use existing page.
			$page_id = isset( $_POST['ppguc_existing_page'] )
				? absint( $_POST['ppguc_existing_page'] )
				: 0;
			return $page_id > 0 ? $page_id : false;
		} else {
			// Create new page with placeholder content.
			return $this->createNewPage();
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Create new page (empty content - policy is injected on frontend)
	 *
	 * Called after nonce verification in getOrCreatePage() method.
	 *
	 * @return int|false Page ID or false on failure.
	 */
	private function createNewPage() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Called after nonce verification.
		$page_title = isset( $_POST['ppguc_new_page_name'] )
			? sanitize_text_field( wp_unslash( $_POST['ppguc_new_page_name'] ) )
			: __( 'Our Privacy Policy', 'privacy-policy-usercentrics' );

		// Create page with empty content.
		// The privacy policy content is automatically injected on the frontend via PolicyContent filter.
		$page_id = wp_insert_post(
			array(
				'post_title'   => $page_title,
				'post_content' => '',
				'post_status'  => 'draft',
				'post_type'    => 'page',
			)
		);

		return ! is_wp_error( $page_id ) ? $page_id : false;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Verify nonce for security
	 *
	 * @param string $nonce_field  Nonce field name.
	 * @param string $nonce_action Nonce action name.
	 *
	 * @return bool True if nonce is valid.
	 */
	private function verifyNonce( string $nonce_field, string $nonce_action ): bool {
		return isset( $_POST[ $nonce_field ] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_field ] ) ), $nonce_action );
	}

	/**
	 * Set admin notice via transient
	 *
	 * @param string $message Notice message.
	 * @param string $type    Notice type.
	 *
	 * @return void
	 */
	private function setNotice( string $message, string $type = 'success' ): void {
		set_transient(
			'ppguc_admin_notice',
			array(
				'type'    => $type,
				'message' => $message,
			),
			30
		);
	}
}

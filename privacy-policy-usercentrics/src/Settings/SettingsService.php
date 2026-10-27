<?php
/**
 * Settings Service
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Settings
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings service class
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Settings
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class SettingsService {

	/**
	 * Option name for policy ID
	 */
	private const OPTION_POLICY_ID = 'ppguc_usercentrics_policy_id';

	/**
	 * Option name for display page
	 */
	private const OPTION_DISPLAY_PAGE = 'ppguc_usercentrics_display_page';

	/**
	 * Register WordPress settings
	 *
	 * @return void
	 */
	public function registerSettings(): void {
		register_setting(
			'ppguc_settings_group',
			self::OPTION_POLICY_ID,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitizePolicyId' ),
				'default'           => '',
			)
		);

		register_setting(
			'ppguc_settings_group',
			self::OPTION_DISPLAY_PAGE,
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
	}

	/**
	 * Sanitize and validate policy ID
	 *
	 * @param string $input Policy ID to sanitize.
	 *
	 * @return string Sanitized policy ID or empty string if invalid.
	 */
	public function sanitizePolicyId( string $input ): string {
		$input = sanitize_text_field( $input );

		if ( empty( $input ) ) {
			return '';
		}

		// UUID validation: 8-4-4-4-12 hexadecimal.
		$pattern = '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i';

		if ( ! preg_match( $pattern, $input ) ) {
			add_settings_error(
				self::OPTION_POLICY_ID,
				'invalid_uuid',
				__(
					'Invalid Policy ID format. Expected format: 2b3a23a0-94de-49c1-8bb2-0a4a847dd425',
					'privacy-policy-usercentrics'
				),
				'error'
			);
			return get_option( self::OPTION_POLICY_ID, '' );
		}

		return strtolower( $input );
	}

	/**
	 * Get policy ID
	 *
	 * @return string Policy ID.
	 */
	public function getPolicyId(): string {
		return get_option( self::OPTION_POLICY_ID, '' );
	}

	/**
	 * Get display page ID
	 *
	 * @return int Page ID.
	 */
	public function getDisplayPage(): int {
		return (int) get_option( self::OPTION_DISPLAY_PAGE, 0 );
	}

	/**
	 * Get current plugin state
	 *
	 * States:
	 * - 'not_connected': No policy ID set
	 * - 'connected_not_published': Policy ID set but page not published
	 * - 'published': Policy ID set and page is published
	 *
	 * @return string Current state.
	 */
	public function getCurrentState(): string {
		$policy_id    = $this->getPolicyId();
		$display_page = $this->getDisplayPage();

		// State 1: Not connected.
		if ( empty( $policy_id ) ) {
			return 'not_connected';
		}

		// State 2: Connected but not published.
		if ( 0 === $display_page ) {
			return 'connected_not_published';
		}

		// Check if page exists and is published.
		$page = get_post( $display_page );
		if ( ! $page || 'publish' !== $page->post_status ) {
			return 'connected_not_published';
		}

		// State 3: Connected and published.
		return 'published';
	}

	/**
	 * Check if policy is connected
	 *
	 * @return bool True if policy ID is set.
	 */
	public function isConnected(): bool {
		return ! empty( $this->getPolicyId() );
	}

	/**
	 * Check if policy is published
	 *
	 * @return bool True if connected and page is published.
	 */
	public function isPublished(): bool {
		return 'published' === $this->getCurrentState();
	}

	/**
	 * Update option value
	 *
	 * @param string $option_name Option name.
	 * @param mixed  $value       Option value.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function updateOption( string $option_name, $value ): bool {
		return update_option( $option_name, $value );
	}
}

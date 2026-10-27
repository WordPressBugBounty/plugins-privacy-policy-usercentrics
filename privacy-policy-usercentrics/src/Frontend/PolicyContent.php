<?php
/**
 * Policy Content Handler
 *
 * Handles policy content injection without modifying database
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Frontend
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Frontend;

use Usercentrics\PrivacyPolicyGenerator\Settings\SettingsService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Policy content handler class
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Frontend
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class PolicyContent {

	/**
	 * Settings service instance
	 *
	 * @var SettingsService
	 */
	private SettingsService $_settings;

	/**
	 * Constructor
	 *
	 * @param SettingsService $settings Settings service instance.
	 */
	public function __construct( SettingsService $settings ) {
		$this->_settings = $settings;
	}

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public function init(): void {
		// Filter content on frontend only (not in admin/editor).
		add_filter( 'the_content', array( $this, 'injectPolicyContent' ), 999 );

		// Enqueue scripts on frontend.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueuePolicyScript' ) );
	}

	/**
	 * Check if we should inject policy on current page
	 *
	 * @return bool True if should inject.
	 */
	private function _shouldInject(): bool {
		// Don't inject in admin.
		if ( is_admin() ) {
			return false;
		}

		$display_page = $this->_settings->getDisplayPage();

		// No page selected.
		if ( 0 === $display_page ) {
			return false;
		}

		// Check if current page matches.
		return is_page( $display_page );
	}

	/**
	 * Inject policy content container (replaces original content)
	 *
	 * @param string $content Original page content.
	 *
	 * @return string Policy container (original content hidden but preserved in DB).
	 */
	public function injectPolicyContent( string $content ): string {
		// Only in the_loop on main query.
		if ( ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		// Check if we should inject.
		if ( ! $this->_shouldInject() ) {
			return $content;
		}

		$policy_id = $this->_settings->getPolicyId();
		if ( empty( $policy_id ) ) {
			return $content;
		}

		// Try to get cached policy HTML.
		$cached_policy = $this->getCachedPolicy( $policy_id );

		// REPLACE content entirely with policy container.
		// Original content is preserved in database, just not displayed.
		$policy_container  = '<div class="uc-privacy-policy-wrapper">';
		$policy_container .= '<div class="uc-privacy-policy" id="uc-privacy-policy" data-policy-id="' . esc_attr( $policy_id ) . '">';
		
		if ( $cached_policy ) {
			// Use cached content (instant load).
			$policy_container .= $cached_policy;
		} else {
			// Show nice loading skeleton while JS loads.
			$policy_container .= '<div class="uc-policy-loading">';
			$policy_container .= '<div class="uc-policy-skeleton">';
			$policy_container .= '<div class="uc-skeleton-header"></div>';
			$policy_container .= '<div class="uc-skeleton-line"></div>';
			$policy_container .= '<div class="uc-skeleton-line"></div>';
			$policy_container .= '<div class="uc-skeleton-line"></div>';
			$policy_container .= '<div class="uc-skeleton-line"></div>';
			$policy_container .= '<div class="uc-skeleton-line short"></div>';
			$policy_container .= '<div class="uc-skeleton-line short"></div>';
			$policy_container .= '<div class="uc-skeleton-line short"></div>';
			$policy_container .= '<div class="uc-skeleton-line"></div>';
			$policy_container .= '<div class="uc-skeleton-line"></div>';
			$policy_container .= '<div class="uc-skeleton-line"></div>';
			$policy_container .= '<div class="uc-skeleton-line"></div>';
			$policy_container .= '</div>';
			$policy_container .= '</div>';
		}
		
		$policy_container .= '</div>';
		$policy_container .= '<noscript><div class="ppguc-noscript-message"><p>' .
							esc_html__( 'Please enable JavaScript to view the privacy policy.', 'privacy-policy-usercentrics' ) .
							'</p></div></noscript>';
		$policy_container .= '</div>';

		// Return ONLY the policy container (replaces all page content).
		return $policy_container;
	}

	/**
	 * Get cached policy HTML or fetch and cache it
	 *
	 * @param string $policy_id Policy ID.
	 *
	 * @return string|false Cached HTML or false if not cached.
	 */
	private function getCachedPolicy( string $policy_id ) {
		// Try to get from cache.
		$cache_key    = 'ppguc_policy_' . md5( $policy_id );
		$cached_html  = get_transient( $cache_key );

		if ( false !== $cached_html ) {
			return $cached_html;
		}

		// Fetch from API and cache it.
		$language = $this->getBrowserLanguage();
		$api_url  = 'https://policygenerator.usercentrics.eu/api/embedding?id=' . urlencode( $policy_id ) . '&language=' . urlencode( $language );

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout' => 10,
				'headers' => array(
					'User-Agent' => 'WordPress/Usercentrics-PPG-Plugin',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$html = wp_remote_retrieve_body( $response );
		if ( empty( $html ) ) {
			return false;
		}

		// Cache for 10 minutes.
		set_transient( $cache_key, $html, 10 * MINUTE_IN_SECONDS );

		return $html;
	}

	/**
	 * Get browser language from HTTP headers
	 *
	 * @return string Language code (e.g., 'en', 'de').
	 */
	private function getBrowserLanguage(): string {
		// Check if Accept-Language header is set.
		if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			$lang = sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) );
			// Get first language from the list.
			$lang = strtok( $lang, ',' );
			$lang = strtok( $lang, ';' );
			return strtolower( substr( $lang, 0, 2 ) );
		}

		return 'en';
	}

	/**
	 * Enqueue policy loading script
	 *
	 * @return void
	 */
	public function enqueuePolicyScript(): void {
		if ( ! $this->_shouldInject() ) {
			return;
		}

		$policy_id = $this->_settings->getPolicyId();
		if ( empty( $policy_id ) ) {
			return;
		}

		// Enqueue the compiled policy loader script.
		wp_enqueue_script(
			'ppguc-policy-loader',
			PPGUC_PLUGIN_URL . 'assets/dist/js/policy-loader.js',
			array(),
			PPGUC_VERSION,
			true
		);

		// Also enqueue frontend CSS.
		wp_enqueue_style(
			'ppguc-frontend',
			PPGUC_PLUGIN_URL . 'assets/dist/css/frontend.css',
			array(),
			PPGUC_VERSION
		);
	}
}


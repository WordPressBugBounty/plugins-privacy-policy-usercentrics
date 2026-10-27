<?php
/**
 * Assets Manager
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Assets manager class
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class AssetsManager {

	/**
	 * Enqueue global admin menu styles
	 *
	 * Loads on all admin pages to style the menu icon.
	 * Uses inline CSS to keep it lightweight and ensure it loads everywhere.
	 *
	 * @return void
	 */
	public function enqueueGlobalMenuStyles(): void {
		// Inline CSS for menu icon - keeps it lightweight.
		// Upgrade menu styling is handled in compiled SCSS (components/_menu.scss).
		$menu_css = '#adminmenu #toplevel_page_privacy-policy-usercentrics .wp-menu-image { padding: 7px 0 !important; }';
		
		// Add to common-css which is always loaded in admin.
		wp_add_inline_style( 'common', $menu_css );
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 *
	 * @return void
	 */
	public function enqueueAdminAssets( string $hook ): void {

		// Only load on our pages.
		$ppguc_pages = array(
			'toplevel_page_privacy-policy-usercentrics',
			'usercentrics-privacy-policy-generator_page_ppg-settings',
			'usercentrics-privacy-policy-generator_page_ppg-addons',
			'usercentrics-privacy-policy-generator_page_ppg-support',
		);

		if ( ! in_array( $hook, $ppguc_pages, true ) ) {
			return;
		}

		// Enqueue admin CSS.
		$css_file = PPGUC_PLUGIN_URL . 'assets/dist/css/admin-styles.css';
		if ( file_exists( PPGUC_PLUGIN_DIR . 'assets/dist/css/admin-styles.css' ) ) {
			wp_enqueue_style(
				'ppg-admin-styles',
				$css_file,
				array(),
				PPGUC_VERSION
			);
		}

		// Enqueue admin JS.
		$js_file = PPGUC_PLUGIN_URL . 'assets/dist/js/settings.js';
		if ( file_exists( PPGUC_PLUGIN_DIR . 'assets/dist/js/settings.js' ) ) {
			wp_enqueue_script(
				'ppg-settings',
				$js_file,
				array(),
				PPGUC_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
			add_filter(
				'script_loader_tag',
				function ( $tag, $handle ) {
					if ( 'ppg-settings' === $handle ) {
						return str_replace( ' src', ' type="module" src', $tag );
					}
					return $tag;
				},
				10,
				2
			);

			// Pass translated strings to JavaScript.
			wp_localize_script(
				'ppg-settings',
				'ppgucStrings',
				array(
					'validPolicyId'   => __( 'Please enter a valid Policy ID', 'privacy-policy-usercentrics' ),
					'enterPageName'   => __( 'Please enter a name for the new page.', 'privacy-policy-usercentrics' ),
					'selectPage'      => __( 'Please select an existing page to continue.', 'privacy-policy-usercentrics' ),
					'publishFirst'    => __( 'Please publish the page first to preview it.', 'privacy-policy-usercentrics' ),
					'policyIdValid'   => __( 'Policy ID is valid!', 'privacy-policy-usercentrics' ),
					'policyIdInvalid' => __( 'Policy ID not found', 'privacy-policy-usercentrics' ),
					'validationError' => __( 'Validation failed', 'privacy-policy-usercentrics' ),
				)
			);

			// Pass AJAX config.
			wp_localize_script(
				'ppg-settings',
				'ppgucAjax',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'ppguc_ajax_nonce' ),
				)
			);
		}

		// Enqueue toast notifications JS (for all PPG pages).
		$toast_js = PPGUC_PLUGIN_URL . 'assets/dist/js/toast.js';
		if ( file_exists( PPGUC_PLUGIN_DIR . 'assets/dist/js/toast.js' ) ) {
			wp_enqueue_script(
				'ppg-toast',
				$toast_js,
				array(),
				PPGUC_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
			add_filter(
				'script_loader_tag',
				function ( $tag, $handle ) {
					if ( 'ppg-toast' === $handle ) {
						return str_replace( ' src', ' type="module" src', $tag );
					}
					return $tag;
				},
				10,
				2
			);
		}

		// Enqueue video lazy loading JS (for all PPG pages with videos).
		$video_lazy_load_file = PPGUC_PLUGIN_URL . 'assets/dist/js/video-lazy-load.js';
		if ( file_exists( PPGUC_PLUGIN_DIR . 'assets/dist/js/video-lazy-load.js' ) ) {
			wp_enqueue_script(
				'ppg-video-lazy-load',
				$video_lazy_load_file,
				array(),
				PPGUC_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}

		// Enqueue status banner JS (for overview page).
		$status_banner_js = PPGUC_PLUGIN_URL . 'assets/dist/js/status-banner.js';
		if ( file_exists( PPGUC_PLUGIN_DIR . 'assets/dist/js/status-banner.js' ) ) {
			wp_enqueue_script(
				'ppg-status-banner',
				$status_banner_js,
				array(),
				PPGUC_VERSION,
				true
			);

			// Pass nonce for banner dismiss.
			wp_localize_script(
				'ppg-status-banner',
				'ppgucBanner',
				array(
					'nonce' => wp_create_nonce( 'ppguc_banner_nonce' ),
				)
			);
		}

		// Enqueue addons page specific JS.
		if ( 'usercentrics-privacy-policy-generator_page_ppg-addons' === $hook ) {
			$addons_js_file = PPGUC_PLUGIN_URL . 'assets/dist/js/addons.js';
			if ( file_exists( PPGUC_PLUGIN_DIR . 'assets/dist/js/addons.js' ) ) {
				wp_enqueue_script(
					'ppg-addons',
					$addons_js_file,
					array(),
					PPGUC_VERSION,
					array(
						'in_footer' => true,
						'strategy'  => 'defer',
					)
				);
				add_filter(
					'script_loader_tag',
					function ( $tag, $handle ) {
						if ( 'ppg-addons' === $handle ) {
							return str_replace( ' src', ' type="module" src', $tag );
						}
						return $tag;
					},
					10,
					2
				);
			}
		}

		// Enqueue support page specific JS.
		if ( 'usercentrics-privacy-policy-generator_page_ppg-support' === $hook ) {
			$support_js_file = PPGUC_PLUGIN_URL . 'assets/dist/js/support.js';
			if ( file_exists( PPGUC_PLUGIN_DIR . 'assets/dist/js/support.js' ) ) {
				wp_enqueue_script(
					'ppg-support',
					$support_js_file,
					array(),
					PPGUC_VERSION,
					array(
						'in_footer' => true,
						'strategy'  => 'defer',
					)
				);
				add_filter(
					'script_loader_tag',
					function ( $tag, $handle ) {
						if ( 'ppg-support' === $handle ) {
							return str_replace( ' src', ' type="module" src', $tag );
						}
						return $tag;
					},
					10,
					2
				);
			}
		}
	}
}

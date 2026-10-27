<?php
/**
 * Plugin Activator
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Core
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activator class
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Core
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class Activator {

	/**
	 * Activate plugin
	 *
	 * @return void
	 */
	public static function activate(): void {
		// Set default options.
		add_option( 'ppguc_usercentrics_policy_id', '' );
		add_option( 'ppguc_usercentrics_display_page', 0 );
		add_option( 'ppguc_usercentrics_onboarding_complete', '0' );

		// Set redirect transient for welcome screen.
		set_transient( 'ppguc_activation_redirect', true, 30 );

		// Flush rewrite rules.
		flush_rewrite_rules();
	}
}

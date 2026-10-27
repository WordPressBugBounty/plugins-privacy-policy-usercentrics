<?php
/**
 * Plugin Deactivator
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
 * Deactivator class
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Core
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class Deactivator {

	/**
	 * Deactivate plugin
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Flush rewrite rules.
		flush_rewrite_rules();

		// Note: Options are kept on deactivation.
		// They will only be deleted on uninstall (see uninstall.php)
	}
}

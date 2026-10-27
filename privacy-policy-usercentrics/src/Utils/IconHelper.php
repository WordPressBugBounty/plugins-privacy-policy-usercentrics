<?php
/**
 * Icon Helper
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Utils
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon helper class
 * Provides utility functions for working with icons
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Utils
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class IconHelper {

	/**
	 * Get icon URL for modals and UI elements
	 *
	 * @param string $icon_name Icon file name (without extension).
	 *
	 * @return string Icon URL.
	 */
	public static function getIcon( string $icon_name ): string {
		return esc_url( PPGUC_PLUGIN_URL . 'assets/images/icons/' . $icon_name . '.svg' );
	}

	/**
	 * Get modal icon URL (alias for getIcon for backwards compatibility)
	 *
	 * @param string $icon_name Icon file name (without extension).
	 *
	 * @return string Icon URL.
	 */
	public static function getModalIcon( string $icon_name ): string {
		return self::getIcon( $icon_name );
	}
}

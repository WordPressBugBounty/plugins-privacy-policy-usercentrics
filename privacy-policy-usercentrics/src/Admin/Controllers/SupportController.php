<?php
/**
 * Support Controller
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
 * Support controller class
 * Handles the support page
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Controllers
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class SupportController extends BaseController {

	/**
	 * Render support page
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! $this->canManageOptions() ) {
			$this->unauthorizedAccess();
		}

		// Load template.
		$this->loadTemplate( 'support-page' );
	}
}

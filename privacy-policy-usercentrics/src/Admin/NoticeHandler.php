<?php
/**
 * Notice Handler
 * Converts WordPress admin notices to toast notifications
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Admin;

/**
 * Notice Handler class
 * Converts WordPress admin notices to toast notifications
 */
class NoticeHandler {

	/**
	 * Collected toasts for current request
	 *
	 * @var array
	 */
	private array $toasts = array();

	/**
	 * Initialize notice handling
	 */
	public function init(): void {
		add_action( 'admin_notices', array( $this, 'startBuffering' ), 0 );
		add_action( 'admin_notices', array( $this, 'endBuffering' ), 9999 );
		add_action( 'admin_footer', array( $this, 'renderToastContainer' ) );
	}

	/**
	 * Start output buffering
	 */
	public function startBuffering(): void {
		if ( ! $this->isPPGPage() ) {
			return;
		}

		ob_start();
	}

	/**
	 * End buffering and capture notices
	 */
	public function endBuffering(): void {
		if ( ! $this->isPPGPage() ) {
			return;
		}

		$buffer = ob_get_clean();

		if ( empty( $buffer ) ) {
			return;
		}

		// Pattern to match WordPress notices.
		$pattern = '/<div\s+class="([^"]*notice[^"]*)"[^>]*>(.*?)<\/div>/is';

		preg_match_all( $pattern, $buffer, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			// No matches found, output buffer as-is.
			echo $buffer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$output = $buffer;

		foreach ( $matches as $match ) {
			$full_match = $match[0];
			$classes    = $match[1];
			$content    = $match[2];

			// Only process PPG notices.
			if ( ! $this->isPPGNotice( '', $full_match ) ) {
				continue;
			}

			// Extract text content.
			$text = wp_strip_all_tags( $content );
			$text = trim( preg_replace( '/\s+/', ' ', $text ) );

			if ( empty( $text ) ) {
				continue;
			}

			// Determine toast type.
			$type = 'info';
			if ( strpos( $classes, 'notice-success' ) !== false || strpos( $classes, 'updated' ) !== false ) {
				$type = 'success';
			} elseif ( strpos( $classes, 'notice-error' ) !== false || strpos( $classes, 'error' ) !== false ) {
				$type = 'error';
			} elseif ( strpos( $classes, 'notice-warning' ) !== false ) {
				$type = 'warning';
			}

			$this->toasts[] = array(
				'type'    => $type,
				'message' => $text,
			);

			// Remove this notice from output (it will be shown as toast instead).
			$output = str_replace( $full_match, '', $output );
		}

		// Output remaining notices (non-PPG ones).
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Check if notice is related to PPG plugin
	 *
	 * @param string $text Notice text content.
	 * @param string $html Notice HTML content.
	 * @return bool
	 */
	private function isPPGNotice( string $text, string $html ): bool {
		// STRICT: Only capture notices that have our plugin's marker class.
		// This ensures we ONLY convert notices explicitly created by our plugin.
		if ( strpos( $html, 'ppg-notice' ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Render toast container and data
	 */
	public function renderToastContainer(): void {
		if ( ! $this->isPPGPage() ) {
			return;
		}
		?>
		<div id="ppg-toast-container"></div>
		<?php
		if ( ! empty( $this->toasts ) ) {
			wp_add_inline_script(
				'ppg-toast',
				sprintf( 'window.ppgucToasts = %s;', wp_json_encode( $this->toasts ) ),
				'before'
			);
		}
	}

	/**
	 * Check if current page is a PPG admin page
	 *
	 * @return bool
	 */
	private function isPPGPage(): bool {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		return strpos( $screen->id, 'ppg-' ) !== false ||
				strpos( $screen->id, 'privacy-policy-usercentrics' ) !== false;
	}
}


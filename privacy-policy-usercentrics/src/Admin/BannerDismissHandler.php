<?php
/**
 * Banner Dismiss Handler
 *
 * Handles AJAX requests to dismiss status banners and persist the choice.
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 */

namespace Usercentrics\PrivacyPolicyGenerator\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BannerDismissHandler
 */
class BannerDismissHandler {

	/**
	 * Initialize the handler
	 */
	public function init(): void {
		add_action( 'wp_ajax_ppguc_dismiss_banner', array( $this, 'handleDismiss' ) );
	}

	/**
	 * Handle banner dismiss AJAX request
	 */
	public function handleDismiss(): void {
		// Verify nonce.
		if ( ! check_ajax_referer( 'ppguc_banner_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}

		// Get banner ID.
		$banner_id = isset( $_POST['banner_id'] ) ? sanitize_text_field( wp_unslash( $_POST['banner_id'] ) ) : '';

		if ( empty( $banner_id ) ) {
			wp_send_json_error( array( 'message' => 'Invalid banner ID' ) );
		}

		// Save to user meta with timestamp (for 7-day expiration).
		$user_id  = get_current_user_id();
		$meta_key = 'ppguc_dismissed_' . $banner_id . '_banner';

		$result = update_user_meta( $user_id, $meta_key, time() );

		if ( false === $result ) {
			wp_send_json_error( array( 'message' => 'Failed to save preference' ) );
		}

		wp_send_json_success( array( 'message' => 'Banner dismissed successfully' ) );
	}
}

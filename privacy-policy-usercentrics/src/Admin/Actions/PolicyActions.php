<?php
/**
 * Policy Actions
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
 * Policy actions class
 * Handles policy-related actions (connect, disconnect, edit)
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin\Actions
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class PolicyActions {

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
	 * Initialize AJAX hooks
	 *
	 * @return void
	 */
	public function initAjax(): void {
		add_action( 'wp_ajax_ppguc_validate_policy_id', array( $this, 'ajaxValidatePolicyId' ) );
	}

	/**
	 * AJAX handler for Policy ID validation
	 *
	 * @return void
	 */
	public function ajaxValidatePolicyId(): void {
		// Check nonce.
		check_ajax_referer( 'ppguc_ajax_nonce', 'nonce' );

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Permission denied.', 'privacy-policy-usercentrics' ) ),
				403
			);
		}

		// Get Policy ID.
		$policy_id = isset( $_POST['policy_id'] ) ? sanitize_text_field( wp_unslash( $_POST['policy_id'] ) ) : '';

		// Validate UUID format.
		$sanitized_id = $this->settings_service->sanitizePolicyId( $policy_id );
		if ( empty( $sanitized_id ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid Policy ID format.', 'privacy-policy-usercentrics' ) ),
				400
			);
		}

		// Validate with API.
		$result = $this->validatePolicyIdWithApi( $sanitized_id );

		if ( $result['valid'] ) {
			wp_send_json_success(
				array(
					'message'   => $result['message'],
					'policy_id' => $sanitized_id,
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => $result['message'],
				),
				404
			);
		}
	}

	/**
	 * Usercentrics API base URL
	 */
	private const API_BASE_URL = 'https://policygenerator.usercentrics.eu/api/embedding';

	/**
	 * Handle connect policy action
	 *
	 * @return void
	 */
	public function connect(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified below.
		if ( ! $this->verifyNonce( 'ppguc_connect_nonce', 'ppguc_connect_policy' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'privacy-policy-usercentrics' ) );
		}

		$policy_id = isset( $_POST['ppguc_policy_id'] )
			? sanitize_text_field( wp_unslash( $_POST['ppguc_policy_id'] ) )
			: '';

		// Check if already validated via AJAX.
		$already_validated = isset( $_POST['ppguc_validated'] ) && '1' === $_POST['ppguc_validated'];
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// Validate UUID format.
		$sanitized_id = $this->settings_service->sanitizePolicyId( $policy_id );

		if ( empty( $sanitized_id ) ) {
			$this->setNotice(
				__( 'Invalid Policy ID format. Please enter a valid UUID.', 'privacy-policy-usercentrics' ),
				'error'
			);
			return;
		}

		// Only validate with API if not already validated via AJAX.
		if ( ! $already_validated ) {
			$validation_result = $this->validatePolicyIdWithApi( $sanitized_id );

			if ( ! $validation_result['valid'] ) {
				$this->setNotice(
					$validation_result['message'],
					'error'
				);
				return;
			}
		}

		// Save the validated Policy ID.
		$this->settings_service->updateOption( 'ppguc_usercentrics_policy_id', $sanitized_id );

		$this->setNotice(
			__( 'Privacy Policy connected successfully!', 'privacy-policy-usercentrics' ),
			'success'
		);
	}

	/**
	 * Validate Policy ID with Usercentrics API
	 *
	 * @param string $policy_id The Policy ID to validate.
	 *
	 * @return array Array with 'valid' (bool) and 'message' (string) keys.
	 */
	private function validatePolicyIdWithApi( string $policy_id ): array {
		$api_url  = self::API_BASE_URL . '?id=' . $policy_id;
		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'    => 10,
				'user-agent' => 'WordPress/PPG-Usercentrics-Plugin',
			)
		);

		// Check for errors in the request.
		if ( is_wp_error( $response ) ) {
			return array(
				'valid'   => false,
				'message' => sprintf(
					/* translators: %s: Error message */
					__( 'Unable to validate Policy ID: %s', 'privacy-policy-usercentrics' ),
					$response->get_error_message()
				),
			);
		}

		// Get response code.
		$response_code = wp_remote_retrieve_response_code( $response );

		// Check if Policy ID is valid (200 OK).
		if ( 200 === $response_code ) {
			return array(
				'valid'   => true,
				'message' => __( 'Policy ID is valid!', 'privacy-policy-usercentrics' ),
			);
		} elseif ( 404 === $response_code ) {
			return array(
				'valid'   => false,
				'message' => __( 'Policy ID not found. Please check your Policy ID and try again.', 'privacy-policy-usercentrics' ),
			);
		} else {
			return array(
				'valid'   => false,
				'message' => sprintf(
					/* translators: %d: HTTP status code */
					__( 'Validation failed with status code: %d', 'privacy-policy-usercentrics' ),
					$response_code
				),
			);
		}
	}


	/**
	 * Disconnect policy
	 *
	 * @return void
	 */
	public function disconnect(): void {
		if ( ! $this->verifyNonce( 'ppguc_disconnect_nonce', 'ppguc_disconnect_action' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'privacy-policy-usercentrics' ) );
		}

		// Clear all plugin settings.
		$this->settings_service->updateOption( 'ppguc_usercentrics_policy_id', '' );
		$this->settings_service->updateOption( 'ppguc_usercentrics_display_page', 0 );

		$this->setNotice(
			__( 'Privacy Policy disconnected successfully.', 'privacy-policy-usercentrics' ),
			'warning'
		);

		// Just refresh the page instead of redirecting.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : admin_url( 'admin.php?page=privacy-policy-usercentrics' ) );
		exit;
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

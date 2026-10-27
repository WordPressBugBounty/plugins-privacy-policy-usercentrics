<?php
/**
 * Admin Header Component
 *
 * Simple, consistent header with Usercentrics logo
 * Used across all admin pages
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ppg-admin-header">
	<div class="ppg-logo-container">
		<img src="<?php echo esc_url( PPGUC_IMAGES_URL . 'logos/uc-logo.svg' ); ?>" 
			alt="<?php esc_attr_e( 'Usercentrics', 'privacy-policy-usercentrics' ); ?>" 
			class="ppg-uc-logo">
	</div>
</div>


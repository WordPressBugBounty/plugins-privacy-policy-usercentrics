<?php
/**
 * CMP Promotion Banner Component
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Determine UTM parameters based on current page.
$current_screen = get_current_screen();
$ppguc_utm_content    = 'dashboard_banner'; // Default.

if ( $current_screen ) {
	switch ( $current_screen->id ) {
		case 'toplevel_page_privacy-policy-usercentrics': // Dashboard.
			$ppguc_utm_content = 'dashboard_banner';
			break;
		case 'privacy-policy-usercentrics-generator_page_ppg-support': // Support page.
			$ppguc_utm_content = 'support_banner';
			break;
		case 'privacy-policy-usercentrics-generator_page_ppg-settings': // Settings page.
			$ppguc_utm_content = 'settings_banner';
			break;
		default:
			$ppguc_utm_content = 'plugin_banner';
			break;
	}
}

// Build complete URL with UTM parameters.
$ppguc_cookiebot_url = add_query_arg(
	array(
		'utm_source'   => 'wordpress',
		'utm_medium'   => 'referral',
		'utm_campaign' => 'cmp_banner_clicks',
		'utm_content'  => $ppguc_utm_content,
	),
	'https://usercentrics.com/integrations/wordpress/'
);
?>

<div class="ppg-cmp-banner">
	<div class="ppg-cmp-icon" aria-hidden="true">
		<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="#000">
			<g clip-path="url(#clip0_6481_31815)">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M5.8616 5.8052V19.8114C5.8616 27.5363 12.2039 33.8176 20.0039 33.8176C27.8038 33.8176 34.1462 27.5363 34.1462 19.8114V5.8052H5.8616ZM20 39.6228C8.97638 39.6207 0 30.7371 0 19.8114V0H40V19.8114C40 30.7294 31.0314 39.6207 20 39.6228ZM22.6894 26.9486L22.6888 26.9499H15.92L15.9312 26.9266L9.98921 16.5821H16.7657L19.0106 20.4902L23.7122 10.6631H30.4888L22.7035 26.9192L22.7204 26.9486H22.6894Z" fill="#000" />
			</g>
			<defs>
				<clipPath id="clip0_6481_31815">
					<rect width="40" height="40" fill="#000" />
				</clipPath>
			</defs>
		</svg>
	</div>

	<div class="ppg-cmp-content">
		<h3><?php esc_html_e( 'Take privacy compliance one step further', 'privacy-policy-usercentrics' ); ?></h3>
		<p><?php echo wp_kses_post( __( 'With our automatic <strong>cookie banner plugin</strong> for GDPR/CCRA and Google Consent Mode your can scan, block, and signal cookie consent to complete your privacy toolkit.', 'privacy-policy-usercentrics' ) ); ?></p>

		<div class="ppg-cmp-tags">
			<span class="ppg-cmp-tag">
				<strong><?php esc_html_e( 'Free Plan available', 'privacy-policy-usercentrics' ); ?></strong>
			</span>
			<span class="ppg-cmp-tag">
				<strong><?php echo esc_html( '100,000' ); ?></strong> <?php esc_html_e( 'active installations', 'privacy-policy-usercentrics' ); ?>
			</span>
		</div>
	</div>

	<div class="ppg-cmp-cta">
		<a href="<?php echo esc_url( $ppguc_cookiebot_url ); ?>" class="ppg-cmp-button" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Install Plugin', 'privacy-policy-usercentrics' ); ?>
		</a>
	</div>
</div>

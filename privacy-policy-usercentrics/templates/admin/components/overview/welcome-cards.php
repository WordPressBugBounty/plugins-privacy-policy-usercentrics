<?php
/**
 * Welcome Cards Component
 * Two-column cards for account creation and video guide
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 * @var string $ppguc_utm_params UTM tracking parameters (optional, passed from parent)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ensure UTM params are available.
if ( ! isset( $ppguc_utm_params ) ) {
	$ppguc_utm_params = 'utm_source=WordPress&utm_medium=plugin&utm_campaign=ppg';
}
?>

<div class="ppg-welcome-cards">
	<!-- Left card: Generate Privacy Policy -->
	<div class="ppg-welcome-card">
		<h2><?php esc_html_e( 'Generate your Privacy Policy in 3 minutes', 'privacy-policy-usercentrics' ); ?></h2>
		<div class="ppg-mb-2lg">
			<p><?php esc_html_e( 'Our automated process makes it simple: specify your needs, and our generator creates a future-proof Privacy Policy that keeps your website compliant, protects you from legal risks, and strengthens user confidence.', 'privacy-policy-usercentrics' ); ?></p>
		</div>

		<a href="<?php echo esc_url( 'https://policygenerator.usercentrics.eu/?' . $ppguc_utm_params ); ?>"
			class="ppg-button-primary"
			data-ppg-action="create-account-redirect"
			data-redirect-url="<?php echo esc_url( admin_url( 'admin.php?page=ppg-settings' ) ); ?>"
			target="_blank"
			rel="noopener noreferrer">
			<?php esc_html_e( 'Create an account', 'privacy-policy-usercentrics' ); ?>
		</a>

		<p class="ppg-account-link ppg-mt-md">
			<?php
			printf(
				/* translators: %s: Settings page link */
				wp_kses_post( __( 'Already have an account? <a href="%s">Connect it</a>', 'privacy-policy-usercentrics' ) ),
				esc_url( admin_url( 'admin.php?page=ppg-settings' ) )
			);
			?>
		</p>
	</div>

	<!-- Right card: How to install -->
	<div class="ppg-welcome-card">
		<?php
		// Video card component.
	$ppguc_video_url   = 'https://www.youtube.com/embed/GTH4VrxhSe4';
	$ppguc_video_title = __( 'Usercentrics Cookiebot WordPress Plugin Installation Guide', 'privacy-policy-usercentrics' );
	$ppguc_heading     = __( 'How it works', 'privacy-policy-usercentrics' );
	$ppguc_description = __( 'Need a hand? This short video walks you through adding the plugin and setting up your first Privacy Policy.', 'privacy-policy-usercentrics' );

	require PPGUC_PLUGIN_DIR . 'templates/admin/components/video-card.php';
		?>
	</div>
</div>


<?php
/**
 * Overview State: Not Connected
 * Welcome screen when no policy is connected
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 * @var string $ppguc_utm_params UTM tracking parameters
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ppg-welcome-wrapper">
	<!-- Welcome heading -->
	<div class="ppg-welcome-title">
		<h1><?php esc_html_e( 'Welcome 👋!', 'privacy-policy-usercentrics' ); ?></h1>
	</div>

	<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/overview/welcome-cards.php'; ?>
</div>


<?php
/**
 * Overview State: Connected but Not Published
 * Status banner when policy is connected but page not published
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 * @var string $ppguc_utm_params UTM tracking parameters
 */

use Usercentrics\PrivacyPolicyGenerator\Utils\IconHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ppg-state-connected-not-published">
	<div class="ppg-status-banner ppg-status-banner-warning">
		<div class="ppg-status-banner-icon">
			<img src="<?php echo esc_url( IconHelper::getIcon( 'stop-watch' ) ); ?>" alt="">
		</div>
		<div class="ppg-status-banner-content">
			<strong><?php esc_html_e( 'You\'re almost there!', 'privacy-policy-usercentrics' ); ?></strong>
			<?php esc_html_e( 'Your privacy policy ID is connected, but you still need to publish your page for it to be live.', 'privacy-policy-usercentrics' ); ?>
		</div>
		<div class="ppg-status-banner-action">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ppg-settings' ) ); ?>" class="ppg-button ppg-button-ghost ppg-button-text-sm">
				<?php esc_html_e( 'Finish Setup', 'privacy-policy-usercentrics' ); ?>
			</a>
		</div>
	</div>

	<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/overview/welcome-cards.php'; ?>
</div>


<?php
/**
 * Overview State: Published
 * Success banner when policy is live
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 * @var string $ppguc_utm_params UTM tracking parameters
 * @var int $display_page Display page ID
 * @var string $page_url Page URL
 */

use Usercentrics\PrivacyPolicyGenerator\Utils\IconHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ppg-state-published">
	<?php
	// Check if banner was dismissed within the last 7 days.
	$ppguc_dismissed_time = get_user_meta( get_current_user_id(), 'ppguc_dismissed_success_banner', true );
	$ppguc_seven_days     = 0 * DAY_IN_SECONDS;
	$ppguc_should_hide    = $ppguc_dismissed_time && ( time() - $ppguc_dismissed_time < $ppguc_seven_days );
	?>
	<div class="ppg-status-banner ppg-status-banner-success" id="ppg-success-banner" style="<?php echo $ppguc_should_hide ? 'display: none;' : ''; ?>">
		<div class="ppg-status-banner-icon">
			<img src="<?php echo esc_url( IconHelper::getIcon( 'checkmark' ) ); ?>" alt="">
		</div>
		<div class="ppg-status-banner-content">
			<strong><?php esc_html_e( 'Congrats! You\'re All Set', 'privacy-policy-usercentrics' ); ?></strong>
			<?php esc_html_e( 'Your privacy policy is live and helping you stay compliant. Want more?', 'privacy-policy-usercentrics' ); ?>
		</div>
		<?php
		$ppguc_cookiebot_url = 'https://usercentrics.com/integrations/wordpress/';
		$ppguc_cookiebot_utm = 'utm_source=wordpress&utm_medium=referral&utm_campaign=cmp_banner_clicks&utm_content=discover_cmp_banner';
		?>
		<div class="ppg-status-banner-action">
			<a href="<?php echo esc_url( $ppguc_cookiebot_url . '?' . $ppguc_cookiebot_utm ); ?>" target="_blank" rel="noopener noreferrer" class="ppg-button ppg-button-ghost ppg-button-text-sm">
				<?php esc_html_e( 'Discover our Consent Management Platform', 'privacy-policy-usercentrics' ); ?>
			</a>
		</div>
		<button type="button" class="ppg-status-banner-close" aria-label="<?php esc_attr_e( 'Dismiss', 'privacy-policy-usercentrics' ); ?>">
			<span class="dashicons dashicons-no-alt"></span>
		</button>
	</div>

	<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/overview/published-cards.php'; ?>
</div>


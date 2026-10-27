<?php

/**
 * Published State Cards
 * Two-column cards for published state with quick actions and video
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 * @var int $display_page Display page ID
 * @var string $page_url Page URL
 */

if (! defined('ABSPATH')) {
	exit;
}

use Usercentrics\PrivacyPolicyGenerator\Utils\IconHelper;
?>

<div class="ppg-welcome-cards">
	<!-- Left card: Quick Actions -->
	<div class="ppg-welcome-card">
		<h2><?php esc_html_e('Your Privacy Policy is Live! 🎉', 'privacy-policy-usercentrics'); ?></h2>
		<p><?php esc_html_e('Great job! Your privacy policy is now published and helping you stay compliant.', 'privacy-policy-usercentrics'); ?></p>

		<div class="ppg-published-quick-actions">
			<div class="ppg-quick-action-primary">
				<?php if (! empty($page_url)) : ?>
					<a href="<?php echo esc_url($page_url); ?>"
						class="ppg-button-primary"
						target="_blank"
						rel="noopener noreferrer">
						<span class="dashicons dashicons-external"></span>
						<?php esc_html_e('View Live Page', 'privacy-policy-usercentrics'); ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="ppg-quick-actions-grid">

				<a href="https://policygenerator.usercentrics.eu/?utm_source=wordpress&utm_medium=plugin&utm_campaign=ppg&utm_content=published_dashboard"
					class="ppg-quick-action-link"
					target="_blank"
					rel="noopener noreferrer">
					<span class="dashicons dashicons-edit"></span>
					<span><?php esc_html_e('Edit Policy', 'privacy-policy-usercentrics'); ?></span>
				</a>

				<a href="<?php echo esc_url(admin_url('admin.php?page=ppg-settings')); ?>"
					class="ppg-quick-action-link">
					<span class="dashicons dashicons-admin-settings"></span>
					<span><?php esc_html_e('Settings', 'privacy-policy-usercentrics'); ?></span>
				</a>

				<a href="<?php echo esc_url(admin_url('admin.php?page=ppg-support')); ?>"
					class="ppg-quick-action-link">
					<img src="<?php echo esc_url(IconHelper::getIcon('info-icon')); ?>" alt="<?php esc_attr_e('Support', 'privacy-policy-usercentrics'); ?>" width="20" height="20">
					<span><?php esc_html_e('Support', 'privacy-policy-usercentrics'); ?></span>
				</a>
			</div>
		</div>
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
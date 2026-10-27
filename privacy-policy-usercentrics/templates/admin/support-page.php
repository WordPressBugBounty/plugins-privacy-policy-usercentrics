<?php

/**
 * Support Page Template
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 */

if (! defined('ABSPATH')) {
	exit;
}

use Usercentrics\PrivacyPolicyGenerator\Utils\IconHelper;
?>

<div class="ppg-support-page">
	<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/header.php'; ?>
	<div class="ppg-overview-container">
		<div class="ppg-welcome-wrapper">
			<div class="ppg-welcome-body">

				<!-- Hero Header -->
				<h1 class="ppg-page-title"><?php esc_html_e('How can we help you?', 'privacy-policy-usercentrics'); ?></h1>

				<!-- Support Hero Section -->
				<div class="ppg-support-hero-section">
					<!-- Left: Support info -->
					<div class="ppg-support-hero-content">
						<p class="ppg-support-hero-description">
							<?php esc_html_e('If you need further assistance, click the Visit Support Center button to create a support request.', 'privacy-policy-usercentrics'); ?>
						</p>
						<p class="ppg-support-hero-description">
							<?php esc_html_e('To help us respond faster, please scroll down to the System Information section, copy all the details, and paste them into your support request.', 'privacy-policy-usercentrics'); ?>
						</p>
						<p class="ppg-support-hero-description">
							<?php esc_html_e('We’ll get back to you as soon as possible.', 'privacy-policy-usercentrics'); ?>
						</p>

						<?php
						$ppguc_support_utm = 'utm_source=WordPress&utm_medium=plugin&utm_campaign=ppg_support';
						?>
						<a href="<?php echo esc_url('https://support.usercentrics.com/hc/en-us/articles/18866385218716-privacy-policy-usercentrics-Generator/?' . $ppguc_support_utm); ?>"
							class="ppg-button ppg-button-primary ppg-button-large"
							target="_blank"
							rel="noopener noreferrer">
							<?php esc_html_e('Visit Support Center', 'privacy-policy-usercentrics'); ?>
						</a>
					</div>

					<!-- Right: Video guide card -->
					<div class="ppg-support-hero-video">
						<?php
						// Video card component.
					$ppguc_video_url   = 'https://www.youtube.com/embed/GTH4VrxhSe4';
					$ppguc_video_title = __('Usercentrics WordPress Plugin Guide', 'privacy-policy-usercentrics');
					$ppguc_heading     = __('Video Guide', 'privacy-policy-usercentrics');
					$ppguc_description = '';

					require PPGUC_PLUGIN_DIR . 'templates/admin/components/video-card.php';
						?>
					</div>
				</div>

				<div class="ppg-support-container">
					<!-- FAQ Section -->
					<div class="ppg-faq-section">
						<div class="ppg-section-header">
							<div class="ppg-section-header-icon">
								<img src="<?php echo esc_url(IconHelper::getIcon('info-icon')); ?>" alt="<?php esc_attr_e('Information', 'privacy-policy-usercentrics'); ?>" width="24" height="24">
							</div>
							<div class="ppg-section-header-content">
								<h2><?php esc_html_e('Frequently Asked Questions', 'privacy-policy-usercentrics'); ?></h2>
								<p><?php esc_html_e('Quick answers to questions you may have', 'privacy-policy-usercentrics'); ?></p>
							</div>
						</div>

						<div class="ppg-faq-accordion">
							<div class="ppg-faq-item">
								<button class="ppg-faq-trigger" type="button" aria-expanded="false">
									<span class="ppg-faq-question-text"><?php esc_html_e( 'Where do I find my Policy ID?', 'privacy-policy-usercentrics' ); ?></span>
									<span class="ppg-faq-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
											<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
								</button>
								<div class="ppg-faq-panel">
									<div class="ppg-faq-content">
										<p><?php esc_html_e( 'After setting up your privacy policy in your Usercentrics dashboard, you\'ll find your Policy ID under your generated policy details. It looks like this: 2b3a23a0-94de-49c1-8bb2-0a4a842dd425. Copy and paste it into the plugin settings in WordPress.', 'privacy-policy-usercentrics' ); ?></p>
									</div>
								</div>
							</div>

							<div class="ppg-faq-item">
								<button class="ppg-faq-trigger" type="button" aria-expanded="false">
									<span class="ppg-faq-question-text"><?php esc_html_e( 'Can I customize the privacy policy text?', 'privacy-policy-usercentrics' ); ?></span>
									<span class="ppg-faq-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
											<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
								</button>
								<div class="ppg-faq-panel">
									<div class="ppg-faq-content">
										<p><?php esc_html_e( 'Yes, customization happens directly in your Usercentrics dashboard. Any edits you make — such as company name, contact info, or policy sections — are instantly reflected on your WordPress site. You always stay in full control of your content.', 'privacy-policy-usercentrics' ); ?></p>
									</div>
								</div>
							</div>

							<div class="ppg-faq-item">
								<button class="ppg-faq-trigger" type="button" aria-expanded="false">
									<span class="ppg-faq-question-text"><?php esc_html_e( 'Does this plugin work with page builders?', 'privacy-policy-usercentrics' ); ?></span>
									<span class="ppg-faq-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
											<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
								</button>
								<div class="ppg-faq-panel">
									<div class="ppg-faq-content">
										<p><?php esc_html_e( 'Absolutely. It\'s fully compatible with all major WordPress page builders, including Elementor, Divi, Gutenberg, Beaver Builder, and others. No extra configuration needed.', 'privacy-policy-usercentrics' ); ?></p>
									</div>
								</div>
							</div>

							<div class="ppg-faq-item">
								<button class="ppg-faq-trigger" type="button" aria-expanded="false">
									<span class="ppg-faq-question-text"><?php esc_html_e( 'How is this different from other privacy policy plugins?', 'privacy-policy-usercentrics' ); ?></span>
									<span class="ppg-faq-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
											<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
								</button>
								<div class="ppg-faq-panel">
									<div class="ppg-faq-content">
										<p><?php esc_html_e( 'Most plugins create a static policy you have to update manually. This plugin connects directly to Usercentrics — a professional consent and privacy platform — so your policy updates automatically whenever laws change. You never have to worry about outdated text.', 'privacy-policy-usercentrics' ); ?></p>
									</div>
								</div>
							</div>

							<div class="ppg-faq-item">
								<button class="ppg-faq-trigger" type="button" aria-expanded="false">
									<span class="ppg-faq-question-text"><?php esc_html_e( 'Will this slow down my website?', 'privacy-policy-usercentrics' ); ?></span>
									<span class="ppg-faq-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
											<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
								</button>
								<div class="ppg-faq-panel">
									<div class="ppg-faq-content">
										<p><?php esc_html_e( 'No. The plugin is lightweight (under 100KB) and built with performance in mind. It loads policy content asynchronously, so it won\'t affect your site\'s speed or Core Web Vitals.', 'privacy-policy-usercentrics' ); ?></p>
									</div>
								</div>
							</div>

							<div class="ppg-faq-item">
								<button class="ppg-faq-trigger" type="button" aria-expanded="false">
									<span class="ppg-faq-question-text"><?php esc_html_e( 'What languages are supported?', 'privacy-policy-usercentrics' ); ?></span>
									<span class="ppg-faq-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
											<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
								</button>
								<div class="ppg-faq-panel">
									<div class="ppg-faq-content">
										<p><?php esc_html_e( 'Your privacy policy can be generated in multiple languages — English, German, Italian, and Dutch. The plugin automatically detects the visitor\'s browser language and displays the correct version when available.', 'privacy-policy-usercentrics' ); ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- System Information -->
					<div class="ppg-system-section">
						<div class="ppg-section-header">
							<div class="ppg-section-header-icon">

								<img src="<?php echo esc_url(IconHelper::getIcon('system-icon')); ?>" alt="<?php esc_attr_e('Information', 'privacy-policy-usercentrics'); ?>" width="24" height="24">

							</div>
							<div class="ppg-section-header-content">
								<h2><?php esc_html_e('System Information', 'privacy-policy-usercentrics'); ?></h2>
								<p><?php esc_html_e('Technical details for troubleshooting', 'privacy-policy-usercentrics'); ?></p>
							</div>
							<button type="button" class="ppg-copy-btn ppg-button ppg-button-primary" data-copy-target=".ppg-system-info-grid">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
									<path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z" fill="currentColor" />
								</svg>
								<span><?php esc_html_e('Copy Info', 'privacy-policy-usercentrics'); ?></span>
							</button>
						</div>

						<?php
						$ppguc_display_page_id = get_option('ppguc_usercentrics_display_page');
						$ppguc_display_page    = $ppguc_display_page_id ? get_post($ppguc_display_page_id) : null;
						?>

						<div class="ppg-system-info-grid">
							<div class="ppg-system-info-item">
								<div class="ppg-system-info-label"><?php esc_html_e('Plugin Version', 'privacy-policy-usercentrics'); ?></div>
								<div class="ppg-system-info-value"><?php echo esc_html(PPGUC_VERSION); ?></div>
							</div>

							<div class="ppg-system-info-item">
								<div class="ppg-system-info-label"><?php esc_html_e('WordPress Version', 'privacy-policy-usercentrics'); ?></div>
								<div class="ppg-system-info-value"><?php echo esc_html(get_bloginfo('version')); ?></div>
							</div>

							<div class="ppg-system-info-item">
								<div class="ppg-system-info-label"><?php esc_html_e('PHP Version', 'privacy-policy-usercentrics'); ?></div>
								<div class="ppg-system-info-value"><?php echo esc_html(phpversion()); ?></div>
							</div>

							<div class="ppg-system-info-item">
								<div class="ppg-system-info-label"><?php esc_html_e('Active Theme', 'privacy-policy-usercentrics'); ?></div>
								<div class="ppg-system-info-value"><?php echo esc_html(wp_get_theme()->get('Name')); ?></div>
							</div>

							<div class="ppg-system-info-item ppg-info-span-2">
								<div class="ppg-system-info-label"><?php esc_html_e('Policy ID', 'privacy-policy-usercentrics'); ?></div>
								<div class="ppg-system-info-value ppg-monospace">
									<?php
									$ppguc_policy_id = get_option('ppguc_usercentrics_policy_id');
									echo $ppguc_policy_id ? esc_html($ppguc_policy_id) : '<span class="ppg-value-empty">' . esc_html__('Not configured', 'privacy-policy-usercentrics') . '</span>';
									?>
								</div>
							</div>

							<div class="ppg-system-info-item">
								<div class="ppg-system-info-label"><?php esc_html_e('Display Page', 'privacy-policy-usercentrics'); ?></div>
								<div class="ppg-system-info-value"
									<?php if ($ppguc_display_page) : ?>
									data-page-title="<?php echo esc_attr($ppguc_display_page->post_title); ?>"
									data-page-status="<?php echo esc_attr($ppguc_display_page->post_status); ?>"
									<?php endif; ?>>
									<?php if ($ppguc_display_page) : ?>
										<div class="ppg-page-info">
											<?php echo esc_html($ppguc_display_page->post_title); ?>
											<span class="ppg-status-pill ppg-status-<?php echo esc_attr($ppguc_display_page->post_status); ?>">
												<?php echo esc_html(ucfirst($ppguc_display_page->post_status)); ?>
											</span>
										</div>
									<?php else : ?>
										<span class="ppg-value-empty"><?php esc_html_e('No page selected', 'privacy-policy-usercentrics'); ?></span>
									<?php endif; ?>
								</div>
							</div>

							<div class="ppg-system-info-item">
								<div class="ppg-system-info-label"><?php esc_html_e('Page URL', 'privacy-policy-usercentrics'); ?></div>
								<div class="ppg-system-info-value">
									<?php if ($ppguc_display_page) : ?>
										<a href="<?php echo esc_url(get_permalink($ppguc_display_page_id)); ?>" target="_blank" rel="noopener noreferrer" class="ppg-external-link">
											<span><?php esc_html_e('View Page', 'privacy-policy-usercentrics'); ?></span>
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none">
												<path d="M19 19H5V5H12V3H5C3.89 3 3 3.9 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V12H19V19ZM14 3V5H17.59L7.76 14.83L9.17 16.24L19 6.41V10H21V3H14Z" fill="currentColor" />
											</svg>
										</a>
									<?php else : ?>
										<span class="ppg-value-empty"><?php esc_html_e('No page selected', 'privacy-policy-usercentrics'); ?></span>
									<?php endif; ?>
								</div>
							</div>

							<div class="ppg-system-info-item ppg-info-span-2">
								<div class="ppg-system-info-label"><?php esc_html_e('Site URL', 'privacy-policy-usercentrics'); ?></div>
								<div class="ppg-system-info-value">
									<a href="<?php echo esc_url(home_url()); ?>" target="_blank" rel="noopener noreferrer" class="ppg-external-link">
										<span><?php echo esc_html(home_url()); ?></span>
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none">
											<path d="M19 19H5V5H12V3H5C3.89 3 3 3.9 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V12H19V19ZM14 3V5H17.59L7.76 14.83L9.17 16.24L19 6.41V10H21V3H14Z" fill="currentColor" />
										</svg>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div><!-- .ppg-support-container -->
			</div><!-- .ppg-welcome-body -->
		</div><!-- .ppg-welcome-wrapper -->

		<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/cmp-banner.php'; ?>

	</div><!-- .ppg-overview-container -->
</div><!-- .ppg-support-page -->
<?php
/**
 * Addons page template
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 *
 * @var array $cookiebot_plugin Cookiebot plugin information
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ppg-addons-page">
	<?php require_once PPGUC_PLUGIN_DIR . 'templates/admin/components/header.php'; ?>

	<div class="ppg-overview-container">
		<h1 class="ppg-page-title"><?php esc_html_e( 'Addons', 'privacy-policy-usercentrics' ); ?></h1>
		
		<div class="ppg-addons-intro">
			<p><?php esc_html_e( 'Enhance your privacy compliance with recommended plugins from Usercentrics.', 'privacy-policy-usercentrics' ); ?></p>
		</div>

		<div class="ppg-addons-grid">
			<?php if ( $cookiebot_plugin ) : ?>
				<div class="ppg-addon-card" data-plugin-slug="<?php echo esc_attr( $cookiebot_plugin['slug'] ); ?>">
					<div class="ppg-addon-card-header">
						<div class="ppg-addon-icon">
							<img src="<?php echo esc_url( $cookiebot_plugin['icon'] ); ?>" alt="<?php echo esc_attr( $cookiebot_plugin['name'] ); ?>">
						</div>
						<div class="ppg-addon-info">
							<h3 class="ppg-addon-name"><?php echo esc_html( $cookiebot_plugin['name'] ); ?></h3>
							<p class="ppg-addon-author">
								<?php
								printf(
									/* translators: %s: plugin author */
									esc_html__( 'By %s', 'privacy-policy-usercentrics' ),
									'<strong>Usercentrics</strong>'
								);
								?>
							</p>
						</div>
					</div>

					<div class="ppg-addon-card-body">
						<p class="ppg-addon-description"><?php echo esc_html( $cookiebot_plugin['description'] ); ?></p>

						<?php if ( ! empty( $cookiebot_plugin['rating'] ) && $cookiebot_plugin['rating'] > 0 ) : ?>
							<div class="ppg-addon-meta">
								<div class="ppg-addon-rating">
									<span class="ppg-addon-stars">
										<?php
										$ppguc_rating_percentage = ( $cookiebot_plugin['rating'] / 100 ) * 5;
										$ppguc_full_stars        = floor( $ppguc_rating_percentage );
										$ppguc_half_star         = ( $ppguc_rating_percentage - $ppguc_full_stars ) >= 0.5;

										for ( $ppguc_i = 0; $ppguc_i < 5; $ppguc_i++ ) {
											if ( $ppguc_i < $ppguc_full_stars ) {
												echo '<span class="star-full">★</span>';
											} elseif ( $ppguc_i === $ppguc_full_stars && $ppguc_half_star ) {
												echo '<span class="star-half">★</span>';
											} else {
												echo '<span class="star-empty">☆</span>';
											}
										}
										?>
									</span>
									<span class="ppg-addon-rating-text">
									<?php
									printf(
										/* translators: 1: rating out of 5, 2: number of ratings */
										esc_html__( '%1$s (%2$s ratings)', 'privacy-policy-usercentrics' ),
										esc_html( number_format( $ppguc_rating_percentage, 1 ) ),
										esc_html( number_format_i18n( $cookiebot_plugin['num_ratings'] ) )
									);
									?>
								</span>
							</div>
							<?php if ( ! empty( $cookiebot_plugin['active_installs'] ) ) : ?>
								<div class="ppg-addon-installs">
									<?php
									printf(
										/* translators: %s: number of active installations */
										esc_html__( '%s+ Active Installations', 'privacy-policy-usercentrics' ),
										esc_html( number_format_i18n( $cookiebot_plugin['active_installs'] ) )
									);
									?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

				<div class="ppg-addon-card-footer">
					<?php
					$ppguc_addon_status = $cookiebot_plugin['status'];

					if ( 'active' === $ppguc_addon_status ) :
						?>
							<button class="ppg-addon-btn ppg-addon-btn-active" disabled>
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M13.3333 4L6 11.3333L2.66667 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php esc_html_e( 'Active', 'privacy-policy-usercentrics' ); ?>
						</button>
					<?php elseif ( 'installed' === $ppguc_addon_status ) : ?>
						<button class="ppg-addon-btn ppg-addon-btn-primary ppg-addon-btn-activate" data-action="activate">
							<?php esc_html_e( 'Activate', 'privacy-policy-usercentrics' ); ?>
						</button>
					<?php else : ?>
							<button class="ppg-addon-btn ppg-addon-btn-primary ppg-addon-btn-install" data-action="install">
								<?php esc_html_e( 'Install Plugin', 'privacy-policy-usercentrics' ); ?>
							</button>
						<?php endif; ?>
                         <?php 
						 $ppguc_cookiebot_url = 'https://usercentrics.com/integrations/wordpress/';
						 $ppguc_cookiebot_utm = 'utm_source=WordPress&utm_medium=plugin&utm_campaign=ppg_addons_page';
						 ?>
						<a href="<?php echo esc_url( $ppguc_cookiebot_url . '?' . $ppguc_cookiebot_utm ); ?>" class="ppg-addon-link" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'More Details', 'privacy-policy-usercentrics' ); ?>
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9 6.5L9 10C9 10.2652 8.89464 10.5196 8.70711 10.7071C8.51957 10.8946 8.26522 11 8 11L2 11C1.73478 11 1.48043 10.8946 1.29289 10.7071C1.10536 10.5196 1 10.2652 1 10L1 4C1 3.73478 1.10536 3.48043 1.29289 3.29289C1.48043 3.10536 1.73478 3 2 3L5.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M7.5 1H11V4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M5 7L11 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
					</div>

					<div class="ppg-addon-status-message" style="display: none;"></div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php wp_nonce_field( 'ppg-addons-nonce', 'ppguc_addons_nonce' ); ?>

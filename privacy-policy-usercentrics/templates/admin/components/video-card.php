<?php
/**
 * Video Card Component
 * Reusable video embed card for YouTube videos with lazy loading
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 * @var string $ppguc_video_url YouTube embed URL (required)
 * @var string $ppguc_video_title Video title for accessibility (required)
 * @var string $ppguc_heading Optional heading above the video
 * @var string $ppguc_description Optional description text
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Default values.
$ppguc_video_url   = $ppguc_video_url ?? 'https://www.youtube.com/embed/GTH4VrxhSe4';
$ppguc_video_title = $ppguc_video_title ?? __( 'Video Guide', 'privacy-policy-usercentrics' );
$ppguc_heading     = $ppguc_heading ?? '';
$ppguc_description = $ppguc_description ?? '';

// Extract video ID from URL for thumbnail.
preg_match( '/(?:youtube\.com\/embed\/|youtu\.be\/)([a-zA-Z0-9_-]+)/', $ppguc_video_url, $matches );
$ppguc_video_id        = $matches[1] ?? '';
$ppguc_thumbnail_url   = $ppguc_video_id ? "https://img.youtube.com/vi/{$ppguc_video_id}/maxresdefault.jpg" : '';
$ppguc_thumbnail_url_2 = $ppguc_video_id ? "https://img.youtube.com/vi/{$ppguc_video_id}/hqdefault.jpg" : ''; // Fallback.
?>

<div class="ppg-video-card">
	<?php if ( ! empty( $ppguc_heading ) ) : ?>
		<h2 class="ppg-video-card-heading"><?php echo esc_html( $ppguc_heading ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $ppguc_description ) ) : ?>
		<p class="ppg-video-card-description ppg-mb-sm"><?php echo esc_html( $ppguc_description ); ?></p>
	<?php endif; ?>

	<div class="ppg-video-container">
		<div class="ppg-video-wrapper ppg-video-lazy" data-video-url="<?php echo esc_attr( $ppguc_video_url ); ?>">
			<?php if ( $ppguc_thumbnail_url ) : ?>
				<img 
					src="<?php echo esc_url( $ppguc_thumbnail_url ); ?>" 
					alt="<?php echo esc_attr( $ppguc_video_title ); ?>"
					class="ppg-video-thumbnail"
					loading="lazy"
				>
				<button type="button" class="ppg-video-play-button" aria-label="<?php esc_attr_e( 'Play video', 'privacy-policy-usercentrics' ); ?>">
					<svg width="68" height="48" viewBox="0 0 68 48" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55c-2.93.78-4.63 3.26-5.42 6.19C.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="#f00"/>
						<path d="M45 24L27 14v20l18-10z" fill="#fff"/>
					</svg>
				</button>
			<?php else : ?>
				<!-- Fallback: load iframe immediately if no video ID -->
				<iframe
					src="<?php echo esc_url( $ppguc_video_url ); ?>"
					title="<?php echo esc_attr( $ppguc_video_title ); ?>"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					allowfullscreen
					loading="lazy">
				</iframe>
			<?php endif; ?>
		</div>
	</div>
</div>


<?php
/**
 * Overview Page Template - 3 States
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 * @var string $current_state Current plugin state (not_connected, connected_not_published, published)
 * @var string $policy_id Current policy ID
 * @var int $display_page Current display page ID
 * @var string $page_title Page title if published
 * @var string $page_url Page URL if published
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// UTM parameters for tracking.
$ppguc_utm_params = 'utm_source=WordPress&utm_medium=plugin&utm_campaign=ppg';
?>

<div class="ppg-overview-page">
	<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/header.php'; ?>
	<div class="ppg-overview-container">

		<?php if ( 'not_connected' === $current_state ) : ?>
			<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/overview/not-connected.php'; ?>
		<?php elseif ( 'connected_not_published' === $current_state ) : ?>
			<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/overview/connected-not-published.php'; ?>
		<?php else : ?>
			<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/overview/published.php'; ?>
		<?php endif; ?>

		<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/cmp-banner.php'; ?>

	</div>
</div>
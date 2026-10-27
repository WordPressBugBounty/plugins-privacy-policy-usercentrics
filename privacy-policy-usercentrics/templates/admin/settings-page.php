<?php
/**
 * Settings Page Template - Two Step Layout
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 * @var string $current_state Current plugin state (not_connected, connected_not_published, published)
 * @var string $policy_id Current policy ID
 * @var int $display_page Current display page ID
 * @var array $pages List of published pages
 * @var bool $is_connected Whether policy is connected
 * @var string $page_title Page title if published
 * @var string $page_url Page URL if published
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Usercentrics\PrivacyPolicyGenerator\Utils\IconHelper;

// UTM parameters for tracking.
$ppguc_utm_params = 'utm_source=WordPress&utm_medium=plugin&utm_campaign=ppg';
// Disable Step 2 if not connected OR if already published.
$ppguc_should_disable_step_2 = ! $is_connected || 'published' === $current_state;
?>

<div class="ppg-settings-page">
	<?php require PPGUC_PLUGIN_DIR . 'templates/admin/components/header.php'; ?>
	<div class="ppg-overview-container">
		<h1 class="ppg-page-title"><?php esc_html_e( 'Setup', 'privacy-policy-usercentrics' ); ?></h1>

		<div class="ppg-welcome-wrapper">
			<div class="ppg-welcome-body">
				<!-- Two Column Layout -->
				<div class="ppg-settings-grid">

					<!-- Step 1: Connect the Privacy Policy ID -->
					<div class="ppg-settings-card">
						<div class="ppg-step-badge ppg-step-badge-active">
							<?php esc_html_e( 'STEP 1', 'privacy-policy-usercentrics' ); ?>
						</div>
						<h2 class="ppg-step-title"><?php esc_html_e( 'Connect the Privacy Policy ID', 'privacy-policy-usercentrics' ); ?></h2>
						<p class="ppg-step-description">
							<?php esc_html_e( 'Copy & paste the Usercentrics Policy ID into the field below to integrate the script directly into your website.', 'privacy-policy-usercentrics' ); ?>
						</p>

						<?php if ( $is_connected ) : ?>
							<!-- Connected State (Read-only) -->
							<div class="ppg-form-group ppg-form-group-gray">
								<div class="ppg-status-badge ppg-status-connected">
									<?php esc_html_e( 'Connected', 'privacy-policy-usercentrics' ); ?>
								</div>
								<label class="ppg-form-label">
									<?php esc_html_e( 'Privacy Policy ID', 'privacy-policy-usercentrics' ); ?>
									<span class="ppg-required">*</span>
								</label>
								<input type="text"
									class="ppg-input ppg-input-readonly"
									value="<?php echo esc_attr( $policy_id ); ?>"
									disabled
									readonly>
							</div>


							<!-- Action Buttons -->
							<div class="ppg-button-group-right">
								<button type="button"
									class="ppg-button ppg-button-primary"
									data-action="show-disconnect-modal">
									<?php esc_html_e( 'Disconnect Policy ID', 'privacy-policy-usercentrics' ); ?>
								</button>
							</div>

						<?php else : ?>
							<!-- Disconnected State -->
							<form method="post" action="" id="ppg-connect-form">
								<?php wp_nonce_field( 'ppguc_connect_policy', 'ppguc_connect_nonce' ); ?>
								<input type="hidden" name="ppguc_action" value="connect">

								<div class="ppg-form-group ppg-form-group-gray">
									<label for="ppguc_policy_id" class="ppg-form-label">
										<?php esc_html_e( 'Privacy Policy ID', 'privacy-policy-usercentrics' ); ?>
										<span class="ppg-required">*</span>
									</label>
									<input type="text"
										name="ppguc_policy_id"
										id="ppguc_policy_id"
										class="ppg-input"
										value="<?php echo esc_attr( $policy_id ); ?>"
										placeholder="<?php esc_attr_e( 'Paste policy ID here', 'privacy-policy-usercentrics' ); ?>"
										required>

									<!-- Verification Status (hidden by default, shown via JS) -->
									<div class="ppg-verification-status" style="display: none;">
										<span class="ppg-spinner"></span>
										<span class="ppg-verification-text"><?php esc_html_e( 'Verifying the Policy ID...', 'privacy-policy-usercentrics' ); ?></span>
									</div>
								</div>

								<div class="ppg-button-group ppg-button-group-right">
									<button type="submit" class="ppg-button ppg-button-primary ppg-button-large" id="ppg-connect-button">
										<?php esc_html_e( 'Connect Policy ID', 'privacy-policy-usercentrics' ); ?>
									</button>
								</div>
							</form>

						<?php endif; ?>
					</div>

					<!-- Step 2: Publish your Privacy Policy -->
					<div class="ppg-settings-card <?php echo $ppguc_should_disable_step_2 ? 'ppg-card-disabled' : ''; ?>">
						<div class="ppg-step-badge <?php echo $ppguc_should_disable_step_2 ? 'ppg-step-badge-disabled' : 'ppg-step-badge-active'; ?>">
							<?php esc_html_e( 'STEP 2', 'privacy-policy-usercentrics' ); ?>
						</div>
						<h2 class="ppg-step-title <?php echo $ppguc_should_disable_step_2 ? 'ppg-step-title-disabled' : ''; ?>"><?php esc_html_e( 'Publish your Privacy Policy Page', 'privacy-policy-usercentrics' ); ?></h2>
						<p class="ppg-step-description <?php echo $ppguc_should_disable_step_2 ? 'ppg-step-description-disabled' : ''; ?>">
							<?php esc_html_e( 'Choose where to display your policy: override the content of an existing page or create a new one. For best results, we suggest adding it to a new page.', 'privacy-policy-usercentrics' ); ?>
						</p>

						<form method="post" action="" id="ppg-publish-form" data-form="publish-policy" <?php echo $ppguc_should_disable_step_2 ? 'disabled' : ''; ?>>
							<?php wp_nonce_field( 'ppguc_publish_policy', 'ppguc_publish_nonce' ); ?>
							<input type="hidden" name="ppguc_action" value="publish">

							<!-- Page Type -->
							<div class="ppg-form-group ppg-form-group-gray <?php echo $ppguc_should_disable_step_2 ? 'ppg-form-group-disabled' : ''; ?>">
								<label class="ppg-form-label <?php echo $ppguc_should_disable_step_2 ? 'ppg-form-label-disabled' : ''; ?>">
									<?php esc_html_e( 'Page Type', 'privacy-policy-usercentrics' ); ?> <span class="ppg-required">*</span>
								</label>
								<div class="ppg-radio-group">
									<label class="ppg-radio-label">
										<input type="radio"
											name="ppguc_page_option"
											value="create"
											id="ppguc_page_option_create"
											class="ppg-radio-input"
											<?php checked( $page_option, 'create' ); ?>
											<?php echo $ppguc_should_disable_step_2 ? 'disabled' : ''; ?>>
										<span class="ppg-radio-text <?php echo $ppguc_should_disable_step_2 ? 'ppg-radio-text-disabled' : ''; ?>">
											<?php esc_html_e( 'Create New Page', 'privacy-policy-usercentrics' ); ?>
											<span class="ppg-radio-description">
												<?php esc_html_e( 'Adds a new page with your Privacy Policy.', 'privacy-policy-usercentrics' ); ?>
											</span>
										</span>
									</label>
									<label class="ppg-radio-label">
										<input type="radio"
											name="ppguc_page_option"
											value="existing"
											id="ppguc_page_option_existing"
											class="ppg-radio-input"
											<?php checked( $page_option, 'existing' ); ?>
											<?php echo $ppguc_should_disable_step_2 ? 'disabled' : ''; ?>>
										<span class="ppg-radio-text <?php echo $ppguc_should_disable_step_2 ? 'ppg-radio-text-disabled' : ''; ?>">
											<?php esc_html_e( 'Use Existing Page', 'privacy-policy-usercentrics' ); ?>
											<span class="ppg-radio-description">
												<?php esc_html_e( 'Overrides content on the selected page.', 'privacy-policy-usercentrics' ); ?>
											</span>
										</span>
									</label>
								</div>
							</div>

							<!-- Existing Page Selection -->
							<div class="ppg-form-group ppg-form-group-gray <?php echo $ppguc_should_disable_step_2 ? 'ppg-form-group-disabled' : ''; ?>" id="ppg-existing-page-group">
								<label for="ppguc_existing_page" class="ppg-form-label <?php echo $ppguc_should_disable_step_2 ? 'ppg-form-label-disabled' : ''; ?>">
									<?php esc_html_e( 'Select the page you want to use', 'privacy-policy-usercentrics' ); ?>
									<span class="ppg-required">*</span>
								</label>
								<select name="ppguc_existing_page"
									id="ppguc_existing_page"
									class="ppg-input"
									<?php echo $ppguc_should_disable_step_2 ? 'disabled' : ''; ?>>
									<option value="" disabled <?php selected( $display_page, 0 ); ?>><?php esc_html_e( '— Select a Page —', 'privacy-policy-usercentrics' ); ?></option>
									<?php
									$ppguc_privacy_policy_page_id = (int) get_option( 'wp_page_for_privacy_policy', 0 );
									foreach ( $pages as $ppguc_page ) :
										$ppguc_is_wp_privacy_page = ( $ppguc_privacy_policy_page_id > 0 && $ppguc_page->ID === $ppguc_privacy_policy_page_id );
										?>
										<option value="<?php echo esc_attr( $ppguc_page->ID ); ?>"
											<?php selected( $display_page, $ppguc_page->ID ); ?>>
											<?php
											echo esc_html( $ppguc_page->post_title );

											// Show status for non-published pages.
											if ( 'publish' !== $ppguc_page->post_status ) {
												echo ' (' . esc_html( ucfirst( $ppguc_page->post_status ) ) . ')';
											}
											?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- New Page Name Input -->
							<div class="ppg-form-group ppg-form-group-gray ppg-hidden <?php echo $ppguc_should_disable_step_2 ? 'ppg-form-group-disabled' : ''; ?>" id="ppg-new-page-group">
								<label for="ppguc_new_page_name" class="ppg-form-label <?php echo $ppguc_should_disable_step_2 ? 'ppg-form-label-disabled' : ''; ?>">
									<?php esc_html_e( 'Page Name', 'privacy-policy-usercentrics' ); ?> <span class="ppg-required">*</span>
								</label>
							<input type="text"
								name="ppguc_new_page_name"
								id="ppguc_new_page_name"
								class="ppg-input"
								value=""
								placeholder="<?php esc_attr_e( 'Give your policy page a name', 'privacy-policy-usercentrics' ); ?>"
								<?php echo $ppguc_should_disable_step_2 ? 'disabled' : ''; ?>>
							</div>

						<!-- Action Buttons -->
						<div class="ppg-button-group ppg-button-group-right">
							<?php if ( 'published' === $current_state ) : ?>
								<a href="<?php echo esc_url( $page_url ); ?>"
									class="ppg-button ppg-button-primary ppg-button-large"
									target="_blank"
									rel="noopener noreferrer">
									<?php esc_html_e( 'Preview Page', 'privacy-policy-usercentrics' ); ?>
								</a>
							<?php else : ?>
								<button type="button"
									class="ppg-button ppg-button-secondary <?php echo $ppguc_should_disable_step_2 ? 'ppg-button-disabled' : ''; ?>"
									data-action="preview-policy"
									data-page-id="<?php echo esc_attr( $display_page ); ?>"
									data-page-url="<?php echo esc_url( get_permalink( $display_page ) ); ?>"
									<?php echo $ppguc_should_disable_step_2 ? 'disabled' : ''; ?>>
									<?php esc_html_e( 'Preview Page', 'privacy-policy-usercentrics' ); ?>
								</button>
								<button type="button"
									class="ppg-button ppg-button-primary <?php echo $ppguc_should_disable_step_2 ? 'ppg-button-disabled' : ''; ?>"
									data-action="show-publish-modal"
									<?php echo $ppguc_should_disable_step_2 ? 'disabled' : ''; ?>>
									<?php esc_html_e( 'Publish Policy', 'privacy-policy-usercentrics' ); ?>
									<span class="dashicons dashicons-arrow-right-alt2"></span>
								</button>
							<?php endif; ?>
						</div>
						</form>
					</div>
				</div>
			</div><!-- .ppg-welcome-body -->
		</div><!-- .ppg-welcome-wrapper -->

		<?php if ( $is_connected ) : ?>
		<!-- Manage Your Policy Card -->
		<div class="ppg-info-card ppg-manage-policy-horizontal">
		<div class="ppg-manage-policy-icon">
			<img src="<?php echo esc_url( IconHelper::getIcon( 'edit-icon' ) ); ?>" alt="<?php esc_attr_e( 'Edit', 'privacy-policy-usercentrics' ); ?>" width="24" height="24">
		</div>
			<div class="ppg-manage-policy-content">
				<h3><?php esc_html_e( 'Manage Your Policy', 'privacy-policy-usercentrics' ); ?></h3>
				<p><?php esc_html_e( 'To edit your Privacy Policy - just head over to your Usercentrics account to make changes. Then update your Policy ID here in WordPress to sync the latest version and keep your site compliant.', 'privacy-policy-usercentrics' ); ?></p>
			</div>
			<div class="ppg-manage-policy-action">
					<a href="<?php echo esc_url( 'https://policygenerator.usercentrics.eu/?' . $ppguc_utm_params ); ?>"
					class="ppg-button ppg-button-secondary"
					target="_blank"
					rel="noopener noreferrer">
					<?php esc_html_e( 'Go to Usercentrics', 'privacy-policy-usercentrics' ); ?>
				</a>
			</div>
		</div>
		<?php endif; ?>


	</div><!-- .ppg-overview-container -->
</div><!-- .ppg-settings-page -->

<!-- Disconnect Modal -->
<?php
// Disconnect Modal - Reusable warning modal component.
$ppguc_modal_id      = 'ppg-disconnect-modal';
$ppguc_title         = __( 'Are you sure?', 'privacy-policy-usercentrics' );
$ppguc_content       = '<p>' . __( 'Clicking OK will remove the Policy ID from your website.', 'privacy-policy-usercentrics' ) . '</p>';
$ppguc_confirm_text  = __( 'OK', 'privacy-policy-usercentrics' );
$ppguc_form          = array(
	'nonce_action'  => 'ppguc_disconnect_action',
	'nonce_name'    => 'ppguc_disconnect_nonce',
	'hidden_fields' => array(
		'ppguc_action' => 'disconnect',
	),
);
require PPGUC_PLUGIN_DIR . 'templates/admin/components/modals/warning-modal.php';

// Publish Warning Modal - Reusable warning modal component.
$ppguc_modal_id       = 'ppg-publish-modal';
$ppguc_title          = __( 'The existing page content will be overridden', 'privacy-policy-usercentrics' );
$ppguc_content        = '<p>' . __( 'When published, this page displays ONLY your privacy policy from Usercentrics.', 'privacy-policy-usercentrics' ) . '</p>' .
				  '<p>' . __( 'Don\'t worry — your original content is saved and can be restored anytime.', 'privacy-policy-usercentrics' ) . '</p>';
$ppguc_confirm_text   = __( 'Publish Policy', 'privacy-policy-usercentrics' );
$ppguc_confirm_action = 'confirm-publish';
$ppguc_confirm_class  = 'ppg-button-primary';
$ppguc_form           = null; // Button-based modal (no form).
require PPGUC_PLUGIN_DIR . 'templates/admin/components/modals/warning-modal.php';
?>
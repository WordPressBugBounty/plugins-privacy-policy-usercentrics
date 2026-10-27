<?php
/**
 * Warning Modal Component
 * Reusable modal for warning messages with optional form
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 *
 * @var string $ppguc_modal_id        Modal ID (required)
 * @var string $ppguc_title           Modal title (required)
 * @var string $ppguc_content         Modal body content (required, can be HTML)
 * @var string $ppguc_cancel_text     Cancel button text (optional, default: 'Cancel')
 * @var string $ppguc_confirm_text    Confirm button text (required)
 * @var string $ppguc_confirm_action  Confirm button data-action attribute (optional for button-based modals)
 * @var string $ppguc_confirm_class   Confirm button CSS class (optional, default: 'ppg-button-primary')
 * @var array  $ppguc_form            Form data (optional). If provided, renders form instead of button.
 *                              Structure: ['action' => '', 'nonce_action' => '', 'nonce_name' => '', 'hidden_fields' => []]
 */

use Usercentrics\PrivacyPolicyGenerator\Utils\IconHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Set defaults.
$ppguc_modal_id       = $ppguc_modal_id ?? 'ppg-modal';
$ppguc_title          = $ppguc_title ?? '';
$ppguc_content        = $ppguc_content ?? '';
$ppguc_cancel_text    = $ppguc_cancel_text ?? __( 'Cancel', 'privacy-policy-usercentrics' );
$ppguc_confirm_text   = $ppguc_confirm_text ?? __( 'Confirm', 'privacy-policy-usercentrics' );
$ppguc_confirm_action = $ppguc_confirm_action ?? '';
$ppguc_confirm_class  = $ppguc_confirm_class ?? 'ppg-button-primary';
$ppguc_form           = $ppguc_form ?? null;

// Determine if we need data-modal attributes (for Modal.js)
$ppguc_needs_data_modal = ! empty( $ppguc_form ) || in_array( $ppguc_modal_id, array( 'ppg-disconnect-modal', 'ppg-publish-modal' ), true );
$ppguc_data_modal_attr  = $ppguc_needs_data_modal ? sprintf( 'data-modal="%s"', esc_attr( $ppguc_modal_id ) ) : '';
?>

<!-- Warning Modal: <?php echo esc_html( $ppguc_modal_id ); ?> -->
<div id="<?php echo esc_attr( $ppguc_modal_id ); ?>" class="ppg-modal">
	<div class="ppg-modal-overlay" <?php echo ! $ppguc_needs_data_modal ? 'data-action="close-modal"' : ''; ?>></div>
	<div class="ppg-modal-content">
		<div class="ppg-modal-header-row">
			<div class="ppg-modal-icon ppg-modal-icon-warning">
				<img src="<?php echo esc_url( IconHelper::getIcon( 'warning-icon' ) ); ?>" alt="<?php esc_attr_e( 'Warning', 'privacy-policy-usercentrics' ); ?>" width="24" height="24">
			</div>
		<button type="button" class="ppg-modal-close" data-action="close-modal" <?php echo $ppguc_data_modal_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in variable construction ?>>
			<span class="dashicons dashicons-no-alt"></span>
		</button>
		</div>
		<div class="ppg-modal-header">
			<h3><?php echo wp_kses_post( $ppguc_title ); ?></h3>
		</div>
		<div class="ppg-modal-body">
			<?php echo wp_kses_post( $ppguc_content ); ?>
		</div>
		<div class="ppg-modal-footer">
		<button type="button" class="ppg-button ppg-button-secondary <?php echo $ppguc_form ? '' : 'ppg-button-large'; ?>" data-action="close-modal" <?php echo $ppguc_data_modal_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in variable construction ?>>
			<?php echo esc_html( $ppguc_cancel_text ); ?>
		</button>

			<?php if ( $ppguc_form ) : ?>
				<!-- Form-based confirm (for actions that submit to backend) -->
				<form method="post" action="" class="ppg-inline-form">
					<?php
					// Render nonce field.
					if ( ! empty( $ppguc_form['nonce_action'] ) && ! empty( $ppguc_form['nonce_name'] ) ) {
						wp_nonce_field( $ppguc_form['nonce_action'], $ppguc_form['nonce_name'] );
					}

					// Render hidden fields.
					if ( ! empty( $ppguc_form['hidden_fields'] ) && is_array( $ppguc_form['hidden_fields'] ) ) {
						foreach ( $ppguc_form['hidden_fields'] as $ppguc_name => $ppguc_value ) {
							printf(
								'<input type="hidden" name="%s" value="%s">',
								esc_attr( $ppguc_name ),
								esc_attr( $ppguc_value )
							);
						}
					}
					?>
					<button type="submit" class="ppg-button <?php echo esc_attr( $ppguc_confirm_class ); ?>">
						<?php echo esc_html( $ppguc_confirm_text ); ?>
					</button>
				</form>
			<?php else : ?>
				<!-- Button-based confirm (for JavaScript actions) -->
				<button type="button" class="ppg-button <?php echo esc_attr( $ppguc_confirm_class ); ?> ppg-button-large" <?php echo $ppguc_confirm_action ? sprintf( 'data-action="%s"', esc_attr( $ppguc_confirm_action ) ) : ''; ?>>
					<?php echo esc_html( $ppguc_confirm_text ); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
</div>


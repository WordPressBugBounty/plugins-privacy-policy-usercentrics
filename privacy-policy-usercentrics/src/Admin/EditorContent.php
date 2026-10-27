<?php
/**
 * Editor Content Manager
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Admin;

use Usercentrics\PrivacyPolicyGenerator\Settings\SettingsService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editor content manager class - Shows policy content in editor
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Admin
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class EditorContent {

	/**
	 * Settings service instance
	 *
	 * @var SettingsService
	 */
	private SettingsService $_settings_service;

	/**
	 * Meta key for PPG page flag
	 */
	private const META_KEY = '_ppguc_usercentrics_page';

	/**
	 * Constructor
	 *
	 * @param SettingsService $settings_service Settings service instance.
	 */
	public function __construct( SettingsService $settings_service ) {
		$this->_settings_service = $settings_service;
	}

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public function init(): void {
		// Update meta when settings are saved.
		add_action( 'update_option_ppguc_usercentrics_display_page', array( $this, 'updatePageMeta' ), 10, 2 );

		// Enqueue editor assets (including policy preview JS).
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueEditorAssets' ) );

		// Register meta box.
		add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );

		// Hook into post save/publish to detect when user publishes.
		add_action( 'save_post_page', array( $this, 'handlePagePublish' ), 10, 3 );

		// Redirect after publish.
		add_filter( 'redirect_post_location', array( $this, 'redirectAfterPublish' ), 10, 2 );
	}

	/**
	 * Update page meta when settings change
	 *
	 * @param int $old_value Old page ID.
	 * @param int $new_value New page ID.
	 *
	 * @return void
	 */
	public function updatePageMeta( int $old_value, int $new_value ): void {
		// Remove meta from old page.
		if ( $old_value > 0 ) {
			delete_post_meta( $old_value, self::META_KEY );
		}

		// Add meta to new page.
		if ( $new_value > 0 ) {
			update_post_meta( $new_value, self::META_KEY, '1' );
		}
	}

	/**
	 * Check if a page is the PPG page
	 *
	 * @param int $page_id Page ID.
	 *
	 * @return bool True if PPG page.
	 */
	private function _isPpgPage( int $page_id ): bool {
		$display_page = $this->_settings_service->getDisplayPage();
		return $page_id === $display_page && $display_page > 0;
	}


	/**
	 * Add meta box
	 *
	 * @return void
	 */
	public function addMetaBox(): void {
		$display_page = $this->_settings_service->getDisplayPage();
		if ( ! $display_page ) {
			return;
		}

		add_meta_box(
			'ppg-privacy-policy',
			__( 'Privacy Policy (Usercentrics)', 'privacy-policy-usercentrics' ),
			array( $this, 'renderMetaBox' ),
			'page',
			'side',
			'default'
		);
	}

	/**
	 * Render meta box
	 *
	 * @param \WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function renderMetaBox( \WP_Post $post ): void {
		if ( ! $this->_isPpgPage( $post->ID ) ) {
			return;
		}

		$ppguc_policy_id  = $this->_settings_service->getPolicyId();
		$ppguc_utm_params = 'utm_source=WordPress&utm_medium=plugin&utm_campaign=ppg&utm_content=editor_metabox';
		?>
		<div class="ppg-meta-box">
	  
			<p class="ppg-meta-box-description">
		<?php esc_html_e( 'When published, this page displays ONLY your privacy policy from Usercentrics. Any page content you add will be hidden (but preserved).', 'privacy-policy-usercentrics' ); ?>
			</p>
			
	   
			<div class="ppg-meta-box-actions">
				<a href="<?php echo esc_url( 'https://policygenerator.usercentrics.eu/?' . $ppguc_utm_params ); ?>" 
					target="_blank" 
					class="button button-primary">
					<span class="dashicons dashicons-edit"></span>
		<?php esc_html_e( 'Edit Policy on Usercentrics', 'privacy-policy-usercentrics' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=ppg-settings' ) ); ?>" 
					class="button button-secondary">
					<span class="dashicons dashicons-admin-settings"></span>
		<?php esc_html_e( 'Plugin Settings', 'privacy-policy-usercentrics' ); ?>
				</a>
			</div>

		</div>
		<?php
	}

	/**
	 * Enqueue editor assets
	 *
	 * @param string $hook Current page hook.
	 *
	 * @return void
	 */
	public function enqueueEditorAssets( string $hook ): void {
		// Only on post editor.
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		// Get current page ID.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only operation, no data modification.
		$page_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		if ( ! $page_id ) {
			return;
		}

		// Check if this is preview mode OR if it's already the PPG page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only operation for preview flag.
		$is_preview_mode = isset( $_GET['ppguc_preview_mode'] ) && '1' === $_GET['ppguc_preview_mode'];
		$is_ppguc_page     = $this->_isPpgPage( $page_id );

		// Only load assets if it's the PPG page OR preview mode.
		if ( ! $is_ppguc_page && ! $is_preview_mode ) {
			return;
		}

		// If in preview mode, set a meta flag so we know to register this page when published.
		if ( $is_preview_mode ) {
			update_post_meta( $page_id, '_ppguc_preview_mode_page', '1' );
		}

		$ppguc_policy_id  = $this->_settings_service->getPolicyId();
		$ppguc_utm_params = 'utm_source=WordPress&utm_medium=plugin&utm_campaign=ppg&utm_content=editor';

		// Enqueue admin CSS (contains editor styles).
		wp_enqueue_style(
			'ppg-admin-editor',
			PPGUC_PLUGIN_URL . 'assets/dist/css/admin-styles.css',
			array(),
			PPGUC_VERSION
		);

		// Enqueue policy preview JavaScript.
		wp_enqueue_script(
			'ppg-editor-policy-preview',
			PPGUC_PLUGIN_URL . 'assets/dist/js/editor-policy-preview.js',
			array(),
			PPGUC_VERSION,
			true
		);

		// Pass configuration to JavaScript.
		wp_localize_script(
			'ppg-editor-policy-preview',
			'ppgucEditorConfig',
			array(
				'policyId'     => $ppguc_policy_id,
				'isPPGPage'    => true,
				'dashboardUrl' => 'https://policygenerator.usercentrics.eu/?' . $ppguc_utm_params,
				'settingsUrl'  => admin_url( 'admin.php?page=ppg-settings' ),
			)
		);

		// Add body class to enable editor-specific styles.
		add_filter(
			'admin_body_class',
			function ( $classes ) {
				return $classes . ' ppg-is-policy-page';
			}
		);

		// Add modal HTML to footer (only if in preview mode).
		if ( $is_preview_mode ) {
			add_action( 'admin_footer', array( $this, 'renderPublishWarningModal' ) );
		}
	}

	/**
	 * Render the publish warning modal in the editor
	 *
	 * @return void
	 */
	public function renderPublishWarningModal(): void {
		// Use reusable warning modal component.
		$ppguc_modal_id       = 'ppg-editor-publish-modal';
		$ppguc_title          = __( 'The existing page content will be overridden', 'privacy-policy-usercentrics' );
		$ppguc_content        = '<p>' . __( 'When published, this page displays ONLY your privacy policy from Usercentrics.', 'privacy-policy-usercentrics' ) . '</p>' .
							'<p>' . __( 'Don\'t worry — your original content is saved and can be restored anytime.', 'privacy-policy-usercentrics' ) . '</p>';
		$ppguc_confirm_text   = __( 'Publish Policy', 'privacy-policy-usercentrics' );
		$ppguc_confirm_action = 'confirm-publish';
		$ppguc_confirm_class  = 'ppg-button-primary';
		$ppguc_form           = null; // Button-based modal (no form).
		require PPGUC_PLUGIN_DIR . 'templates/admin/components/modals/warning-modal.php';
	}

	/**
	 * Handle page publish - update plugin state
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @param bool     $update  Whether this is an update.
	 *
	 * @return void
	 */
	public function handlePagePublish( int $post_id, \WP_Post $post, bool $update ): void {
		// Check if this is our PPG page OR if we're coming from preview mode.
		$is_ppguc_page           = $this->_isPpgPage( $post_id );
		$was_in_preview_mode   = get_post_meta( $post_id, '_ppguc_preview_mode_page', true ) === '1';
		$should_handle_publish = $is_ppguc_page || $was_in_preview_mode;

		// Exit if not our page and not in preview mode.
		if ( ! $should_handle_publish ) {
			return;
		}

		// Only when publishing.
		if ( 'publish' !== $post->post_status ) {
			return;
		}

		// Check if this is an autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// If we were in preview mode, now set this as the display page.
		if ( $was_in_preview_mode ) {
			update_option( 'ppguc_usercentrics_display_page', $post_id );
			// Clean up the preview mode meta.
			delete_post_meta( $post_id, '_ppguc_preview_mode_page' );
		}

		// Make sure display_page option is set (in case it wasn't already).
		$current_display_page = $this->_settings_service->getDisplayPage();
		if ( $current_display_page !== $post_id ) {
			update_option( 'ppguc_usercentrics_display_page', $post_id );
		}

		// Set post meta to mark this as PPG page.
		update_post_meta( $post_id, self::META_KEY, '1' );

		// Set transient to trigger redirect.
		set_transient( 'ppguc_page_published_' . $post_id, '1', 30 );

		// Set success message.
		set_transient(
			'ppguc_admin_notice',
			array(
				'type'    => 'success',
				'message' => __( 'Privacy Policy published successfully!', 'privacy-policy-usercentrics' ),
			),
			30
		);
	}

	/**
	 * Redirect after publish to dashboard
	 *
	 * @param string $location Redirect location.
	 * @param int    $post_id  Post ID.
	 *
	 * @return string Modified redirect location.
	 */
	public function redirectAfterPublish( string $location, int $post_id ): string {
		// Check if we should redirect.
		if ( ! get_transient( 'ppguc_page_published_' . $post_id ) ) {
			return $location;
		}

		// Delete the transient.
		delete_transient( 'ppguc_page_published_' . $post_id );

		// Only for our PPG page.
		if ( ! $this->_isPpgPage( $post_id ) ) {
			return $location;
		}

		// Redirect to dashboard.
		return admin_url( 'admin.php?page=privacy-policy-usercentrics' );
	}
}


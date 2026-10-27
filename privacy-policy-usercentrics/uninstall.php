<?php
/**
 * Uninstall script for Usercentrics Privacy Policy Generator
 *
 * @package Usercentrics\PrivacyPolicyGenerator
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'ppguc_usercentrics_policy_id' );
delete_option( 'ppguc_usercentrics_display_page' );
delete_option( 'ppguc_usercentrics_onboarding_complete' );

// Delete transients.
delete_transient( 'ppguc_activation_redirect' );

// For multisite.
if ( is_multisite() ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall script, one-time operation, caching not needed.
	$ppguc_blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	foreach ( $ppguc_blog_ids as $ppguc_blog_id ) {
		switch_to_blog( $ppguc_blog_id );
		delete_option( 'ppguc_usercentrics_policy_id' );
		delete_option( 'ppguc_usercentrics_display_page' );
		delete_option( 'ppguc_usercentrics_onboarding_complete' );
		delete_transient( 'ppguc_activation_redirect' );
		restore_current_blog();
	}
}

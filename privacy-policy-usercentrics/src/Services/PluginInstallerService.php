<?php
/**
 * Plugin Installer Service
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Services
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */

declare(strict_types=1);

namespace Usercentrics\PrivacyPolicyGenerator\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin installer service class
 * Handles plugin installation and activation
 *
 * @category WordPress_Plugin
 * @package  Usercentrics\PrivacyPolicyGenerator\Services
 * @author   Usercentrics <support@usercentrics.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://usercentrics.com
 */
class PluginInstallerService {

	/**
	 * Supported plugins configuration
	 *
	 * @var array
	 */
	private const SUPPORTED_PLUGINS = array(
		'cookiebot' => array(
			'slug'    => 'cookiebot',
			'file'    => 'cookiebot/cookiebot.php',
			'wp_slug' => 'cookiebot',
		),
	);

	/**
	 * Get plugin information
	 *
	 * @param string $plugin_slug The plugin slug.
	 *
	 * @return array|null Plugin information or null if not found.
	 */
	public function getPluginInfo( string $plugin_slug ): ?array {
		if ( ! isset( self::SUPPORTED_PLUGINS[ $plugin_slug ] ) ) {
			return null;
		}

		$plugin = self::SUPPORTED_PLUGINS[ $plugin_slug ];
		$status = $this->getPluginStatus( $plugin['file'] );

		// Fetch plugin data from WordPress.org API
		$api_data = $this->fetchPluginDataFromAPI( $plugin['wp_slug'] );

		// Merge with basic plugin info.
		$plugin_info = array_merge(
			$plugin,
			array(
				'name'            => $api_data['name'] ?? 'Usercentrics Cookiebot',
				'description'     => $api_data['short_description'] ?? '',
				'author'          => $api_data['author'] ?? 'Usercentrics',
				'version'         => $api_data['version'] ?? '',
				'requires'        => $api_data['requires'] ?? '',
				'tested'          => $api_data['tested'] ?? '',
				'rating'          => $api_data['rating'] ?? 0,
				'num_ratings'     => $api_data['num_ratings'] ?? 0,
				'active_installs' => $api_data['active_installs'] ?? 0,
				'last_updated'    => $api_data['last_updated'] ?? '',
				'icon'            => $api_data['icons']['2x'] ?? $api_data['icons']['1x'] ?? '',
				'banners'         => $api_data['banners'] ?? array(),
				'download_link'   => $api_data['download_link'] ?? '',
				'homepage'        => $api_data['homepage'] ?? '',
				'status'          => $status,
			)
		);

		return $plugin_info;
	}

	/**
	 * Fetch plugin data from WordPress.org API
	 *
	 * @param string $plugin_slug The WordPress.org plugin slug.
	 *
	 * @return array Plugin data from API.
	 */
	private function fetchPluginDataFromAPI( string $plugin_slug ): array {
		// Check transient cache first (cache for 12 hours).
		$cache_key = 'ppguc_plugin_info_' . $plugin_slug;
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Include necessary WordPress files.
		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		// Fetch from WordPress.org API
		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin_slug,
				'fields' => array(
					'short_description' => true,
					'description'       => true,
					'sections'          => false,
					'tested'            => true,
					'requires'          => true,
					'rating'            => true,
					'ratings'           => true,
					'num_ratings'       => true,
					'downloaded'        => true,
					'active_installs'   => true,
					'last_updated'      => true,
					'added'             => true,
					'homepage'          => true,
					'versions'          => false,
					'donate_link'       => false,
					'tags'              => false,
					'icons'             => true,
					'banners'           => true,
					'screenshots'       => false,
					'download_link'     => true,
				),
			)
		);

		$data = array();

		if ( ! is_wp_error( $api ) ) {
			$data = array(
				'name'              => $api->name ?? '',
				'short_description' => $api->short_description ?? '',
				'description'       => $api->description ?? '',
				'author'            => wp_strip_all_tags( $api->author ?? '' ),
				'version'           => $api->version ?? '',
				'requires'          => $api->requires ?? '',
				'tested'            => $api->tested ?? '',
				'rating'            => $api->rating ?? 0,
				'num_ratings'       => $api->num_ratings ?? 0,
				'active_installs'   => $api->active_installs ?? 0,
				'last_updated'      => $api->last_updated ?? '',
				'icons'             => (array) ( $api->icons ?? array() ),
				'banners'           => (array) ( $api->banners ?? array() ),
				'download_link'     => $api->download_link ?? '',
				'homepage'          => $api->homepage ?? '',
			);

			// Cache for 12 hours.
			set_transient( $cache_key, $data, 12 * HOUR_IN_SECONDS );
		}

		return $data;
	}

	/**
	 * Get plugin status
	 *
	 * @param string $plugin_file The plugin file path.
	 *
	 * @return string Status: 'not_installed', 'installed', or 'active'.
	 */
	private function getPluginStatus( string $plugin_file ): string {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();

		if ( ! isset( $all_plugins[ $plugin_file ] ) ) {
			return 'not_installed';
		}

		if ( is_plugin_active( $plugin_file ) ) {
			return 'active';
		}

		return 'installed';
	}

	/**
	 * Install plugin
	 *
	 * @param string $plugin_slug The plugin slug.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function installPlugin( string $plugin_slug ) {
		if ( ! isset( self::SUPPORTED_PLUGINS[ $plugin_slug ] ) ) {
			return new \WP_Error(
				'invalid_plugin',
				__( 'Invalid plugin slug.', 'privacy-policy-usercentrics' )
			);
		}

		$plugin = self::SUPPORTED_PLUGINS[ $plugin_slug ];

		// Check if already installed.
		if ( $this->getPluginStatus( $plugin['file'] ) !== 'not_installed' ) {
			return new \WP_Error(
				'already_installed',
				__( 'Plugin is already installed.', 'privacy-policy-usercentrics' )
			);
		}

		// Include necessary WordPress files.
		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}
		if ( ! class_exists( 'WP_Upgrader' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		// Get plugin information from WordPress.org
		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin['wp_slug'],
				'fields' => array(
					'short_description' => true,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'downloadlink'      => true,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			return new \WP_Error(
				'plugin_api_error',
				sprintf(
					/* translators: %s: error message */
					__( 'Failed to get plugin information: %s', 'privacy-policy-usercentrics' ),
					$api->get_error_message()
				)
			);
		}

		// Install the plugin.
		$upgrader = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( false === $result ) {
			return new \WP_Error(
				'install_failed',
				__( 'Plugin installation failed.', 'privacy-policy-usercentrics' )
			);
		}

		return true;
	}

	/**
	 * Activate plugin
	 *
	 * @param string $plugin_slug The plugin slug.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function activatePlugin( string $plugin_slug ) {
		if ( ! isset( self::SUPPORTED_PLUGINS[ $plugin_slug ] ) ) {
			return new \WP_Error(
				'invalid_plugin',
				__( 'Invalid plugin slug.', 'privacy-policy-usercentrics' )
			);
		}

		$plugin = self::SUPPORTED_PLUGINS[ $plugin_slug ];

		// Check if plugin is installed.
		$status = $this->getPluginStatus( $plugin['file'] );

		if ( 'not_installed' === $status ) {
			return new \WP_Error(
				'not_installed',
				__( 'Plugin is not installed.', 'privacy-policy-usercentrics' )
			);
		}

		if ( 'active' === $status ) {
			return new \WP_Error(
				'already_active',
				__( 'Plugin is already active.', 'privacy-policy-usercentrics' )
			);
		}

		// Activate the plugin.
		$result = activate_plugin( $plugin['file'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}
}

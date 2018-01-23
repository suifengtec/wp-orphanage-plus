<?php
/**
 * @Author: suifengtec
 * @Date:   2018-01-22 20:11:04
 * @Last Modified by:   suifengtec
 * @Last Modified time: 2018-01-24 01:03:25
 **/
/* *
 * Plugin Name: WP Orphanage Plus
 * Plugin URI: http://coolwp.com/wp-orphanage-plus.html
 * Description: Plugin to promote users with no roles set (the orphans) to the role from other blog where they registered or to default if any found.
 * Author: suifengtec
 * Version: 1.0.0
 * Author URI: http://coolwp.com/
 * Requires at least: 4.4
 * Tested up to: 4.9.2
 *
 * Text Domain: wp-orphanage-plus
 * Domain Path: /languages/
 *
 */
/*

1.0.0 : Based on WP-Orphanage Extended (version 1.1) by MELONIQ.NET ( http://blog.meloniq.net/2012/01/29/wp-orphanage-extended/ )

 */
/*
使用条件

多个 WP 站点使用一个数据库,以不同的表前缀区分不同站点的除用户之外的数据;

假如同一数据库有N个WordPress 站点的数据表,不同的 WordPress 站点以不同的
数据表前缀做区别, 其中有 www.site-1.com 和 www.site-2.com 两个站点,想让
site-2 这个站点(数据表前缀为 s2_ )使用 site-1 这个站点(数据表前缀为 s1_ )的用户数据表,可以在 site-2 的 wp-config.php, 定义两个常量:

define('CUSTOM_USER_TABLE', 's1_users');
define('CUSTOM_USER_META_TABLE', 's1_usermeta');

然后启用这个插件

 */
/*
该修改版实现的特性:

适用于 WordPress 4.9.2,代码优化;

 */
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('WP_Orphanage_Plus')):
	final class WP_Orphanage_Plus {

		private static $instance;
		public function __wakeup() {}
		public function __clone() {}
		public function __construct() {}
		public static function instance() {
			if (!isset(self::$instance) && !(self::$instance instanceof WP_Orphanage_Plus)) {
				self::$instance = new self();
				self::$instance->define_constants();
				self::$instance->hooks();
			}
			return self::$instance;
		}

		public function hooks() {

			register_activation_hook(__FILE__, array($this, 'activate'));

			add_action('wp_login', array($this, 'adopt_this'));
			add_action('load-users.php', array($this, 'adopt_all'));
			add_action('init', array($this, 'init'), 0);

			include_once dirname(__FILE__) . '/settings.php';
			new WP_Orphanage_Plus_Settings;
		}

		public function init() {

			load_plugin_textdomain(WPOP_SLUG, false, dirname(plugin_basename(__FILE__)) . '/languages/');

		}

		/**
		 * Adopts orphaned user
		 *
		 * @param string $login
		 *
		 * @return void
		 */
		public function adopt_this($login) {

			$user = get_user_by('login', $login);
			if (!current_user_can('read')) {
				$user_up = new WP_User($user->ID);
				$user_up->set_role($this->get_default_user_role($user->ID));
			}
		}

		/**
		 * Adopts all orphaned users
		 *
		 * @return void
		 */
		public function adopt_all() {
			foreach ($this->get_all_users() as $user_id) {
				$user = new WP_User($user_id);
				if (!user_can($user_id, 'read')) {
					$user->set_role($this->get_default_user_role($user_id));
				}
			}
		}

		/**
		 * Returns an array of user IDs
		 *
		 * @return array
		 */
		function get_all_users() {
			global $wpdb;

			$results = $wpdb->get_col("SELECT ID FROM $wpdb->users");
			return $results;
		}

		/**
		 * Searching other blogs and returns a user role, if not found, returns default one
		 *
		 * @param int $user_id (optional)
		 *
		 * @return string
		 */
		function get_default_user_role($user_id = false) {
			global $wpdb, $current_user;

			$current_user = wp_get_current_user();
			if (!$user_id) {
				$user_id = $current_user->ID;
			}

			$prefixes = get_option('wporphanageplus_prefixes');
			if ($prefixes && is_array($prefixes)) {
				foreach ($prefixes as $prefix) {
					$role = get_user_meta($user_id, $prefix . 'capabilities', true);
					if ($role != '' && is_array($role)) {
						foreach ($role as $key => $value) {
							return $key;
						}
					}
				}
			}
			$default = get_option('wporphanageplus_role');
			return $default;
		}

		public function activate() {

			$default_role = get_option('default_role');
			if (!get_option('wporphanageplus_role') && $default_role) {
				update_option('wporphanageplus_role', $default_role);
			} else {
				update_option('wporphanageplus_role', 'subscriber');
			}
			$prefixes = array();
			global $wpdb;
			$prefixes[] = $wpdb->prefix;
			if (!get_option('wporphanageplus_prefixes')) {
				update_option('wporphanageplus_prefixes', $prefixes);
			}

		}

		public function define_constants() {

			define('WPOP_VERSION', '1.0.0');
			define('WPOP_SLUG', 'wp-orphanage-plus');

		}

	}

	$GLOBALS['WP_Orphanage_Plus'] = WP_Orphanage_Plus::instance();

endif;

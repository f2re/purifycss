<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/f2re
 * @since      1.0.0
 *
 * @package    Purifycss
 * @subpackage Purifycss/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Purifycss
 * @subpackage Purifycss/includes
 * @author     F2re <lendingad@gmail.com>
 */
class Purifycss_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// creating SLQ table
		self::create_table();
	}

	public static function create_table(){
		global $wpdb;     
		$table_name = $wpdb->prefix . "purifycss";
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
				`id` int(0) UNSIGNED NULL AUTO_INCREMENT,
				`orig_css` varchar(512) NULL,
				`css` varchar(512) NULL,
				PRIMARY KEY (`id`)
				) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}

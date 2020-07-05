<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/f2re
 * @since      1.0.0
 *
 * @package    Purifycss
 * @subpackage Purifycss/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Purifycss
 * @subpackage Purifycss/public
 * @author     F2re <lendingad@gmail.com>
 */
class Purifycss_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * dequeue styles in site
	 * before check if live mode ot prod mode
	 *
	 * @return void
	 */
	function dequeue_all_styles() {
		global $wp_styles;
		global $wpdb;   
		$table_name = $wpdb->prefix . "purifycss";
		$need_to_enc = [];
		
		foreach( $wp_styles->queue as $style ) {
			if ( $style=='admin-bar' ){
				continue;
			}
			$src = $wp_styles->registered[$style]->src;
			// echo $src;
			$files = $wpdb->get_results( "SELECT css from $table_name WHERE  `orig_css` LIKE '%$src%' ;" );
			foreach ($files as $file){
				$need_to_enc[] = $file->css;	
			}
			// echo "/";
			wp_dequeue_style($wp_styles->registered[$style]->handle);
		}
		// print_r($need_to_enc);
		update_option( "purifycss_neededstyles", serialize($need_to_enc) );

	}

	/**
	 * remove inline styles
	 *
	 * @param [type] $content
	 * @return void
	 */
	public function remove_inline_styles($content){
		//--Remove all inline styles--
		$content = preg_replace('/<style[^>]*>[^<]*<\/style>/is','',$content);
		return $content;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		// echo PurifycssHelper::get_css_file();
		// get_option('purifycss_manual_css')==false
		if ( true ){
			$needed_styled = unserialize(get_option( "purifycss_neededstyles" ));
			// print_r($needed_styled);
			if ( is_array($needed_styled) && count($needed_styled)>0 ){
				$i=0;
				foreach ( $needed_styled as $style ){
					wp_enqueue_style( $this->plugin_name.'_'.$i, $style, array(), $this->version, 'all' );
					$i++;
				}
				wp_enqueue_style( $this->plugin_name.'_inline', plugin_dir_url( ( __FILE__ ) ).'../' . PurifycssHelper::$folder.PurifycssHelper::$inline_style, array(), $this->version, 'all' );
			}

			return;

			global $wp;
			// $url = home_url(add_query_arg(array(), $wp->request));
			// echo $url;
			global $wpdb;   
        	$table_name = $wpdb->prefix . "purifycss";
			$files = $wpdb->get_results( "SELECT css from $table_name WHERE  `orig_css` LIKE '%${url}%' ;" );
			$i=0;
			foreach ($files as $file){
				// echo ($file->css);
				wp_enqueue_style( $this->plugin_name.'_'.$i, $file->css, array(), $this->version, 'all' );
				$i++;
			}
			wp_enqueue_style( $this->plugin_name.'_inline', plugin_dir_url( ( __FILE__ ) ).'../' . PurifycssHelper::$folder.PurifycssHelper::$inline_style, array(), $this->version, 'all' );
		}else{
			wp_enqueue_style( $this->plugin_name, PurifycssHelper::get_css_file(), array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Purifycss_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Purifycss_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/purifycss-public.js', array( 'jquery' ), $this->version, false );

	}

}

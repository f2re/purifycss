<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/f2re
 * @since      1.0.0
 *
 * @package    Purifycss
 * @subpackage Purifycss/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Purifycss
 * @subpackage Purifycss/admin
 * @author     F2re <lendingad@gmail.com>
 */
class Purifycss_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register action for add admin menu of plugin
	 *
	 * @return void
	 */	
	public function add_settings_page(){
		add_options_page( 'PurifyCSS', 'PurifyCSS', 'manage_options', 'purifycss-plugin', [$this,'render_plugin_settings_page'] );
	}

	/**
	 * function to render setting page
	 * show partials php script with settings fields
	 *
	 * @return void
	 */
	public function render_plugin_settings_page(){
		require_once 'partials/'.$this->plugin_name.'-admin-display.php';
	}

	/**
	 * Register plugins settings fields
	 *
	 * @return void
	 */
	public function register_settings(){
		// register API key of plugin
		register_setting( $this->plugin_name, "purifycss_api_key", 'string' );

		// register Livemode of plugin
		register_setting( $this->plugin_name, "purifycss_livemode", 'string' );
	}


	/**
	 * AJAX action activate code
	 *
	 * @return void
	 */
	public function actionActivate(){
		$option = "purifycss_api_key";
		$url    = 'https://purifycss.online/api/validate';
		$key    = esc_attr($_POST['key']);

		$msg 	= '';
		// result of function execution
		$result   = false;

		// send request
		$response = wp_remote_post( $url, [ 'body'=>['key'=>$key] ] );

		if ( is_wp_error( $response ) ) {
			$msg    = $response->get_error_message();
			$result = false;
		}else{
			$_rsp = json_decode($response['body'], true);
			if ( $_rsp['valid']==true ){
				$result = update_option( $option, $key );
			}else{
				$result = false;
				$msg    = $_rsp['error'];
			}
		}

		// success result
		if ( $result ){
			wp_send_json([
				'status'=>'OK',
				'msg'=>__('License key acceped','purifycss').' '.$key,
				'resp'=>$response
				]);			
		}else{
			// error
			wp_send_json([
				'status'=>'ERR',
				'msg'=>$msg==''?__('License key not acceped, site error','purifycss'):$msg,
				'resp'=>$response
				]);
		}
	}


	/**
	 * AJAX action enable/disable live mode
	 *
	 * @return void
	 */
	public function actionLivemode(){
		$option = "purifycss_livemode";
		$livemode = get_option($option);
		if ( $livemode=="" || $livemode=="0" ){
			$livemode="1";
		}else{
			$livemode="0";
		}
		$result = update_option( $option, $livemode );

		// success result
		if ( $result ){
			wp_send_json([
				'status'=>'OK',
				'msg'=>__('Live mode '.($livemode=='1'?'enabled':'disabled'),'purifycss'),
				'livemode'=>$livemode,
				]);			
		}else{
			// error
			wp_send_json([
				'status'=>'ERR',
				'msg'=>__('Live mode don\'t enabled, site error','purifycss'),
				]);
		}
	}


	/**
	 * AJAX action enable/disable test mode
	 *
	 * @return void
	 */
	public function actionTestmode(){
		$option = "purifycss_testmode";
		$testmode = get_option($option);
		if ( $testmode=="" || $testmode=="0" ){
			$testmode="1";
		}else{
			$testmode="0";
		}
		$result = update_option( $option, $testmode );

		// success result
		if ( $result ){
			wp_send_json([
				'status'=>'OK',
				'msg'=>__('Test mode '.($testmode=='1'?'enabled':'disabled'),'purifycss'),
				'testmode'=>$testmode,
				]);			
		}else{
			// error
			wp_send_json([
				'status'=>'ERR',
				'msg'=>__('Test mode don\'t enabled, site error','purifycss'),
				]);
		}
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/purifycss-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/purifycss-admin.js', array( 'jquery' ), $this->version, false );

	}

}

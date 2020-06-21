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
	 * function to send request to get CSS code
	 *
	 * @return void
	 */
	public function actionGetCSS(){
		$option = "purifycss_css";
		$url    = 'https://purifycss.online/api/purify';
		$key    = get_option('purifycss_api_key');
		$html   = esc_attr($_POST['customhtml']);
		$msg 	= '';
		// compiled styles
		$css    = '';
		// result msg for display in div block
		$resmsg = '';
		// result of function execution
		$result   = false;

		// check license key
		if ( $key =='' ){
			$msg = __("Invalid licanse key. Please enter verifed license",'purifycss');
			wp_send_json([ 'status'=>'ERR','msg'=>$msg,'resmsg'=>'error' ]);
		}

		// save html code
		update_option( 'purifycss_customhtml', $html );
		// var_dump(get_option( 'purifycss_customhtml' ));

		// send request
		$response = wp_remote_post( $url, [ 
			'url'      => [get_site_url()],
			'source'   => 'wp-plugin',
			'options'  => ['crawl'=>true],
			'htmlCode' => $html,
			'key'      => $key
			 ] );
		
		
		// check error
		if ( is_wp_error( $response ) ) {
			$msg    = $response->get_error_message();
			$result = false;
		}else{
			// get body request
			$_rsp = json_decode($response['body'], true);
			if ( !$_rsp || isset($_rsp['error']) ){
				$result = false;
				if ( isset($_rsp['response']['message']) ){
					$msg    = $_rsp['response']['message'];
					$resmsg = $_rsp['response']['message'];
				}else{
					$msg    =  $resmsg = "error";
				}
			}else{
				$result = true;
				$css = update_option( $option, $_rsp['result']['purified']['content'] );
				// save css to file
				PurifycssHelper::save_css($css);

				$resmsg = '<b>'.$_rsp['result']['stats']['percentageUnused']
							   .' ('.$_rsp['result']['stats']['after'].')</b> '
							   .__('of your CSS has been cleaned up','purifycss');

				// save result text to db
				update_option( 'purifycss_resultdata', $resmsg );
				
			}
		}
		// remove this
		$resmsg=$response;

		// success result
		if ( $result ){
			wp_send_json([
				'status'=>'OK',
				'msg'=>__('CSS generated successfully','purifycss'),
				'resmsg'=>$resmsg,
				'styles'=>$css,
				'resp'=>$response
				]);			
		}else{
			// error
			wp_send_json([
				'status'=>'ERR',
				'msg'=>$msg==''?__('Error by CSS generated','purifycss'):$msg,
				'resmsg'=>$resmsg,
				'resp'=>$response,
				]);
		}
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
				update_option( $option, $key );
				$result = true;
				update_option( 'purifycss_api_key_activated', true);
			}else{
				$result = false;
				$msg    = $_rsp['error'];
				update_option( 'purifycss_api_key_activated', false);
			}
		}

		// success result
		if ( $result ){
			wp_send_json([
				'status'=>'OK',
				'msg'=>__('License key acceped','purifycss').' '.$key,
				// 'resp'=>$response
				]);			
		}else{
			// error
			wp_send_json([
				'status'=>'ERR',
				'msg'=>$msg==''?__('License key not acceped, site error','purifycss'):$msg,
				// 'resp'=>$response
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/purifycss-admin.js', array( 'jquery' ), $this->version, false );


		// подключаем редактор кода для HTML.
		$settings_html = wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
		$settings_css  = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

		// ничего не делаем если CodeMirror отключен.
		if ( false === $settings_html ) {
			return;
		}

		// инициализация
		wp_add_inline_script( 
			'code-editor',
			sprintf( 'jQuery( function() { 				
				var purified_css = wp.codeEditor.initialize( "purified_css", %s );
				var customhtml_text; 
			} );',  wp_json_encode( $settings_css )  )  
		);

		// html text code editor params
		wp_localize_script( $this->plugin_name, 'customhtml_text_param', $settings_html  );
		
	}

}

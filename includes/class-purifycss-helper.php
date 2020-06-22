<?php

/**
 * The file that defines htlper function
 *
 * @link       https://github.com/f2re
 * @since      1.0.0
 *
 * @package    Purifycss
 * @subpackage Purifycss/includes
 */

class PurifycssHelper {

    /**
     * folder of style files store
     *
     * @var string
     */
    private static $folder = 'public/generatedcss/';

    /**
     * filename of style
     *
     * @var string
     */
    private static $style = 'style.min.css';

	/**
	 * save css to file with versionized
	 *
	 * @since    1.0.0
	 */
	static public function save_css($content) {
        // copy exists file to timestamp copy
        // self::$folder
        // self::$style
        $file = plugin_dir_path( dirname( __FILE__ ) ) . self::$folder.self::$style ;
        if ( file_exists( $file ) ){
            $newfile = plugin_dir_path( dirname( __FILE__ ) ) . self::$folder.uniqid().'_'.self::$style ;
            copy($file, $newfile);
        }

        // write code to file
        // style.css
        file_put_contents($file, $content);

        return;
    }
    
    /**
     * Get css file content
     *
     * @return void
     */
    static public function get_css() {
        $file = plugin_dir_path( dirname( __FILE__ ) ) . self::$folder.self::$style ;

        // return $file;
        if ( file_exists( $file ) ){
            return file_get_contents($file);
        }
        return "nofile";
    }

    /**
     * compare referer with needed values
     *
     * @return void
     */
    static public function check_referer(){

        if (isset($_GET['purify']) && $_GET['purify']=='false' ) {
            return true;
        }
        return false;

        // echo wp_get_raw_referer();
        if ( strpos(wp_get_raw_referer(),'https://purifycss.online')!==false ){
            return true;
        }
        return false;
    }

    /**
     * get path to css file
     *
     * @return void
     */
    static public function get_css_file(){
        $file = plugin_dir_url( ( __FILE__ ) ).'../' . self::$folder.self::$style ;
        return $file;
    }

    /**
     * Check if LIVE mode enabled
     *
     * @return void
     */
    static public function check_live_mode(){
        if ( get_option('purifycss_livemode')=='1' ){
            return true;
        }
        return false;
    }

    /**
     * Check if TEST mode enabled
     *
     * @return void
     */
    static public function check_test_mode(){
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php"); 
        }

        $cur_user = wp_get_current_user();
        if ( get_option('purifycss_testmode')=='1' && $cur_user->ID!==0 ){
            return true;
        }
        return false;
    }

}

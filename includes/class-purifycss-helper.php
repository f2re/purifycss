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
    public static $folder = 'public/generatedcss/';

    /**
     * filename of style
     *
     * @var string
     */
    public static $style = 'style.min.css';

    /**
     * inline style file name
     *
     * @var string
     */
    public static $inline_style = 'inline.css';

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
        if ( file_exists( $file ) ) unlink($file);
        file_put_contents($file, $content);

        // store to db
        update_option( 'purifycss_css', $content );

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

    /**
     * write to db css map and files
     *
     * @param [type] $map
     * @param [type] $css
     * @return void
     */
    static public function save_css_to_db($css){
        global $wpdb;   
        $table_name = $wpdb->prefix . "purifycss";
        // clean db
        $wpdb->query("TRUNCATE TABLE $table_name");
        // clean files
        $files = glob(plugin_dir_path( dirname( __FILE__ ) ) . self::$folder.'*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)){
                unlink($file); // delete file
            }
        }

        // prepared array
        $todb   = [];
        // inlinestyles
        $inline = "";
        // iterate over map
        foreach ($css as $_obj){
            // check inline styles
            if ( isset($_obj['inline']) && $_obj['inline']==True ){
                $css_identifier = self::get_css_id_by_content($_obj['original']['content']);
            }else{
                $css_identifier = $_obj['url'];
            }

            // save to file
            $filename = md5($css_identifier.uniqid()).'.css';

            $_obj = apply_filters('purifycss_before_filesave', $_obj);
            file_put_contents( plugin_dir_path( dirname( __FILE__ ) ) . self::$folder.$filename , $_obj['purified']['content']);

            $todb[] = [
                'orig_css' => $css_identifier,
                'css'      => '../' . self::$folder.$filename
            ];

        }

        // save to db
        $values =  array_reduce( $todb, function( $acc, $item ) {
            $acc[] =" ( '".$item['orig_css']."','".$item['css']."' ) ";
            return $acc;
        } );

        if ( count($values)>0 ){
            $wpdb->query("INSERT INTO $table_name
                            (orig_css,css)
                            VALUES ".join(',',$values).";");
        }

        return;
    }


    static public function get_css_id_by_content($content) {
        return substr(trim(preg_replace('/\s+/', ' ', $content)), 0, 512);
    }

}

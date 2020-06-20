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

/**
 * The file that defines htlper function
 *
 * @since      1.0.0
 * @package    Purifycss
 * @subpackage Purifycss/includes
 * @author     F2re <lendingad@gmail.com>
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

}

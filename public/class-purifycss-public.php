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

            if (strpos($style, 'purified') !== false) {
                continue;
            }

            if (isset($_GET['keep']) && in_array($wp_styles->registered[$style]->handle,explode(",",$_GET['keep']))) {
                continue;
            }


            $src = $wp_styles->registered[$style]->src;
            $files = $wpdb->get_results( "SELECT css from $table_name WHERE  `orig_css` LIKE '%$src%' ;" );
            foreach ($files as $file){
                // there is a purified version, so remove original inline styles and enqueue corresponding file

                // check for inline extra
                $inline_style = $wp_styles->print_inline_style($wp_styles->registered[$style]->handle, false);
                $inline_style_purified = false;
                if ($inline_style) {
                    $inline_style_purified = $this->get_corresponding_css($inline_style);
                }

                // check for deps
                $deps = [];
                foreach ($wp_styles->registered[$style]->deps as $dep) {
                    $newdep = $this->get_style_dependents($wp_styles->registered[$dep]->src);

                    wp_register_style($dep.'_purified', $newdep);

                    if ($newdep) {
                        $deps[] = $dep.'_purified';
                    } else {
                        $deps[] = $dep;
                    }
                }

                wp_dequeue_style($wp_styles->registered[$style]->handle);
                wp_enqueue_style($wp_styles->registered[$style]->handle . '_purified', plugin_dir_url( ( __FILE__ ) ).$file->css, $deps, false, 'all' );

                if ($inline_style_purified) {
                    wp_add_inline_style($wp_styles->registered[$style]->handle . '_purified', $inline_style_purified);
                }
            }
        }
	}


    /**
     * buffer html output
     *
     * @return void
     */
    public function start_html_buffer(){
        ob_start();
    }

    /**
     * end html buffer, parse and remove inline styles
     *
     * @return void
     */
    public function end_html_buffer(){

        global $wpdb;
        global $wp_styles;
        $table_name = $wpdb->prefix . "purifycss";

        $wpHTML = ob_get_clean();

        $matches = '';
        preg_match_all('/<style[^>]*>([^<]*)<\/style>/im', $wpHTML, $matches);



        foreach ($matches[1] as $key => $match) {
            $css_identifier = substr(trim(preg_replace('/\s+/', ' ', $match)), 0, 512);

            $files = $wpdb->get_results( "SELECT css from $table_name WHERE  `orig_css` LIKE '%$css_identifier%';" );

            foreach($files as $file) {
                // there is a purified version, so remove original inline styles and enqueue corresponding file
                // wp_enqueue_style('inline_style_'.$key.'_purified', plugin_dir_url( ( __FILE__ ) ).$file->css, array(), false, 'all' );

                $purifiedcss_inline = file_get_contents(plugin_dir_path( ( __FILE__ ) ).$file->css);
                $wpHTML = str_replace($match,$purifiedcss_inline, $wpHTML);
            }
        }

        //preg_replace('/<style[^>]*><\/style>/is','',$wpHTML);

        echo $wpHTML;
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
				//wp_enqueue_style( $this->plugin_name.'_inline', plugin_dir_url( ( __FILE__ ) ).'../' . PurifycssHelper::$folder.PurifycssHelper::$inline_style, array(), $this->version, 'all' );
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
				wp_enqueue_style( $this->plugin_name.'_'.$i, plugin_dir_url( ( __FILE__ ) ).$file->css, array(), $this->version, 'all' );
				$i++;
			}
			//wp_enqueue_style( $this->plugin_name.'_inline', plugin_dir_url( ( __FILE__ ) ).'../' . PurifycssHelper::$folder.PurifycssHelper::$inline_style, array(), $this->version, 'all' );
		} else {
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


	public function get_corresponding_css($content) {
        global $wpdb;
        $table_name = $wpdb->prefix . "purifycss";

        $css_identifier = PurifycssHelper::get_css_id_by_content($content);
        $files = $wpdb->get_results( "SELECT css from $table_name WHERE  `orig_css` LIKE '%$css_identifier%' ;" );

        foreach($files as $file) {
            return file_get_contents(plugin_dir_path( ( __FILE__ ) ).$file->css);
        }

        /*echo "<pre>";
        print_r('ERROR: didnt find anything in DB for following inline css');
        print_r($css_identifier);
        echo "</pre>";*/
        return $content;
    }

    public function get_style_dependents($depsrc) {
        global $wpdb;
        $table_name = $wpdb->prefix . "purifycss";

        $files = $wpdb->get_results( "SELECT css from $table_name WHERE  `orig_css` LIKE '%$depsrc%';" );

        foreach($files as $file) {
            return plugin_dir_url( ( __FILE__ ) ).$file->css;
        }

        return false;
    }

}

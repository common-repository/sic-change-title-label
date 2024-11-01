<?php
/**
 * Plugin Name: SIC Change Title Label
 * Plugin URI: http://www.strategic-ic.co.uk
 * Description: his is a plugin for updating the label of title field for each post type.From the settings menu -> you can access SIC Change Title option and it will list all the post types in the system with a text box. You can enter your custom label. If you leave it as blank the default label from Wordpress (Title) will be taken.
 * Version: 1.0
 * Author: Jipson Thomas
 * Author URI: http://www.jipsonthomas.com
 * License: A "Slug" license name e.g. GPL2
 *
 *
 * Copyright (c) 2014 Jipson Thomas <jipson@cstrategic-ic.co.uk>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

defined('ABSPATH') or die("Cannot access pages directly.");
class sic_change_title_class {
    private $options;
	private $defaults = array();

	public function __construct() {
		add_action('enter_title_here',array( $this, 'change_default_title' ));
        if ( is_admin() ){
            add_action('admin_menu', array( $this, 'add_sicchngtitle_menu' ));
            add_action('admin_init', array( $this, 'register_settings' ));
        }
    }
	
	function change_default_title( $title ){
	  $options = wp_parse_args(get_option('sic_chngttl'), null);
		$screen = get_current_screen();
		 if(isset($options[$screen->post_type.'_title_text']) && $options[$screen->post_type.'_title_text'] != ''){
			 $title = $options[$screen->post_type.'_title_text'];
		 }
		
		 
		return $title;
	}
   

    /* add menu */
	function add_sicchngtitle_menu () {
        add_options_page( 'SIC Change Title Settings', 'SIC Change Title', 'manage_options', 'sic_chngttl', array( $this, 'sicchng_set' ));
	}

    /* add menu page */
	function sicchng_set () {
        //include 'search_options.php';
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Post Title Settings</h2>
            <form method="post" action="options.php">
            <?php
                // Print out all hidden setting fields
                settings_fields( 'sic_chngttl_group' );
                do_settings_sections( 'sic_chngttl' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
	}

    function register_settings(){
        $args = array(
		   'public'   => true,
		);
		register_setting(
            'sic_chngttl_group', // Option group
            'sic_chngttl', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
		add_settings_section(
            'sic_setting_display', // ID
            'Display Settings', // Title
            array( $this, 'sic_setting_display' ), // Callback
            'sic_chngttl' // Page
        );
		
		$output = 'names'; // names or objects, note names is the default
		//$operator = 'and'; // 'and' or 'or'
		
		$post_types = get_post_types( $args, $output ); 
		
		foreach ( $post_types  as $post_type ) {
		
		   add_settings_field(
				$post_type.'title_text', // ID
				ucwords($post_type).' Title Text', // Title
				array( $this, 'title_text_callback' ), // Callback
				'sic_chngttl', // Page
				'sic_setting_display', // Section
				array('ptype' => $post_type)
			);
		    
		}

		
    }

     /* Sanitize each setting field */
    public function sanitize( $input ) {
		$args = array(
		   'public'   => true,
		);
		$output = 'names'; // names or objects, note names is the default
		//$operator = 'and'; // 'and' or 'or'
		
		$post_types = get_post_types( $args, $output ); 
		
		foreach ( $post_types  as $post_type ) { 
			if(!empty( $input[$post_type.'_title_text'] ) ) {
				$input[$post_type.'_title_text'] = sanitize_text_field( $input[$post_type.'_title_text'] );
			}
		}
		
        return $input;
    }

    /* Section text */
    public function sic_setting_display() {
        print 'Configure settings that control your post type title filed label display:';
    }

    function title_text_callback($args){
        $options = wp_parse_args(get_option('sic_chngttl'), null);//print_r( $options);
	    printf(
            '<input type="text" id="'.$args['ptype'].'_title_text" name="sic_chngttl['.$args['ptype'].'_title_text]" value="%s" />',
            esc_attr( $options[$args['ptype'].'_title_text'])
        );
		 
	   
    }
   
 /* Add settings link on plugin page */
	function settings_link($links) {
		$settings_link = '<a href="options-general.php?page=sicchngtitle">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
    function plugin_meta_links( $links, $file ) {
        $plugin = plugin_basename(__FILE__);
        if ( $file == $plugin ) {
            $links[] = '<a href="http://www.strategic-ic.co.uk/" target="_blank">Visit Strategic-IC</a>';
            $links[] = '<a href="mailto:jipson@strategic-ic.co.uk?subject=[SICChangeTitle]">Email Author</a>';
        }
        return $links;
    }

    
   

   
}

/* initiate class */
$sictitle_obj = new sic_change_title_class;
<?php


	namespace WPJump;
	
	
    class Setting{
        /**
         * Construct the plugin object
         */
        public function __construct(){
            // register actions
            add_action('admin_init', array(&$this, 'admin_init'));
            add_action('admin_menu', array(&$this, 'add_menu'));
        } // END public function __construct
    
        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init(){
	
            // register your plugin's settings
			register_setting('wp_jump_plugin_template-group', 'wpjump_parent_domail');
 
            // add your settings section
            add_settings_section(
                'wp_jump_plugin_template-section', 
                 __('Parent Site','wp-jump'),
                array(&$this, 'settings_section_wp_jump_plugin_template'), 
                'wp_jump_plugin_template'
            );
        
            // add your setting's fields    

			add_settings_field(
                'wp_jump_plugin_template-wpjump_parent_domail', 
                'URL', 
                array(&$this, 'settings_field_input_text'), 
                'wp_jump_plugin_template', 
                'wp_jump_plugin_template-section',
                array(
                    'field'   => 'wpjump_parent_domail',
					'desc'	  => __('Enter site URL','wp-jump'),
					'default' =>  ''
                )
            );

            // Possibly do additional admin_init tasks
        } // END public static function activate
    
        public function settings_section_wp_jump_plugin_template(){
            // Think of this as help text for the section.
            echo __('Set up your global site with your jumps.','wp-jump');
        }
    
        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args){
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
			$get_value = get_option($field);
			$args['default'] = !empty($args['default']) ? $args['default'] : '';
            $value = (!empty( $get_value ) ) ? $get_value : $args['default'];
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" style="width:350px"/>', $field, $field, $value);
			echo (!empty($args['desc'])) ? '<p class="description">'.$args['desc'].'</p>' : '';
        } // END public function settings_field_input_text($args)
    

	  /**
         * This function provides textarea inputs for settings fields
         */
        public function settings_field_input_textarea($args){
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<textarea type="text" name="%s" id="%s" />%s</textarea>', $field, $field, $value);
        } // END public function settings_field_input_text($args)


	  /**
         * This function provides textarea inputs for settings fields
         */
        public function settings_field_input_text_editor($args){
            // Get the field name from the $args array
            $field = $args['field'];
			$settings = array();
			
            // Get the value of this setting
			$get_value = get_option($field);
			$args['default'] = !empty($args['default']) ? $args['default'] : '';
            $value = (!empty(	$get_value ) ) ? $get_value : $args['default'];
            // echo a proper input type="text"
			wp_editor( $value,  $field , $settings ); 
		
			echo (!empty($args['desc'])) ? '<p class="description">'.$args['desc'].'</p>' : '';
        } // END public function settings_field_input_text_editor($args)


		/**
		* This function preovides check inputs for settings fileds
		*/
		function settings_field_input_checkbox($args) {		
		  	// Get the field name from the $args array
          	$field = $args['field'];
           	// Get the value of this setting
           	$value = get_option($field);
	 	  	echo '<input name="' . $field . '" id="' . $field . '" type="checkbox" value="1" class="code" ' . checked( 1, $value , false ) . ' />';		
	 	}
	 

        /**
         * add a menu
         */     
        public function add_menu(){
            // Add a page to manage this plugin's settings
            add_options_page(
                __('WP Jump','wp-jump'), 
                __('WP Jump','wp-jump'), 
                'manage_options', 
                'wp_jump_plugin_template', 
                array(&$this, 'plugin_settings_page')
            );
        } // END public function add_menu()

        /**
         * Menu Callback
         */     
        public function plugin_settings_page(){
	
            if(!current_user_can('manage_options')){
	
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            // Render the settings template
			 $this->view();

        } // END public function plugin_settings_page()


		public function view(){ ?>
			<div class="wrap">
			    <h2><?php _e('WP Jump','wp-jump') ?></h2>					
			    <form method="post" action="options.php"> 
			        <?php @settings_fields('wp_jump_plugin_template-group'); ?>
			        <?php @do_settings_fields('wp_jump_plugin_template-group'); ?>

			        <?php do_settings_sections('wp_jump_plugin_template'); ?>

			        <?php @submit_button(); ?>
			    </form>
			</div>
		<?php }

    } // END class The_Chamelon_Plugin_Template_Settings



	$WPJump_Setting = new Setting();


?>

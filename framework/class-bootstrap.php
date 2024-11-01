<?php


namespace WPJump;
	
	class Bootstrap{
		
		public $config;
	
		function __construct(){

			global $config;
			$this->config = Config::getInstance();	

			//set parts
			$parts = $this->set_parts();

			//Post Types
			$PostTypes = new Post_Types( $parts );
			
			//Post Met 
			$Meta_Boxes = new Meta_Boxes( $parts );
				
			apply_filters( 'wp_feed_cache_transient_lifetime', function( $sec ) {
				return 7200;
			});

			//add menu bar
			add_action( 'wp_before_admin_bar_render', array($this, 'wp_admin_bar'), 22 );
						
			//rss modification
			add_action('rss2_item',array(&$this, 'add_custom_wpjump_fields_to_rss' ));

			//Menager columns dont work...any help is :* 
			//add_action('manage_posts_custom_column', array(&$this, 'set_columns' ), 10, 2);
			//add_filter('manage_edit-wpjump_columns' , array(&$this,'edit_columns' ),  10, 2);
			
			//setting link in plugins list
			add_filter( 'plugin_action_links_wp-jump/wp-jump.php', array(&$this, 'add_action_links' ) );
	
		}
		
		
		
		/**
		 * 	Setting link in plugins list
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 **/
		function add_action_links ( $links ) {
		 	$plugin_links = 
				array(
		 		'<a href="' . admin_url( 'options-general.php?page=wp_jump_plugin_template' ) . '">'. __('Settings','wp-css-generator') .'</a>',
		 		);
			return array_merge(  $plugin_links, $links );
		}
		
		
			

		/*	
		
		//edit GW post type columns
		function edit_columns($columns) {

		    return array(
		        'cb' 	=> '<input type="checkbox" />',
		        'title' => __('Title', 'wp-jump'),	  
				'link'	=> __('Link', 'wp-jump'),			
		    );
		}
		
		//edit GW post type columns
		function set_columns($column, $post_id){
			global $post;
		
			switch ($column){

				case 'link':

					$link = get_post_meta($post_id , 'link', true );
					
					echo $link ;

				break;
				
				default;
				
					echo "Test";
				break;
				

			}
		}*/


		
		/**
		 * RSS modification 
		 *
		 */
		function add_custom_wpjump_fields_to_rss() {
		    if(get_post_type() == 'wpjump') {		
			 	$my_meta_value = get_post_meta(get_the_ID(), 'link', true);
		        ?>
		        <site_url><?php echo $my_meta_value ?></site_url>
		        <?php
		    }
		}
	
		
	
		/**
		 * Add menu bar
		 *
		 */		
		function wp_admin_bar() {
			global $wp_admin_bar;
	
			 // Get RSS Feed(s)
			include_once( ABSPATH . WPINC . '/feed.php' );
			
			apply_filters( 'wp_feed_cache_transient_lifetime', function( $sec ) {
				return 7200;
			});
							
			// Get a SimplePie feed object from the specified feed source.
			$rss = fetch_feed( trim(preg_replace('/([^:])(\/{2,})/', '$1/', WPJUMP_PARENT_DOMAIN.'/feed/?post_type=wpjump'), '/') );
		
	 		$maxitems = 0;
			if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

			    // Figure out how many total items there are, but limit it to 5. 
			    $maxitems = $rss->get_item_quantity( 0 ); 

			    // Build an array of all the items, starting with element 0 (first element).
			    $rss_items = $rss->get_items( 0, $maxitems );

			endif;
			//print_r($rss_items);

			//Add a link called 'CSS Groups'...
			$wp_admin_bar->add_menu(array(
						'id'    => 'wp-jump',
						'title' => __('Jump', 'wp-jump'), 
						'href'  => site_url()
					));
			
			 if ( $maxitems == 0 ) :
								 
			 else : 			    
			     $i=0; foreach ( $rss_items as $item ) :		
					$site_url =	$item->get_item_tags('', 'site_url');
				
					//print_R($site_url);	
						$wp_admin_bar->add_menu( array(
							'id'    =>  'wp-jump-'.$i,
							'title' =>  $item->get_title(). ' &rarr;',
							'href'  => 	$site_url[0]['data'],
							'parent'=> 'wp-jump'
						));
		
		        $i++; endforeach; 
			  endif; 
		
		
		
		}
	
	
		/**
		 * Load plugin textdomain.
		 *
		 * @since 1.0.0
		 */
		function load_textdomain() {

		  load_plugin_textdomain( 'wp-jump', false,  $this->config->DIR .'/i18n' ); 
		}
	
				
		/**
		 * 	Set all parts in to $this->part name  PARTS
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 **/
		 private function set_parts(){
						
			//register all parts like part name == class name	
			foreach (glob( 	$this->config->DIR .'/parts/*', GLOB_ONLYDIR ) as $paths) :

				$dir = explode('/', $paths);	
				$part =  end($dir);

				$this->parts[] = $part;

				//namespace class name, replace the ' fix 
				$class_name = str_replace("'",'',$this->config->namespace."\'".$part);
				
				$this->{$part}  = new $class_name($this);	

				$parts[$part]  	=  $this->{$part};	

			endforeach;
			
			
			return 	$parts;
			
		}
			


	}
	
?>
<?php

	namespace WPJump{

 /**
   * Post Type  
   * 
   * 
   * @package    WordPress
   * @subpackage The Chameleon
   * @since 	 The Chameleon 3.1.0
   * @author     Goran Petrovic <goran.petrovic@godev.rs>
   *
   * @version 2.0.0
   *
   */


	class Post_Types{
		
		var $parts = array();
		var $post_types;

		function __construct( $parts ){

			$this->parts = $parts;					

			$this->get_post_types();			
		
			add_action( 'init', array(&$this, 'set_post_types' ) );

			add_action( 'after_switch_theme',  array(&$this, 'plugin_rewrite_flush' ) );		
			register_activation_hook( 'taskbookers/taskbookers.php',  array(&$this, 'plugin_rewrite_flush') );			
	
		}

		
		/*refresh roote*/
		function plugin_rewrite_flush() {
		    // First, we "add" the custom post type via the above written function.
		    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
		    // They are only referenced in the post_type column with a post entry, 
		    // when you add a post of this CPT.
		    $this->set_post_types();


		    // ATTENTION: This is *only* done during plugin activation hook in this example!
		    // You should *NEVER EVER* do this on every page load!!
		    flush_rewrite_rules();
		}
	

		/**
		 * register post types from all parts
		 *
		 * @return  array
		 */
		function get_post_types(){		
		
			foreach ($this->parts as  $key => $part ) :			
				if( method_exists($part, 'post_type') ) :	
					$this->post_types[ $key ] = $part->post_type();					
				endif;
			endforeach;
			
			return 	$this->post_types;
					
		}
	
	
	
		/**
		 * register post types 
		 *
		 * @return void
		 */	
		function set_post_types() {
		
		if ($this->post_types) 

			foreach ($this->post_types as $key => $value) :
					
						register_post_type( $value['id'],
							array(
								'labels' 			=> 
									array(
										'name' 				 => $value['label'], 
										'singular_name' 	 => $value['single-label'],
										'add_new' 			 => 'Add '. $value['single-label'],
										'add_new_item'  	 => 'Add New '. $value['single-label'], 
										'edit' 				 => 'Edit', 
										'edit_item' 		 => 'Edit '. $value['single-label'],
										'new_item' 			 => 'New '. $value['single-label'], 
										'view' 				 => 'View '.  $value['single-label'], 
										'view_item' 		 => 'View '. $value['single-label'], 
										'search_items'  	 => 'Search '. $value['label'], 
										'not_found' 		 => 'No ' . $value['single-label'] . ' found', 
										'not_found_in_trash' => 'No ' . $value['single-label'] . ' in Trash', 
										'parent' 			 => 'Parent ' . $value['single-label'],
									),
								'public' 			 => isset( $value['public'] ) 			  ? $value['public'] : true,
							    'publicly_queryable' => isset( $value['publicly_queryable'] ) ? $value['publicly_queryable'] : true, 
							    'show_ui' 			 => isset( $value['show_ui'] ) 			  ? $value['show_ui'] : true,  
							    'show_in_menu' 		 => isset( $value['show_in_menu'] ) 	  ? $value['show_in_menu'] : true, 
							    'query_var' 		 => isset( $value['query_var'] ) 		  ? $value['query_var'] : true,
							    'rewrite' 			 => isset( $value['rewrite'] ) 			  ? $value['rewrite'] : true, 
							    'has_archive' 		 => isset( $value['has_archive'] ) 		  ? $value['has_archive'] : true,  
							    'hierarchical' 		 => isset( $value['hierarchical'] )       ? $value['hierarchical'] : false, 
								'menu_position' 	 => isset( $value['menu_position'] )      ? $value['menu_position'] : 15, 			
								'supports' 			 => $value['supports'], 
								'rewrite' 			 => array( 'slug' => $value['id'], 'with_front' => true  )
							)
						);


						// Add new taxonomy, make it hierarchical (like categories)
						foreach ($value['taxonomies'] as $taxonomy) :

							$labels = 
								array(
							  	  	'name' 				=> $taxonomy['single-label'], 
								    'singular_name' 	=> $taxonomy['single-label'], 
								    'search_items' 		=> 'Search'. $taxonomy['label'], 
								    'all_items' 		=> 'All '.$taxonomy['label'], 
								    'parent_item' 		=> 'Parent '.$taxonomy['single-label'], 
								    'parent_item_colon' => 'Parent '.$taxonomy['single-label'].':', 
								    'edit_item' 		=> 'Edit '.$taxonomy['single-label'], 
								    'update_item' 		=> 'Update '.$taxonomy['single-label'], 
								    'add_new_item' 		=> 'Add New '.$taxonomy['single-label'], 
								    'new_item_name' 	=> 'New '.$taxonomy['single-label'].' Name', 
								    'menu_name' 		=> $taxonomy['label'], 
							  ); 	

						 register_taxonomy(
							 $taxonomy['id'], 
									array( $value['id'] ), 
								        array(
							 	            'hierarchical'=> $taxonomy['hierarchical'],
								            'labels' 	  => $labels,
								            'show_ui' 	  => true,
								            'query_var'   => true,
								            'rewrite' 	  => array( 'slug' =>  $taxonomy['id']  ),
					 			        	)
								        );

					endforeach;
					
			endforeach;
	
		


			add_theme_support( 'post-thumbnails');


		}	
	

	
	}	
	

}

?>
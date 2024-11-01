<?php

namespace WPJump{
	
	use WPCSSGenerator;

   /**
    * CSS part class   
    *
    * @author Goran Petrovic
    * @since 1.0
    *
    **/
	class Wpjump extends Part{


		public $view 	 = 'taskbookers';
		public $template = 'taskbookers';
		public $path 	 = 'parts/Taskbookers/';		
	
		
		function __construct(){
	
		}

		/**
		 * 	Create post type 
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 **/
		function post_type(){
		
			
			if (WPJUMP_PARENT_DOMAIN == site_url()) :	
				
				return	$this->post_type = 
							array(
								'id'		   => 'wpjump',
								'label'		   => 'Jumps',
								'single-label' => 'Jump',
								'public'	   => true,
								'has_archive'  => true,
								'hierarchical' => 1,
								'supports'	   => array('title'), 

								'taxonomies'   => array(),
								);			
				
			else:	
			
				return	$this->post_type = 
							array(
								'id'		   => 'wpjump',
								'label'		   => 'Jumps',
								'single-label' => 'Jump',
								'public'	   => true,
								'has_archive'  => true,
								'hierarchical' => 1,
								  'show_ui' 			 => false,  
								  'show_in_menu' 		 => false,
								'supports'	   => array('title'), 

								'taxonomies'   => array(),
								);
					

			endif;

		}


		
		/**
		 * 	Post meta options 
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 **/
		function meta_boxes(){

			global $data;
				return $this->post_meta = 
									array(

										array(	
											'post_types' => array('wpjump'),
											'title'		 => __('WP Jump','wp-jump'),
											'id'		 =>	'tbs_box1',
											'context'	 =>'normal',
											'fields' 	 => array(

												
															array(
																'type'	  => 'text',
																'name'	  => 'link',
																'title'	  => __('Link','wp-jump'),
																'default' => '',
																'desc'	  => '',

																

																'attr'	  => array('class'=>'', 'placeholder'=>'https://wordpress.org/')
																),
															
						
																
															)//fileds

											),//box1


									




								);

		}
	

	

	
	}

}
?>
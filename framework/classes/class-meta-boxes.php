<?php

	namespace WPJump;
	
 /**
   * Meta Boxes 
   * 
   * @author     Goran Petrovic <goran.petrovic@godev.rs>
   * @package    WordPress
   * @subpackage GoX
   * @since 	 GoX 1.0.0
   *
   * @version 1.1.0
   * add text area, wp media
   */
	class Meta_Boxes{
		
		var $parts = array();
		var $meta_boxes;
		var $meta_boxes_fields;
		var $slug;
		
		function __construct( $parts ){
			


			$config = Config::getInstance();
			$this->slug = $config->slug;
			
			$this->parts = $parts;					
		
			//post Post Meta Boxes
			$this->set_meta_boxes_fileds();

			//add Post Meta Boxes
			add_action( 'add_meta_boxes', array( &$this, 'render_meta_boxes' ) );

			//save Post Meta Boxes
			add_action( 'save_post', array( &$this, 'meta_box_save' ) );

			//get post meta values
			add_action('init', array(&$this,'get_post_meta_values') );
			add_action('wp', array(&$this,'get_post_meta_values') );
			
			
		}
		
		
		/**
		 * 	Get meta values
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return void
		 **/
		function get_post_meta_values(){

			global $ThemePostMeta;
			
			if ( is_singular() or is_single() or is_page() ) :										
				$get_post_meta = get_post_meta( get_the_ID(), $this->slug.'meta', TRUE);						
					foreach ( $this->parts as $key => $part ) :
						if ( !empty( $this->meta_boxes_fields[ $key ] ) ) :	
							foreach ( $this->meta_boxes_fields[ $key ] as $value ) :
									foreach ( $value['fields'] as $field ) :									
										$name 	 				   = $field['name'];										
										$default 				   = !empty( $field['default'] ) ? $field['default'] : '';										
										$ThemePostMeta[ $name ] = isset( $get_post_meta[ $name ] ) ? $get_post_meta[ $name ] : $default;
										$this->PostMeta[ $name ] 	   = !empty( $get_post_meta[ $name ] ) ? $get_post_meta[ $name ] : $default;
									endforeach;
							endforeach;
						endif;
					endforeach;		
			endif;


		}

		/**
		 * 	Set boxes from all parts 
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return void
		 **/	
		function set_meta_boxes_fileds(){

			foreach ($this->parts as  $key => $part ) :			
				if( method_exists($part, 'meta_boxes') ) :														
					$this->meta_boxes[ $key ] = $part->meta_boxes();					
					if ( !empty( $this->meta_boxes[	$key ] ) ) :
						foreach ($this->meta_boxes[	$key ] as  $value) :							
							$this->meta_boxes_fields[ $key ][] = array('post_types' => $value['post_types'], 'fields'=>$value['fields']);
						endforeach;
					endif;
				endif;
			endforeach;

		}

		/**
		 * 	Make meta boxes
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return void
		 **/
		 function render_meta_boxes( $post_type ){

			foreach ( $this->parts as $key => $part ) :														
				if ( !empty( $this->meta_boxes[	$key ] ) ) :
					foreach ($this->meta_boxes[ $key ] as $value) :						
						 $context  = !empty( $value['context'] )  ? $value['context']  : 'normal';
						 $priority = !empty( $value['priority'] ) ? $value['priority'] : 'default'; 		
						 self::add_meta_box( $post_type, $this, $value['post_types'], $value['id'], $value['title'], $context, $priority   );
					endforeach;
				endif;
			endforeach;		

		} 

		/**
		 * 	Render fields 
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return void
		 **/
		 function render_meta_boxes_content( $post, $data ){

			foreach ( $this->parts as $key => $part ) :									
				if ( !empty( $this->meta_boxes[	$key ] ) ) :
					foreach ( $this->meta_boxes[ $key ] as $value ) :
						if ( $data['id'] == $value['id'] ) :
														
							 //description
							 $desc = !empty( $value['desc'] ) ? $value['desc'] : NULL;

							 self::add_meta_box_content( $post, $value['fields'], $desc);							
						endif;
					endforeach;
				endif;
			endforeach;

		}

		/**
		 * 	Save custom meta box values
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return void
		 **/
		 function meta_box_save( $post_id ){


			// Check if our nonce is set.
			if ( ! isset( $_POST['the-chameleon_custom_box_nonce'] ) )
				return $post_id;

			$nonce = $_POST['the-chameleon_custom_box_nonce'];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'the-chameleon_custom_box' ) )
				return $post_id;

			// If this is an autosave, our form has not been submitted,
	                //     so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				return $post_id;

			// Check the user's permissions.
			if ( 'page' == $_POST['post_type'] ) {

				if ( ! current_user_can( 'edit_page', $post_id ) )
					return $post_id;

			} else {

				if ( ! current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}

			/* OK, its safe for us to save the data now. */

			if ( !empty( $_POST['meta'] ) ) :
				
				$meta_values = $_POST['meta'];
				update_post_meta( $post_id,	$this->slug.'meta', $meta_values );

				//insert all meta
				foreach ($_POST['meta'] as $key => $value) :
					update_post_meta( $post_id, $key, $value );					
					update_post_meta( $post_id, $this->slug.$key, $value );	
				endforeach;
				

			endif;


		}
		

		/**
		 * 	Create Meta box 
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html
		 **/
		static function add_meta_box( $post_type, $this, $post_types = null , $id = 'meta_box_content', $title = 'Setup', $context ='normal', $priority = 'default' ){

			//https://codex.wordpress.org/Function_Reference/add_meta_box

			/*$post_types = array('page'); */    //limit meta box to certain post types
            if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					$id
					, $title
					, array( &$this, 'render_meta_boxes_content' )
					, $post_type
					, $context  //'normal', 'advanced', or 'side'
					, $priority //'high', 'core', 'default' or 'low') 
				);
            }



		}
		
		/**
		 * 	Meta box Content
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html
		 **/		
	 	static function add_meta_box_content( $post, $fields = array(), $desc = '' ){

			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'the-chameleon_custom_box', 'the-chameleon_custom_box_nonce' );


				echo '<style>.the_chameleon_metabox input, 
				.the_chameleon_metabox textarea{width:100%; padding:5px;}, 
				.the_chameleon_metabox select{padding:5px !important;}
				.the_chameleon_metabox input[type=checkbox]{width:20px !important;}
				table#page_builder tr td{width:100%; border-top:1px solid #eeeeee !important; padding:5px 0px;}
				table#page_builder tr:first-child td{width:100%; border-top:0px solid #eeeeee !important; padding:5px 0px;}
				</style>';

				$table_id = ($fields[0]['type'] =='page_builder') ? 'page_builder' : NULL;

			 	echo '<table id="'.$fields[0]['type'].'" class="the_chameleon_metabox" style="width:100%;">
						<tbody>';
						
					//description
					echo !empty($desc) ? '<tr><td>' . self::desc($desc) . '</td></tr>' : NULL ;
				
				
						foreach ($fields as $key => $field) :
		
							$type 			  = isset($field['type']) ? $field['type'] : 'text';
							$field['title']   = isset($field['title']) ? $field['title']: '';
							$field['attr'] 	  = isset($field['attr']) ? $field['attr']: '';
							$field['default'] = isset($field['default']) ? $field['default']: '';
							$desc			   = (!empty( $field['desc'] )) ? $field['desc'] : '';
					
							if($type == 'select' or $type == 'page_builder') : 
								$other = (!empty( $field['choices'] )) ? $field['choices'] : array();
							elseif($type == 'date'):
								$other = (!empty( $field['format'] )) ? $field['format'] : '';
							else:	
								$other = '';
							endif;
							
							self::{$type}($post, $field['name'], $field['title'], $field['default'], $desc, $field['attr'], $other);
				
						endforeach;
		
			   echo "</tbody></table>";
			
			

		}
		static function none( $post, $id, $label, $value, $desc = '' , $attr = array() ){}	
		/**
		 * 	Text filed
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html
		 **/		
	 	static function text( $post, $id, $label, $value, $desc = '' , $attr = array() ){ 
		
			$id 		= str_replace( "-","_", sanitize_title( $id ) ) ;			
		 	$post_metas 	= self::get_post_meta($post->ID); 
			$post_meta 	= \get_post_meta($post->ID,  $id, true); 
			$value 		= !empty( $post_meta[$id] ) ? $post_meta[$id] : $post_meta ; ?>
			<tr>
				<td id="td-<?php echo $id ?>" class="left" style="vertical-align: middle;">
					<?php echo !empty($label) ? "<p><strong>{$label}</strong></p>": ''; ?>
					<?php echo Form::input("meta[$id]", $value, $attr); ?>
					<?php echo self::desc($desc); ?>
				</td>
			</tr>

		<?php }

		/**
		 * 	Textarea filed
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html
		 **/		
	 	static function textarea( $post, $id, $label, $value, $desc = '' , $attr = array() )
		{ 

			$id 		= str_replace( "-","_", sanitize_title( $id ) ) ;			
		 	$post_meta 	= self::get_post_meta( $post->ID ); 
			$value 		= !empty( $post_meta[$id] ) ? $post_meta[$id] : $value ; ?>
			<tr>
				<td id="td-<?php echo $id ?>"  class="left" style="vertical-align: middle; width:100%;">
					<?php echo !empty($label) ? "<p><strong>{$label}</strong></p>": ''; ?>
					<?php echo Form::textarea("meta[$id]", $value, $attr); ?>
					<?php echo self::desc($desc); ?>
				</td>
			</tr>

		<?php }
		/**
		 * 	Select filed
		 *   
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html
		 **/
		static function select( $post, $id, $label, $value, $desc = '' , $attr = array(), $choices = array() ){ 
			
			$name 		= str_replace( "-","_", sanitize_title( $id ) ) ;
			$id 		= str_replace( "-","_", sanitize_title( $id ) ) ;			
		 	$post_metas = self::get_post_meta($post->ID); 
			$post_meta 	= \get_post_meta($post->ID,  $id, true); 
			$value 		= !empty( $post_meta[$id] ) ? $post_meta[$id] : $post_meta ; ?>


			<tr>
				<td id="td-<?php echo $id ?>" class="left" >
					<?php echo !empty($label) ? "<p><strong>{$label}</strong></p>": ''; ?>
			
					<?php echo Form::select("meta[$name]", $value, $choices, $attr); ?><br />	
					<?php echo self::desc($desc); ?>
				</td>
			</tr>

		<?php
		
	
		}

		
		/**
		 * 	checkbox filed
		 *   
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html
		 **/
		static function checkbox( $post, $id, $label, $value, $desc = '' , $attr = array(), $choices = array() ){ 
			
			$name  		= str_replace( "-","_", sanitize_title( $id ) );
			$post_meta 	= self::get_post_meta( $post->ID ); 
			$value 		= isset( $post_meta[ $name ] ) ? $post_meta[ $name ] : $value ; 
			
				/*	echo $value ;*/
			?>

	
			<tr>
				<td id="newmetaleft" class="left">
					<label><input type="checkbox" name="meta[<?php echo $name ?>]" value="1" <?php checked(	$value , 1 ); ?>  ><?php echo !empty( $label ) ? "<strong> $label </strong>": ''; ?> </label>	
					<br />	
					<?php echo self::desc($desc); ?>
				</td>
			</tr>

		<?php
		
	
		}
		
		/**
		 * 	checkbox filed
		 *   
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html
		 **/
		static function wp_media( $post, $id, $label, $value, $desc = '' , $attr = array() ){ 
		
		
			$name  		= str_replace( "-","_", sanitize_title( $id ) );
			$post_meta 	= self::get_post_meta( $post->ID ); 
			$value 		= isset( $post_meta[ $name ] ) ? $post_meta[ $name ] : $value ;  ?>
	
			<tr>
				<td id="newmetaleft" class="left">
					<?php echo !empty($label) ? "<p><strong>{$label}</strong></p>": ''; ?>

					<img src="<?php echo $value ?>" alt="-" id="meta<?php echo $id ?>" style="width:250px;"/>
					<br />

					<?php echo Form::wp_image("meta[$name]", $value, $attr); ?><br />	
					<?php echo self::desc($desc); ?>
				</td>
			</tr>

		<?php
			
			}
			

		/**
		 * 	Sanitize filed name   
		 *   
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return string
		 **/
		function sanitize_name( $name ){

			return str_replace('-', '_', sanitize_title(  (string)$name ) );
					
		}
		
		/**
		 * 	 Get values  
		 *   
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return void
		 **/		
	    static function get_post_meta( $post_id ){
		
			global $config;
			$slug = $config->slug;
			
			return get_post_meta($post_id, $slug.'meta', TRUE);
	
		}
		
		
		/**
		 * 	 Description  
		 *   
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html
		 **/		
		static function desc( $desc ){
			
 			 return ($desc!='') ? '<p class="howto">'.$desc.'</p>' : NULL;
			
		}
		
		
		/**
		 * 	Filed atributes 
		 *   
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @return html input atributes name = "value"
		 **/	
		static function attr( $attrs )
		{
			
			$result = '';
			foreach ( $attrs as $key => $value) :
				$result .= $key. '="' .$value.'" ';
			endforeach;
			
		  return $result; 
			
			
		}
		
		
	}



	
?>
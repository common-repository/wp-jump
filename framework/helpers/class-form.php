<?php

	namespace  WPJump;	
	/**
	 * Form Helper 
	 * 
	 * 
	 * @author 		Goran Petrovic <goran.petrovic@godev.rs>
	 * @package    	WordPress
	 * @subpackage 	The Chameleon
	 * @since 		The Chameleon 3.1.0
	 *
	 * @version 1.0.0
	 *
	 */
	
	class Form{
		
		/**
		 * 	Input field
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @var string $name -  filed name, format _ or -, filed_name
		 * @var string $value - default value
		 * @var array  $attr  - html attributes
		 *
		 * @return <input type="text" ...>
		 **/
		static function input( $name, $value = '', $attr = array() )
		{ 
			//filetr filed name 
			$id   = str_replace( "-","_", sanitize_title( $name ) ) ;	
			//colect atribute whic is remove in static function attr			
			$id   = !empty( $attr['id'] ) ? $attr['id'] : $id  ;
			$type = !empty( $attr['type'] ) ? $attr['type'] : 'text';
							
			return '<input id="' . esc_attr( $id ) . '" type="' . esc_attr( $type ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" ' . self::attr($attr) . ' >';
				
		}
		
		/**
		 * 	Input field
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @var string $name -  filed name, format _ or -, filed_name
		 * @var string $value - default value
		 * @var array  $attr  - html attributes
		 *
		 * @return <input type="text" ...>
		 **/
		static function textarea( $name, $value = '', $attr = array() ){ 
			//filetr filed name 
			$id   = str_replace( "-","_", sanitize_title( $name ) ) ;	
			//colect atribute whic is remove in static function attr			
			$id   = !empty( $attr['id'] ) ? $attr['id'] : $id  ;
			$type = !empty( $attr['type'] ) ? $attr['type'] : 'text';
							
			return '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" ' . self::attr($attr) . ' >'.esc_textarea( $value ).'</textarea>';
				
		}
		
		/**
		 * 	Select box
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @var string $name -  filed name, format _ or -, filed_name
		 * @var string $value - default value
		 * @var array $choices - options for sellect in array 'value' => 'name'
		 * @var array  $attr  - html attributes
		 *
		 * @return <select name=..>
		 **/
		static function select( $name, $value, $choices, $attr = array() ){			
			$id = str_replace( "-","_", sanitize_title( $name ) ) ;
			//colect atribute whic is remove in static function attr			
			$id   = !empty( $attr['id'] ) ? $attr['id'] : $id  ; ?>	
			<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo self::attr( $attr ) ?> >
				<?php foreach ( $choices as $key => $choice ) : ?>
					<option value="<?php echo esc_attr( $key ) ?>" <?php selected( esc_attr( $value ), $key, 1 );?> ><?php echo esc_attr( $choice ) ?></option>
				<?php endforeach; ?>
			</select>	
			<?php
						
		}
		
		
		
		
		/**
		 * 	Submit 
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @var string $id 
		 * @var string $title 
		 *
		 * @return <label name=..>
		 **/			
		static function submit( $name, $title, $attr= array() ){				

			return '<input type="submit" name="' . $name . '" value="'. $title .'" ' . self::attr( $attr ) . '>';

		}
		
		
		/**
		 * 	Label 
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @var string $id 
		 * @var string $title 
		 *
		 * @return <label name=..>
		 **/			
		static function label( $id, $title ){				
			
			return '<label for="' . $id . '">' . $title . '</label>';

		}

		/**
		 * 	Input atributes return html attr from array
		 *   
		 *
		 * @author Goran Petrovic
		 * @since 1.0
		 *
		 * @var array $attrs atributes in array( 'name' => 'value')
		 * @var array $filter atributes for remove array( 'name', 'name1')
		 *
		 * @return html attributs 
		 **/	
		static function attr( $attrs, $filter = array() ){
			
				$filter = array('type', 'id');
				$result = '';

				foreach ( $attrs as $key => $value) :
					//if ont in filetr var 
					if (!in_array($key, $filter)) :			
						$result .= $key. '="' .$value.'" ';	
					endif;	
				endforeach;

			return $result; 


		}
		
		
	}
	


?>
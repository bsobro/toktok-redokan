<?php

/**
* A singleton class responsible for creating the HTML of a settings field in the admin area.
 *
 * @package WPFEPP
 * @since 2.3.0
*/
class WPFEPP_Field_Renderer
{
	/**
	 * Instead of calling the constructor, other classes are supposed to use this function to get the existing instance of the class.
	 *
	 * @return $instance An instance of the class.
	 **/
	public static function get_instance(){
		static $instance = null;
		if($instance == null){
			$instance = new WPFEPP_Field_Renderer();
		}
		return $instance;
	}

	/**
	 * Takes an array of attributes and outputs a settings field.
	 *
	 * @var array $args Field arguments such as field type, current value etc.
	 **/
	public function render($args){

		if(isset($args['subgroup'])){
			$name = $args['group'].'['.$args['subgroup'].']'.'['.$args['id'].']';
		}
		else {
			$name = $args['group'].'['.$args['id'].']';
		}

		$current 	= isset($args['curr'][$args['id']]) ? $args['curr'][$args['id']] : '';
		$field_name = $name.'[val]';
		$type_name 	= $name.'[type]';
		$input_size = isset($args['size']) ? $args['size'] : '';

		if($args['type'] == 'roles'){
			wpfepp_print_roles_checkboxes( $field_name, $current );
		}
		
		if($args['type'] == 'text' || $args['type'] == 'int'):
		?>
			<input type="text" class="<?php echo 'wpfepp_'. $args['id']; ?>" name="<?php echo $field_name; ?>" value="<?php echo stripslashes(esc_attr($current)); ?>" size="<?php echo $input_size; ?>" />
		<?php
		endif;

		if($args['type'] == 'number'):
		?>
			<input type="number" min="0" max="1000" class="<?php echo 'wpfepp_'. $args['id']; ?>" name="<?php echo $field_name; ?>" value="<?php echo stripslashes((int)$current); ?>" />
		<?php
		endif;		

		if($args['type'] == 'checkbox' || $args['type'] == 'bool'):
			?>
				<input type="hidden" name="<?php echo $field_name; ?>" value="0" />
				<input type="checkbox" class="<?php echo 'wpfepp_'. $args['id']; ?>" name="<?php echo $field_name; ?>" value="1" <?php checked($current); ?> />
			<?php
		endif;

		if($args['type'] == 'select'):
			?>
				<select class="<?php echo 'wpfepp_'. $args['id']; ?>" name="<?php echo $field_name ?>">
					<?php foreach ($args['items'] as $key => $item): ?>
						<option value="<?php echo $key; ?>" <?php selected($current, $key); ?> ><?php echo $item; ?></option>
					<?php endforeach; ?>
				</select>
			<?php
		endif;

		if($args['type'] == 'textarea'):
			?>
				<textarea class="<?php echo 'wpfepp_'. $args['id']; ?>" name="<?php echo $field_name ?>"><?php echo stripslashes(esc_textarea($current)); ?></textarea>
			<?php
		endif;

		if($args['type'] == 'multicheckbox'):
		$roles = wpfepp_get_roles();
			foreach ($args['items'] as $key => $checkboxitem ):
				$item_field_name = $field_name."[$key]";
				$item_current = isset( $current[$key] ) ? $current[$key] : '';
		?>
			<input type="hidden" name="<?php echo $item_field_name ?>" value="0" />
			<input type="checkbox" name="<?php echo $item_field_name ?>" class="<?php echo $item_field_name ?>" value="1" <?php checked( $item_current ); ?> />
			<label for="<?php echo $item_field_name ?>"><?php echo $checkboxitem; ?></label><br/>
		<?php
			endforeach;
		endif;

		if(isset($args['desc'])):
			?>
				<p class="description"><?php echo $args['desc']; ?></p>
			<?php
		endif;

		?>
			<input type="hidden" name="<?php echo $type_name; ?>" value="<?php echo $args['type']; ?>" />
		<?php
	}
}
<?php

/**
 * A singleton class responsible for validating user input and making sure nothing malicious makes it to the database. It is used by WPFEPP_Plugin_Settings as well as WPFEPP_Form_Manager.
 *
 * @package WPFEPP
 * @since 2.3.0
 **/
class WPFEPP_Field_Validator
{
	/**
	 * Instead of calling the constructor, other classes are supposed to use this function to get the existing instance of the class.
	 *
	 * @return $instance An instance of the class.
	 **/
	public static function get_instance(){
		static $instance = null;
		if($instance == null){
			$instance = new WPFEPP_Field_Validator();
		}
		return $instance;
	}

	/**
	 * Removes potentially harmful tags from user input and makes sure the entered value is what it is supposed to be.
	 *
	 * @var array $items The items to be validated
	 * @return array An updated array of items.
	 **/
	public function validate($items){

		if(is_array($items))
		foreach ($items as $key => $item) {

			if(!is_array($item)){
				$items[$key] = $item;
			}

			elseif($item['val'] == ''){
				$items[$key] = $item['val'];
			}

			else {
				switch ($item['type']) {
					case 'int':
						$items[$key] = intval($item['val']);
						break;

					case 'bool':
						$items[$key] = (bool)$item['val'];
						break;

					case 'text':
						$items[$key] = stripslashes(wp_strip_all_tags($item['val']));
						break;

					case 'number':
						$items[$key] = stripslashes(wp_strip_all_tags($item['val']));
						break;						

					case 'roles':
					case 'multicheckbox':
						unset($items[$key]);
						foreach ($item['val'] as $subitem_key => $subitem) {
							$items[$key][$subitem_key] = (bool)$subitem;
						}
						break;
					
					default:
						$items[$key] = stripslashes($item['val']);
						break;
				}
			}
			
		}

		return $items;
	}

}
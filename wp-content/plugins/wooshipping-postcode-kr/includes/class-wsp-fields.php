<?php 
/**
 * 프론트 > 필드 수정
 *
 * @class			WSP_Fields
 * @version		1.0.0
 * @package		WooShipping
 * @category		PostCode
 * @author 		gaegoms (gaegoms@gmail.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WSP_Fields {
	
	public static function init() {
		add_filter( 'woocommerce_get_country_locale' , array( __CLASS__ , 'set_locale_field' ) );
		add_filter( 'woocommerce_billing_fields', array( __CLASS__, 'set_fields' ), 10, 2 );
		add_filter( 'woocommerce_shipping_fields', array( __CLASS__, 'set_fields' ), 10, 2 );
		
		add_action( 'wp_footer', array( __CLASS__, 'display_popup_layer' ) );
	}
	
	public static function display_popup_layer() {
		?>
		<div id="wooshipping-postcode-popup">			
		</div>
		<?php 
	}
	
	public static function set_locale_field( $locale ) {
		$kr = array(
				'first_name'		=> array(
						'placeholder'	=> __( 'User name.', WSP_LANG ),
				),
				'last_name'		=> array(
						'required'			=> 0,
				),
				'email'				=> array(
						'placeholder'	=> __( 'E-mail address.', WSP_LANG  ),
				),
				'phone'			=> array(
						'placeholder'	=>  __( 'Phone Number.', WSP_LANG  ),
				),
				'address_1'		=> array(
						'label'				=> __( 'Address', WSP_LANG ),
						'placeholder'	=> __( 'Town / City', 'woocommerce' ),
				),
				'address_2'		=> array(
						'label'				=> __( 'Detail Address', WSP_LANG ),
						'required'			=> true,
				),
				'city'				=> array(
						//'label'				=> __( 'Town / City', WSP_LANG ),
						//'placeholder'	=> __( 'Town / City', WSP_LANG ),
						'hidden'			=> true,
						'required'			=> false,
				),
				'state'			=> array(
						'required'			=> false,
				),
		);
		
		$locale['KR'] = isset( $locale['KR'] ) ? array_merge( $locale['KR'], $kr ) : $kr;

		return $locale;
	}
	
	public static function set_fields( $fields, $country = 'KR' ) {
		$prefix = preg_replace( '/_.*/i', '_', key( $fields ) );
		
		// 배송주소 > 전화번호 추가
		if ( $prefix == 'shipping_' && 'yes' == get_option( 'wooshipping_postcode_use_shipping_phone', 'no' ) ) {
			
			$new_fields = array();
			foreach ( $fields as $key => $value ) {
				$new_fields[ $key ] = $value;
				if ( ( $prefix . 'company' === $key || ( ! array_key_exists( $prefix . 'company', $fields ) && $key === end( array_keys( $fields ) ) ) ) ) {
					$new_fields[ $prefix . 'phone' ] = array(
							'label'			=> __( 'Phone', 'woocommerce'  ),
							'required'		=> true,
		            		'type'			=> 'tel',
							'class'			=> array( 'form-row-last' ),
							'clear'			=> true,
							'validate'		=> array( 'phone' ),
					);
				}
			}
			
			$fields = $new_fields;
		}
		
		
		$field_set = self::set_locale_field( array() );
		foreach ( $field_set[ $country ] as $key => $val ) {
			if ( $prefix == 'shipping_' && $key == 'email' ) { continue; }
			$fields[ $prefix . $key ] = isset( $fields[ $prefix . $key ] ) ? array_merge( $fields[ $prefix . $key ], $val ) : $val;
		}
		//echo '<pre>';print_r( $fields);echo '</pre>';
		
		return $fields;
	}
	
}

WSP_Fields::init();
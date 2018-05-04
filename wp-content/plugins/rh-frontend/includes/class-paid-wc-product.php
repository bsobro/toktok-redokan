<?php

if (class_exists('WC_Product')) {
	
	class WC_Product_Post_Package extends WC_Product {

	public function __construct( $product = 0 ) {
		parent::__construct( $product );
	}

		public function get_type() {
			return 'rh-submit-package';
		}
	}
}
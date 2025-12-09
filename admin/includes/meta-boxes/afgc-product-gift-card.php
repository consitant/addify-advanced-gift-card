<?php

if ( class_exists( 'WC_Product' ) ) {

	class WC_Product_Gift_Card extends WC_Product {
		public $product_type;
		public function __construct( $product ) {

			$this->product_type = 'gift_card'; // name of your custom product type.

			parent::__construct( $product );
			// add additional functions here
		}
	}

}

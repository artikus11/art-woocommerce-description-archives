<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class AWOOD_Front_End
 *
 * @author Artem Abramovich
 * @since  1.0.0
 */
class AWOOD_Front_End {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'woocommerce_after_shop_loop', array( $this, 'output_addon_description' ) );

	}


	public function output_addon_description() {

		$addon_desc = get_term_meta( get_queried_object()->term_id, 'awood_addon_desc', true );

		if ( empty( $addon_desc ) ) {
			return;
		}

		if ( is_tax( array( 'product_cat', 'product_tag' ) ) && 0 === absint( get_query_var( 'paged' ) ) ) {
			echo '<div class="awood-box normal rounded full">';
			echo wp_kses_post( apply_filters( 'the_content', $addon_desc ) );
			echo '</div>';
		}

	}
}

new AWOOD_Front_End();

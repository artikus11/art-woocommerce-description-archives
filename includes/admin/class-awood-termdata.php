<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class AWOOC_Tax_Metabox
 *
 * @author Artem Abramovich
 * @since  1.0.0
 */
class AWOOD_Tax_Metabox {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_metabox_term' ), 10, 2 );
		add_action( 'product_cat_add_form_fields', array( $this, 'add_metabox_term' ) );
		add_action( 'product_tag_edit_form_fields', array( $this, 'edit_metabox_term' ), 10, 2 );
		add_action( 'product_tag_add_form_fields', array( $this, 'add_metabox_term' ) );
		add_action( 'created_term', array( $this, 'save_metabox_term' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_metabox_term' ), 10, 3 );
	}


	public function edit_metabox_term( $term ) {

		$settings = array(
			'textarea_name' => 'addon-description',
			'textarea_rows' => 4,

		);

		$addon_desc = get_term_meta( $term->term_id, 'awood_addon_desc', true );

		?>
		<tr class="form-field term-description-wrap">
			<th scope="row" valign="top">Дополнительное описание</th>
			<td>
				<?php wp_editor( $addon_desc, 'addondescription', $settings ); ?>
				<p class="description">Это дополнительное описание. Данные из него выводятся после товаров на страниццах архивов</p>
			</td>
		</tr>
		<?php

	}


	public function add_metabox_term() {

		?>
		<div class="form-field term-description-wrap">
			<label for="addon-description">Дополнительное описание</label>
			<textarea name="addon-description" id="addon-description" rows="5" cols="40"></textarea>
			<p class="description">Это дополнительное описание. Данные из него выводятся после товаров на страниццах архивов</p>
		</div>
		<?php

	}


	public function save_metabox_term( $term_id, $tt_id = '', $taxonomy = '' ) {

		if ( isset( $_POST['addon-description'] ) ) {
			update_term_meta(
				$term_id,
				'awood_addon_desc',
				wp_kses_post( $_POST['addon-description'] )
			);
		}

	}

}

new AWOOD_Tax_Metabox();

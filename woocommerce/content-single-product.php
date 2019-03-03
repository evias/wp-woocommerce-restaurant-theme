<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<script>
jQuery(document).ready(function($) {
	var form = $('form.cart');
	var extra_1 = $('div.ppom-field-wrapper[data-data_name="extra_zutaten_1"]');
	var extra_1_sel = $('div.ppom-field-wrapper[data-data_name="extra_zutaten_1"] select');
	var extra_2 = $('div.ppom-field-wrapper[data-data_name="extra_zutaten_2"]');
	var extra_2_sel = $('div.ppom-field-wrapper[data-data_name="extra_zutaten_2"] select');
	var extra_3 = $('div.ppom-field-wrapper[data-data_name="extra_zutaten_3"]');
	var extra_3_sel = $('div.ppom-field-wrapper[data-data_name="extra_zutaten_3"] select');

	extra_2_sel.attr("disabled", "disabled");
	extra_3_sel.attr("disabled", "disabled");

	var extra_1_listener = function(e) {
		var idx = $(this)[0].selectedIndex;

		if (idx !== 0) {
			extra_2.show();
			extra_2_sel.removeAttr("disabled");
			extra_2.on('change', extra_2_listener);
		}

		return true;
	}

	var extra_2_listener = function(e) {
		var idx = $(this)[0].selectedIndex;

		if (idx !== 0) {
			extra_3.show();
			extra_3_sel.removeAttr("disabled");
		}

		return true;
	}

	extra_1.on('change', extra_1_listener);
	form.on('submit', function(e) {

		if (extra1_sel[0].selectedIndex === 0) {
			extra_1_sel.attr("disabled", "disabled");
		}

		if (extra2_sel[0].selectedIndex === 0) {
			extra_2_sel.attr("disabled", "disabled");
		}

		if (extra3_sel[0].selectedIndex === 0) {
			extra_3_sel.attr("disabled", "disabled");
		}

		return true;
	});
});
</script>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>

	<?php
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="summary entry-summary">
		<?php
			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 */
			do_action( 'woocommerce_single_product_summary' );
		?>
	</div>

	<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>

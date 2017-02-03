<?php
/**
* Main Class
*
* @since 1.0
*/
class Obj_Main {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
        add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'cgd_add_product_heart' ), 15 );
        add_action( 'wp_ajax_favorite_callback', array( $this, 'favorite_callback' ) );
        add_action( 'wp_ajax_refresh_products_callback', array( $this, 'refresh_products_callback' ) );
        add_shortcode( 'woocommerce_favorites', array( $this, 'favorites_shortcode' ) );
        add_filter( 'body_class', array( $this, 'woo_body_classes' ) );
        add_action( 'wp', array( $this, 'authenticate_user' ) );
    }

    /**
	 * Check to see if page has a shortcode
	 * @param  string  $shortcode
	 * @return boolean
	 *
	 * @since 1.0
	 */
	private function has_shortcode( $shortcode = '' ) {
	    global $post;
	    $post_obj = get_post( $post->ID );
	    $found = false;
	    if ( ! $shortcode )
	        return $found;
	    if ( stripos( $post_obj->post_content, '[' . $shortcode ) !== false )
	        $found = true;
	    return $found;
	}

    /**
    * Enqueue scripts
    *
    * @since 1.0
    */
    public function scripts() {
        wp_enqueue_script( 'main', PLUGIN_URL . 'assets/js/main.js', array('jquery'), VERSION, true );
        wp_localize_script( 'main', 'favorite_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

        wp_enqueue_style( 'main-css', PLUGIN_URL . 'assets/css/main.css', false, VERSION );
    }

    /**
    * Add product heart
    *
    * @since 1.0
    */
    public function cgd_add_product_heart() {
        global $product;

        $user_id = get_current_user_id();
        $saved_products = get_user_meta( $user_id, 'saved_products', true );
        $active_class = '';

        if ( isset( $saved_products[$product->id] ) ) {
            $active_class = 'is-favorited';
        }

        ?>
        <div class="favorite-product <?php echo $active_class; ?>">
            <a rel="nofollow" href="#" data-product-id="<?php echo $product->id; ?>">
                <svg width="107px" height="97px" viewBox="0 0 107 97" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <!-- Generator: Sketch 41.2 (35397) - http://www.bohemiancoding.com/sketch -->
                    <title>heart</title>
                    <desc>Created with Sketch.</desc>
                    <defs></defs>
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="heart" transform="translate(3.000000, 3.000000)" stroke-width="6">
                            <path d="M48.5,9.3 C48.5,9.3 71.3,-11.5 92,9.3 C92,9.3 108.3,24.8 95.3,45.5 C95.3,45.5 93,54.2 48.5,91.3" id="XMLID_3_"></path>
                            <path d="M52.5,9.3 C52.5,9.3 29.7,-11.5 9,9.3 C9,9.3 -7.3,24.8 5.7,45.5 C5.7,45.5 8,54.2 52.5,91.3" id="XMLID_4_"></path>
                        </g>
                    </g>
                </svg>
            </a>
        </div>
        <?php
    }

    /**
    * Favorite callback
    *
    * @since 1.0
    */
    public function favorite_callback() {
        $new_product_id = $_POST['productId'];
        $user_id = get_current_user_id();

        $saved_products = get_user_meta( $user_id, 'saved_products', true );

        if ( ! is_array( $saved_products ) ) {
            $saved_products = array();
        }

        if ( isset( $saved_products[$new_product_id] ) ) {
            unset( $saved_products[$new_product_id] );
            update_user_meta( $user_id, 'saved_products', $saved_products );

            echo 0;
            wp_die();
        } else {
            $saved_products[$new_product_id] = $new_product_id;
            update_user_meta( $user_id, 'saved_products', $saved_products );

            echo 1;
            wp_die();
        }

    }

    /**
     * Refresh products callback
     *
     * @since 1.0
     */
    public function refresh_products_callback() {
        $this->display_product_grid();
        wp_die();
    }

    /**
     * Add Body classes
     *
     * @since 1.0
     */
    function woo_body_classes( $c ) {

        global $post;

        if( isset($post->post_content) && has_shortcode( $post->post_content, 'woocommerce_favorites' ) ) {
            $c[] = 'woocommerce woocommerce-page woocommerce-favorites';
        }
        return $c;
    }

    /**
     * Display products grid
     */
    private function display_product_grid() {
        $user_id = get_current_user_id();
        $saved_products = get_user_meta( $user_id, 'saved_products', true );
        $args = array(
            'post_type' => 'product',
            'post__in'  => $saved_products
        );
        $saved_query = new WP_Query( $args );
        ?>
        <?php if ( ! is_array( $saved_products ) ): ?>
            No posts
        <?php else: ?>
            <?php
            /**
            * woocommerce_before_main_content hook.
            *
            * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
            * @hooked woocommerce_breadcrumb - 20
            */
            do_action( 'woocommerce_before_main_content' );
            ?>

            <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

                <h1 class="page-title screen-reader-text"><?php woocommerce_page_title(); ?></h1>

            <?php endif; ?>

            <div class="haven-shop-container">

                <?php if ( $saved_query->have_posts() ) : ?>

                    <?php
                    /**
                    * woocommerce_before_shop_loop hook.
                    *
                    * @hooked woocommerce_result_count - 20
                    * @hooked woocommerce_catalog_ordering - 30
                    */
                    do_action( 'woocommerce_before_shop_loop' );
                    ?>

                    <?php woocommerce_product_loop_start(); ?>

                    <?php woocommerce_product_subcategories(); ?>

                    <?php while ( $saved_query->have_posts() ) : $saved_query->the_post(); ?>

                        <?php wc_get_template_part( 'content', 'product' ); ?>

                    <?php endwhile; // end of the loop. ?>
                    <?php wp_reset_postdata(); ?>

                    <?php woocommerce_product_loop_end(); ?>

                    <?php
                    /**
                    * woocommerce_after_shop_loop hook.
                    *
                    * @hooked woocommerce_pagination - 10
                    */
                    do_action( 'woocommerce_after_shop_loop' );
                    ?>

                <?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

                    <?php wc_get_template( 'loop/no-products-found.php' ); ?>

                <?php endif; ?>


            </div>

            <?php
            /**
            * woocommerce_after_main_content hook.
            *
            * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
            */
            do_action( 'woocommerce_after_main_content' );
            ?>
        <?php endif; ?>
        <?php
    }

    /**
     * Authenticate user
     *
     * @since 1.0
     */
    public function authenticate_user() {
        if ( $this->has_shortcode( 'woocommerce_favorites' ) && ! is_user_logged_in() ) {
            auth_redirect();
        }
    }

    /**
    * Shortcode to display favorites grid
    *
    * @since 1.0
    */
    public function favorites_shortcode() {

        $this->display_product_grid();

    }

}

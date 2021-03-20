<?php
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
function crb_attach_theme_options() {
    $products = [];
    $args = array( 
		'post_type' 		=> 'product',
		'posts_per_page' => -1,
	);
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
            $products[get_the_id()] = get_the_title();
        endwhile;
        wp_reset_postdata();
    endif;
    /*Container::make( 'theme_options', __( 'Theme Options', 'crb' ) )
        ->add_fields( array(
            Field::make( 'text', 'crb_text', 'Text Field' ),
        ));
    Container::make( 'post_meta', 'Custom Data' )
        ->where( 'post_type', '=', 'page' )
        ->add_fields( array(
            Field::make( 'map', 'crb_location' )
                ->set_position( 37.423156, -122.084917, 14 ),
            Field::make( 'sidebar', 'crb_custom_sidebar' ),
            Field::make( 'image', 'crb_photo' ),
        ));*/
    Block::make( __( 'Mos Deal of the Day' ) )
    ->add_fields( array(        
        Field::make( 'text', 'mos-dtd-heading', __( 'Heading' ) ),
        Field::make( 'date_time', 'mos-dtd-ete', __( 'Estimated time of ending' ) ),
        Field::make( 'select', 'mos-dtd-product', __( 'Select a product' ) )
            ->set_options( $products ),
        Field::make( 'text', 'mos-dtd-sold', __( 'Already Sold:' ) ),
        Field::make( 'text', 'mos-dtd-available', __( 'Available:' ) ),
    ))
    ->set_icon( 'cart' )
    ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        ?>
        <?php if ($fields['mos-dtd-product']) : ?>
            <?php $product = wc_get_product($fields['mos-dtd-product']); ?>
            <?php
            $sold = ($fields['mos-dtd-sold'])?$fields['mos-dtd-sold']:0;
            $available = ($fields['mos-dtd-available'])?$fields['mos-dtd-available']:0;
            $percent = number_format(($sold * 100 / $available), 2);
            if( class_exists( 'WeDevs_Dokan' ) ) {
                $vendor_id = get_the_author_meta( 'ID' );
                $store_info = dokan_get_store_info( $vendor_id );
                $store_url = dokan_get_store_url( $vendor_id );
                $sold_by_label = apply_filters( 'dokan_sold_by_label', esc_html__( 'Sold by', 'rehub-theme' ) );
                $is_vendor = dokan_is_user_seller( $vendor_id );
                $store_name = esc_html( $store_info['store_name'] );
                $featured_vendor = get_user_meta( $vendor_id, 'dokan_feature_seller', true );
            }elseif (class_exists('WCMp')){
                $vendor_id = get_the_author_meta( 'ID' );
                $is_vendor = is_user_wcmp_vendor( $vendor_id );
                if($is_vendor){
                    $vendorobj = get_wcmp_vendor($vendor_id);
                    $store_url = $vendorobj->permalink;
                    $store_name = $vendorobj->page_title;	
                    $verified_vendor = get_user_meta($vendor_id, 'wcmp_vendor_is_verified', true);			
                }
                $wcmp_option = get_option("wcmp_frontend_settings_name");
                $sold_by_label = (!empty($wcmp_option['sold_by_text'])) ? $wcmp_option['sold_by_text'] : esc_html__( 'Sold by', 'rehub-theme' );
            }
            elseif (defined( 'wcv_plugin_dir' )) {
                $vendor_id = get_the_author_meta( 'ID' );
                $store_url = WCV_Vendors::get_vendor_shop_page( $vendor_id );
                $sold_by_label = get_option( 'wcvendors_label_sold_by' );
                $is_vendor = WCV_Vendors::is_vendor( $vendor_id );
                $store_name = WCV_Vendors::get_vendor_sold_by( $vendor_id );

                if ( class_exists( 'WCVendors_Pro' ) ) {
                    $vendor_meta = array_map( function( $a ){ return $a[0]; }, get_user_meta($vendor_id ) );
                    $verified_vendor = ( array_key_exists( '_wcv_verified_vendor', $vendor_meta ) ) ? $vendor_meta[ '_wcv_verified_vendor' ] : false;
                    $vacation_mode = get_user_meta( $vendor_id , '_wcv_vacation_mode', true ); 
                    $vacation_msg = ( $vacation_mode ) ? get_user_meta( $vendor_id , '_wcv_vacation_mode_msg', true ) : '';		
                }		
            }
            else{
                return false;
            }                    
            ?>
            <div class="mos-dtd-wrapper <?php echo $attributes['className'] ?>">
                <div class="mos-dtd-block">
                    <?php if ($fields['mos-dtd-heading']) : ?>
                    <div class="title"><?php echo esc_html( $fields['mos-dtd-heading'] ); ?></div>
                    <hr>
                    <?php endif?>
                    <div class="wrapper d-flex">
                        <div class="img-part w-sm-50p">
                            <?php if (has_post_thumbnail($fields['mos-dtd-product'])) :?>
                                <a class="img-centered-flex rh-flex-center-align rh-flex-justify-center" href="<?php echo get_the_permalink($fields['mos-dtd-product']) ?>"><img loading="lazy" src="<?php echo aq_resize(get_the_post_thumbnail_url($fields['mos-dtd-product'], 'full'),600,450,true)?>" data-src="<?php echo get_the_post_thumbnail_url($fields['mos-dtd-product'], 'full')?>" alt="<?php echo get_the_title($fields['mos-dtd-product']) ?>" class="lazyloaded" width="600" height="450"></a>
                            <?php endif;?>
                        </div>
                        <div class="text-part w-sm-50p">
                            <h3><a class="" href="<?php echo get_the_permalink($fields['mos-dtd-product']) ?>"><?php echo get_the_title($fields['mos-dtd-product']) ?></a></h3>
                            <div class="soldby">
                                <small class="wcvendors_sold_by_in_loop"><span><?php echo $sold_by_label; ?></span> <a href="<?php echo $store_url ?>"><?php echo $store_name ?></a></small>
                            </div>
                            <div class="woo_spec_price">
                                <?php echo $product->get_price_html(); ?>
                            </div>
                            <div class="woo_spec_bar mt30 mb20">
                                <div class="deal-stock mb10">
                                    <span class="stock-sold floatleft">
                                        Already Sold: <strong><?php echo $sold; ?></strong>
                                    </span>
                                    <span class="stock-available floatright">
                                        Available: <strong><?php echo $available; ?></strong>
                                    </span>
                                </div>
                                <div class="wpsm-bar wpsm-clearfix" data-percent="<?php echo $percent ?>%">
                                    <div class="wpsm-bar-bar" style="background: rgb(106, 220, 250); width: <?php echo $percent ?>%;"></div>
                                    <div class="wpsm-bar-percent">75 %</div>
                                </div>
                            </div>
                            <div class="marketing-text mt15 mb15">Hurry Up! Offer ends soon.</div>
                            <?php if ($fields['mos-dtd-ete']) :  ?>
                                <?php $raw = new DateTime($fields['mos-dtd-ete']); ?>
                                <div class="woo_spec_timer">
                                    <!--2021-03-18 17:00:00-->
                                    <div id="countdown_dashboard7" class="countdown_dashboard" data-day="<?php echo $raw->format('d'); ?>" data-month="<?php echo $raw->format('m'); ?>" data-year="<?php echo $raw->format('Y'); ?>" data-hour="<?php echo $raw->format('H'); ?>" data-min="<?php echo $raw->format('i'); ?>">
                                        <div class="dash days_dash"> <span class="dash_title">days</span>
                                            <div class="digit">0</div>
                                            <div class="digit">0</div>
                                        </div>
                                        <div class="dash hours_dash"> <span class="dash_title">hours</span>
                                            <div class="digit">0</div>
                                            <div class="digit">0</div>
                                        </div>
                                        <div class="dash minutes_dash"> <span class="dash_title">minutes</span>
                                            <div class="digit">0</div>
                                            <div class="digit">0</div>
                                        </div>
                                        <div class="dash seconds_dash"> <span class="dash_title">seconds</span>
                                            <div class="digit">0</div>
                                            <div class="digit">0</div>
                                        </div>
                                    </div>
                                    <!-- Countdown dashboard end -->
                                    <div class="clearfix"></div>
                                </div>  
                            <?php endif?>                             
                            <div class="mt20 mb15">
                            <a href="?add-to-cart=<?php echo get_the_ID() ?>" data-product_id="<?php echo get_the_ID() ?>" data-product_sku="testmultivendor" class="re_track_btn rehub_main_btn rehub-main-smooth wpsm-button ajax_add_to_cart product_type_simple" >Add to cart</a>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        <?php endif?>
        <?php
    });
    Block::make( __( 'Mos Product List' ) )
    ->add_fields( array(
        Field::make( 'text', 'mos-product-list-heading', __( 'Heading' ) ),        
        Field::make( 'multiselect', 'mos-product-list-products', __( 'Select a products' ) )
            ->set_options( $products ),
    ))
    ->set_icon( 'cart' )
    ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        ?>
        <div class="mos-product-list-wrapper <?php echo $attributes['className'] ?>">
            <div class="mos-product-list">
                <?php if ($fields['mos-product-list-heading']) : ?>
                    <div class="mos-product-list-heading">
                        <h3><?php echo esc_html( $fields['mos-product-list-heading'] ); ?></h3>
                    </div>
                <?php endif?>
                    <?php //var_dump($fields['mos-product-list-products'])?>
                <?php if (sizeof($fields['mos-product-list-products'])) : ?>
                    <div class="wpsm_recent_posts_list mb0">
                        <?php foreach($fields['mos-product-list-products'] as $post_id): ?>
                            <?php $product = wc_get_product($post_id); ?>
                            <div class="col_item item-small-news flowhidden item-small-news-image border-lightgrey pl10 pr10 mt--1 pt10 pb10">
                                <?php if (has_post_thumbnail($post_id)) : ?>
                                <figure class="img-centered-flex rh-flex-eq-height rh-flex-justify-center floatleft width-80 height-80 img-width-auto position-relative"><a href="<?php echo get_the_permalink($post_id)?>">
                                        <img loading="lazy" src="<?php echo aq_resize(get_the_post_thumbnail_url($post_id,'full'), 100,85, true) ?>" data-src="<?php echo aq_resize(get_the_post_thumbnail_url($post_id,'full'), 100,85, false) ?>" alt="<?php echo get_the_title($post_id) ?>" class=" lazyloaded" width="100" height="85"> </a>
                                </figure>
                                <?php endif;?>
                                <div class="item-small-news-details position-relative floatright width-80-calc pl15 rtlpr15">
                                    <div class="post-meta mb10 upper-text-trans changeonhover">
                                        <?php 
                                        $categories = get_the_terms( get_the_ID(), 'product_cat' );
                                        foreach($categories as $category) {
                                            echo '<a href="'.get_term_link($category->term_id).'" class="woocat greycolor">'.$category->name.'</a> ';
                                        }
                                        ?>
<!--                                        <a href="https://wahimall.com/product-category/electronics/" class="woocat greycolor">Electronics</a>-->
                                    </div>
                                    <h3 class="mb5 mt0"><a href="<?php echo get_the_permalink($post_id)?>" class="mr10"><?php echo get_the_title($post_id)?></a>
                                    </h3>
                                    <span class="simple_price_count greencolor fontnormal"><?php echo $product->get_price_html(); ?></span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
            </div>
        </div>
        <?php
    });
    
}
add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
    require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}
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
            <div class="mos-dtd-wrapper <?php echo $attributes['className'] ?>">
                <div class="mos-dtd-block">
                    <?php if ($fields['mos-dtd-heading']) : ?>
                    <div class="title"><?php echo esc_html( $fields['mos-dtd-heading'] ); ?></div>
                    <hr>
                    <?php endif?>
                    <div class="wrapper d-flex">
                        <div class="img-part w-sm-50p">
                            <?php if (has_post_thumbnail($fields['mos-dtd-product'])) :?>
                                <a class="img-centered-flex rh-flex-center-align rh-flex-justify-center" href="<?php echo get_the_permalink($fields['mos-dtd-product']) ?>">
                                    <img loading="lazy" src="<?php echo aq_resize(get_the_post_thumbnail_url($fields['mos-dtd-product'], 'full'),600,450,true)?>" data-src="<?php echo get_the_post_thumbnail_url($fields['mos-dtd-product'], 'full')?>" alt="<?php echo get_the_title($fields['mos-dtd-product']) ?>" class="lazyloaded" width="600" height="450">                            </a>
                            <?php endif;?>
                        </div>
                        <div class="text-part w-sm-50p">
                            <h3><a class="" href="<?php echo get_the_permalink($fields['mos-dtd-product']) ?>"><?php echo get_the_title($fields['mos-dtd-product']) ?></a></h3>
                            <div class="woo_spec_price">
                                <?php echo $product->get_price_html(); ?>
                            </div>
                            <div class="woo_spec_bar mt30 mb20">
                                <div class="deal-stock mb10">
                                    <span class="stock-sold floatleft">
                                        Already Sold: <strong>12</strong>
                                    </span>
                                    <span class="stock-available floatright">
                                        Available: <strong>16</strong>
                                    </span>
                                </div>
                                <div class="wpsm-bar wpsm-clearfix" data-percent="75%">
                                    <div class="wpsm-bar-bar" style="background: rgb(106, 220, 250); width: 75%;"></div>
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
    
}
add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
    require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}
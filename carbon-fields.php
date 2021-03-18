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
            <div class="mos-dtd-wrapper <?php echo $attributes['className'] ?>">
                <div class="mos-dtd-block">
                    <?php if ($fields['mos-dtd-heading']) : ?>
                    <div class="title"><?php echo esc_html( $fields['mos-dtd-heading'] ); ?></div>
                    <hr>
                    <?php endif?>
                    <div class="wrapper">
                        <div class="img-part">
                            <?php if (has_post_thumbnail($fields['mos-dtd-product'])) :?>
                                <a class="img-centered-flex rh-flex-center-align rh-flex-justify-center" href="<?php echo get_the_permalink($fields['mos-dtd-product']) ?>">
                                    <img loading="lazy" src="<?php echo get_the_post_thumbnail_url($fields['mos-dtd-product'], 'full')?>" data-src="" alt="<?php echo get_the_title($fields['mos-dtd-product']) ?>" class="lazyloaded" width="600" height="450">                            </a>
                            <?php endif;?>
                        </div>
                        <div class="text-part">
                            <h3><a class="" href="<?php echo get_the_permalink($fields['mos-dtd-product']) ?>"><?php get_the_title($fields['mos-dtd-product']) ?></a></h3>
                        </div>
                    </div>
                    <?php if ($fields['mos-dtd-ete']) : ?>
                    <div class="ete"><?php echo esc_html( $fields['mos-dtd-ete'] ); ?></div>
                    <hr>
                    <?php endif?>
                    <?php if ($fields['mos-dtd-product']) : ?>
                    <div class="product"><?php echo esc_html( $fields['mos-dtd-product'] ); ?></div>
                    <hr>
                    <?php endif?>
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
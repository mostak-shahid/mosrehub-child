<?php
function admin_shortcodes_page(){
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null )
    add_menu_page( 
        __( 'Theme Short Codes', 'textdomain' ),
        'Short Codes',
        'manage_options',
        'shortcodes',
        'shortcodes_page',
        'dashicons-book-alt',
        3
    ); 
}
add_action( 'admin_menu', 'admin_shortcodes_page' );
function shortcodes_page(){
	?>
	<div class="wrap">
		<h1>Theme Short Codes</h1>
		<ol>
			<li>[home-url slug=''] <span class="sdetagils">displays home url</span></li>
			<li>[site-identity class='' container_class=''] <span class="sdetagils">displays site identity according to theme option</span></li>
			<li>[site-name link='0'] <span class="sdetagils">displays site name with/without site url</span></li>
			<li>[copyright-symbol] <span class="sdetagils">displays copyright symbol</span></li>
			<li>[this-year] <span class="sdetagils">displays 4 digit current year</span></li>		
			<li>[feature-image wrapper_element='div' wrapper_atts='' height='' width=''] <span class="sdetagils">displays feature image</span></li>		
			<li>[font-awesome class="" container-class=""] <span class="sdetagils">displays feature image</span></li>		
			
		</ol>
	</div>
	<?php
}
function home_url_func( $atts = array(), $content = '' ) {
	$atts = shortcode_atts( array(
		'slug' => '',
	), $atts, 'home-url' );

	return home_url( $atts['slug'] );
}
add_shortcode( 'home-url', 'home_url_func' );

function site_identity_func( $atts = array(), $content = null ) {
	global $forclient_options;
	$logo_url = ($forclient_options['logo']['url']) ? $forclient_options['logo']['url'] : get_template_directory_uri(). '/images/logo.png';
	$logo_option = $forclient_options['logo-option'];
	$html = '';
	$atts = shortcode_atts( array(
		'class' => '',
		'container_class' => ''
	), $atts, 'site-identity' ); 
	
	
	$html .= '<div class="logo-wrapper '.$atts['container_class'].'">';
		if($logo_option == 'logo') :
			$html .= '<a class="logo '.$atts['class'].'" href="'.home_url().'">';
			list($width, $height) = getimagesize($logo_url);
			$html .= '<img class="img-responsive img-fluid" src="'.$logo_url.'" alt="'.get_bloginfo('name').' - Logo" width="'.$width.'" height="'.$height.'">';
			$html .= '</a>';
		else :
			$html .= '<div class="text-center '.$atts['class'].'">';
				$html .= '<h1 class="site-title"><a href="'.home_url().'">'.get_bloginfo('name').'</a></h1>';
				$html .= '<p class="site-description">'.get_bloginfo( 'description' ).'</p>';
			$html .= '</div>'; 
		endif;
	$html .= '</div>'; 
		
	return $html;
}
add_shortcode( 'site-identity', 'site_identity_func' );

function site_name_func( $atts = array(), $content = '' ) {
	$html = '';
	$atts = shortcode_atts( array(
		'link' => 0,
	), $atts, 'site-name' );
	if ($atts['link']) $html .=	'<a href="'.esc_url( home_url( '/' ) ).'">';
	$html .= get_bloginfo('name');
	if ($atts['link']) $html .=	'</a>';
	return $html;
}
add_shortcode( 'site-name', 'site_name_func' );

function copyright_symbol_func() {
	return '&copy;';
}
add_shortcode( 'copyright-symbol', 'copyright_symbol_func' );

function this_year_func() {
	return date('Y');
}
add_shortcode( 'this-year', 'this_year_func' );

function feature_image_func( $atts = array(), $content = '' ) {
	global $mosacademy_options;
	$html = '';
	$img = '';
	$atts = shortcode_atts( array(
		'wrapper_element' => 'div',
		'wrapper_atts' => '',
		'height' => '',
		'width' => '',
	), $atts, 'feature-image' );

	if (has_post_thumbnail()) $img = get_the_post_thumbnail_url();	
	elseif(@$mosacademy_options['blog-archive-default']['id']) $img = wp_get_attachment_url( $mosacademy_options['blog-archive-default']['id'] ); 
	if ($img){
		if ($atts['wrapper_element']) $html .= '<'. $atts['wrapper_element'];
		if ($atts['wrapper_atts']) $html .= ' ' . $atts['wrapper_atts'];
		if ($atts['wrapper_element']) $html .= '>';
		list($width, $height) = getimagesize($img);
		if ($atts['width'] AND $atts['height']) :
			if ($width > $atts['width'] AND $height > $atts['height']) $img_url = aq_resize($img, $atts['width'], $atts['height'], true);
			else $img_url = $img;
		elseif ($atts['width']) :
			if ($width > $atts['width']) $img_url = aq_resize($img, $atts['width']);
			else $img_url = $img;
		else : 
			$img_url = $img;
		endif;
		list($fwidth, $fheight) = getimagesize($img_url);
		$html .= '<img class="img-responsive img-fluid img-featured" src="'.$img_url.'" alt="'.get_the_title().'" width="'.$fwidth.'" height="'.$fheight.'" />';
		if ($atts['wrapper_element']) $html .= '</'. $atts['wrapper_element'] . '>';
	}
	return $html;
}
add_shortcode( 'feature-image', 'feature_image_func' );

function font_awesome_func( $atts = array(), $content = '' ) {
    $html= "";
	$atts = shortcode_atts( array(
		'class' => '',
		'container-class' => '',
	), $atts, 'font-awesome' );
    $html .= '<div class="'.$atts['container-class'].'"><i class="fa fas '.$atts['class'].'"></i></div>';
	return $html;
}
add_shortcode( 'font-awesome', 'font_awesome_func' );

function porduct_carousel_func( $atts = array(), $content = '' ) {
	$html = '';
    ob_start();
	$atts = shortcode_atts( array(
        'title'             => '',
		'limit'				=> '-1',
		'offset'			=> 0,
		'category'			=> '',
		'tag'				=> '',
		'orderby'			=> '',
		'order'				=> '',
		'container'			=> 0,
		'container_class'	=> '',
		'class'				=> '',
        'show'              => 4,
	), $atts, 'porduct_carousel' );

	$cat = ($atts['category']) ? preg_replace('/\s+/', '', $atts['category']) : '';
	$tag = ($atts['tag']) ? preg_replace('/\s+/', '', $atts['tag']) : '';

	$args = array( 
		'post_type' 		=> 'product',
		'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
	);
	$args['posts_per_page'] = $atts['limit'];
	if ($atts['offset']) $args['offset'] = $atts['offset'];

	if ($atts['category'] OR $atts['tag']) {
		$args['tax_query'] = array();
		if ($atts['category'] AND $atts['tag']) {
			$args['tax_query']['relation'] = 'OR';
		}
		if ($atts['category']) {
			$args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => explode(',', $cat),
				);
		}
		if ($atts['tag']) {
			$args['tax_query'][] = array(
					'taxonomy' => 'product_tag',
					'field'    => 'term_id',
					'terms'    => explode(',', $tag),
				);
		}
	}
	if ($atts['orderby']) $args['orderby'] = $atts['orderby'];
	if ($atts['order']) $args['order'] = $atts['order'];
	if (@$atts['author']) $args['author'] = $atts['author'];
    $rand = rand(1000,9999); 
    ?>
    <div class="product-carousel-wrap">
    <?php 
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) :
        ?>
        <div class="paginator-center">
            <h2 class="product-carousel-title"><?php echo $atts['title'] ?></h2>
            <ul>
                <li class="prev"><i class="fa fa-angle-left"></i></li>
                <li class="next"><i class="fa fa-angle-right"></i></li>
            </ul>
        </div>
		<div id="product-carousel-<?php echo $rand?>" class="product-carousel-container <?php echo $atts['container_class'] ?>"  data-slick='{"slidesToShow": <?php echo $atts['show'] ?>}'>
		<?php while ( $query->have_posts() ) : $query->the_post(); 
            $product = wc_get_product( get_the_ID() );
            ?>
		    <div <?php post_class( $classes ); ?>>
                <div class="product col_item woo_grid_compact two_column_mobile type-product ">
                    <div class="button_action rh-shadow-sceu pt5 pb5">
                        <div>
                            <div class="heart_thumb_wrap text-center"><span class="flowhidden cell_wishlist"><span class="heartplus" data-post_id="<?php echo get_the_ID() ?>" data-informer="0"><span class="ml5 rtlmr5 wishaddedwrap" id="wishadded<?php echo get_the_ID() ?>">Added to wishlist</span><span class="ml5 rtlmr5 wishremovedwrap" id="wishremoved<?php echo get_the_ID() ?>">Removed from wishlist</span> </span></span><span id="wishcount<?php echo get_the_ID() ?>" class="thumbscount">0</span> </div>
                        </div>
                        <div>
                            <div class="quick_view_wrap pt10 pl5 pr5 pb10"><span class="flowhidden cell_quick_view"><span class="cursorpointer quick_view_button" data-product_id="<?php echo get_the_ID() ?>"><i class="rhicon rhi-search-plus"></i></span></span></div>
                        </div>

                    </div>

                    <figure class="mb15 mt25 position-relative">
                        <a class="img-centered-flex rh-flex-justify-center rh-flex-center-align" href="<?php echo get_the_permalink() ?>">
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'medium') ?>" data-spai="1" data-src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'medium') ?>" alt="<?php echo get_the_title() ?>" class="ls-is-cached lazyloaded" width="280" height="280">

                        </a>

                        <div class="gridcountdown"></div>
                    </figure>
                    <div class="cat_for_grid lineheight15">
                        <?php 
                        $categories = get_the_terms( get_the_ID(), 'product_cat' );
                        foreach($categories as $category) {
                            echo '<a href="'.get_term_link($category->term_id).'" class="woocat">'.$category->name.'</a> ';
                        }
                        ?>                    
                    </div>

                    <h3 class=" text-clamp text-clamp-2">
                        <a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a>
                    </h3>


                    <small class="wcvendors_sold_by_in_loop"><span>Sold by</span> <a href="https://wahimall.com/store/sano/">Sano Health Food Center</a></small><br>



                    <div class="border-top pt10 pr10 pl10 pb10 rh-flex-center-align abposbot">
                        <div class="price_for_grid redbrightcolor floatleft rehub-btn-font mr10">

                            <span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>13.49</bdi></span></span>
                        </div>
                        <div class="rh-flex-right-align btn_for_grid floatright">

                            <a href="?add-to-cart=<?php echo get_the_ID() ?>" data-product_id="<?php echo get_the_ID() ?>" data-product_sku="" class="re_track_btn woo_loop_btn rh-flex-center-align rh-flex-justify-center rh-shadow-sceu add_to_cart_button ajax_add_to_cart product_type_simple"><svg height="24px" version="1.1" viewBox="0 0 64 64" width="24px" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M56.262,17.837H26.748c-0.961,0-1.508,0.743-1.223,1.661l4.669,13.677c0.23,0.738,1.044,1.336,1.817,1.336h19.35   c0.773,0,1.586-0.598,1.815-1.336l4.069-14C57.476,18.437,57.036,17.837,56.262,17.837z"></path>
                                        <circle cx="29.417" cy="50.267" r="4.415"></circle>
                                        <circle cx="48.099" cy="50.323" r="4.415"></circle>
                                        <path d="M53.4,39.004H27.579L17.242,9.261H9.193c-1.381,0-2.5,1.119-2.5,2.5s1.119,2.5,2.5,2.5h4.493l10.337,29.743H53.4   c1.381,0,2.5-1.119,2.5-2.5S54.781,39.004,53.4,39.004z"></path>
                                    </g>
                                </svg> Add to cart</a>



                        </div>
                    </div>

                </div>
		    </div>
        <?php endwhile; ?>
		</div><!--/.product-carousel-container-->
		<?php wp_reset_postdata();
    endif;
    ?>
    </div>
    <script>
    jQuery(document).ready(function($){
        $('#product-carousel-<?php echo $rand?>').slick({
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 1,
//            prevArrow: $('#product-carousel-<?php echo $rand?>').siblings().find('.prev'),
//            nextArrow: $('#product-carousel-<?php echo $rand?>').siblings().find('.next'),
            autoplay: true,
            autoplaySpeed: 2000,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ],
        });
    });
    </script>
    <?php
    $html = ob_get_clean();
    return $html; 
}
add_shortcode( 'porduct_carousel', 'porduct_carousel_func' );
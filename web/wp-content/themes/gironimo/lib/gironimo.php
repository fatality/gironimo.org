<?php

/**
 * file: gironimo.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

load_theme_textdomain('gironimotheme', TEMPLATEPATH . '/languages');
$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if (is_readable($locale_file)) require_once($locale_file);

// Clean up the WordPress head
function gironimo_head_cleanup()
{
    //remove_action( 'wp_head', 'feed_links_extra', 3 );                    // Category Feeds
	//remove_action( 'wp_head', 'feed_links', 2 );                          // Post and Comment Feeds
	//remove_action( 'wp_head', 'rsd_link' );                               // EditURI link
	//remove_action( 'wp_head', 'wlwmanifest_link' );                       // Windows Live Writer
	//remove_action( 'wp_head', 'index_rel_link' );                         // index link
	//remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );            // previous link
	//remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );             // start link
	//remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
	remove_action( 'wp_head', 'wp_generator' );                             // WP version
}
add_action('init', 'gironimo_head_cleanup');

function gironimo_rss_version()
{
    return '';
}
add_filter('the_generator', 'gironimo_rss_version');

// load modernizr, jquery and css file
function gironimo_queue_high_js_and_css()
{
    wp_register_script(
        'modernizr',
        get_template_directory_uri() . '/lib/js/libs/modernizr.custom.min.js',
        array(),
        false,
        false
    );
    wp_enqueue_script('modernizr');
    wp_enqueue_script('jquery');
    
    if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1))
        wp_enqueue_script('comment-reply');
    
    wp_register_script(
        'gironimo-js',
        get_template_directory_uri() . '/lib/js/scripts.js',
        array('modernizr', 'jquery'),
        false,
        true
    );
    wp_enqueue_script('gironimo-js');
    
    wp_register_style(
        'gironimo-base',
        get_template_directory_uri() . '/lib/css/style.css',
        array(),
        false,
        'all'
    );
    wp_enqueue_style('gironimo-base');
}
add_action('wp_enqueue_scripts', 'gironimo_queue_high_js_and_css', 1);

function gironimo_queue_low_js_and_css()
{
    wp_register_script(
        'respond-js',
        get_template_directory_uri() . '/lib/js/libs/respond.min.js',
        array('modernizr'),
        false,
        true
    );
    wp_enqueue_script('respond-js');
}
add_action('wp_enqueue_scripts', 'gironimo_queue_low_js_and_css', 90);

function gironimo_excerpt_more($more)
{
    global $post;
    return ' <a href="' . get_permalink($post->ID) . '" title="' . get_the_title($post->ID) . ' weiterlesen">Weiterlesen &raquo;</a>';
}
add_filter('excerpt_more', 'gironimo_excerpt_more');

function gironimo_theme_support()
{
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(125, 125, true);
    add_theme_support('automatic-feed-links');
    add_theme_support('posts-formats', array(
        'aside',
        'gallery',
        'link',
        'image',
        'quote',
        'status',
        'video',
        'audio',
        'chat'
    ));
    add_theme_support('menus');
    register_nav_menus(array(
        'main-nav' => 'The Main Menu',
        'footer_links' => 'Footer Links'
    ));
    add_editor_style();
}
add_action('after_setup_theme', 'gironimo_theme_support');
add_action('widgets_init', 'gironimo_register_sidebars');
add_filter('get_search_form', 'gironimo_wpsearch');

class gironimo_walker extends Walker_Nav_Menu
{
    function start_el(&$output, $item, $depth, $args)
    {
        global $wp_query;
        $indent = ($depth) ? str_repeat("\t", $depth): '';
        
        $class_names = $value = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="' . esc_attr($class_names) . '"';
        
        $output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';
        
        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) .'"' : '';
		$attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) .'"' : '';
		$attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) .'"' : '';
		
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		$item_output .= '<br /><span class="sub">' . $item->description . '</span>';
		$item_output .= '</a>';
		$item_output .= $args->after;
		
		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}
}

function gironimo_main_nav()
{
    $walker = new gironimo_walker;
    wp_nav_menu(array(
        'menu' => 'main_nav', /* menu name */
        'theme_location' => 'main_nav', /* where in the theme it's assigned */
        'container_class' => 'menu clearfix', /* container class */
        'fallback_cb' => 'gironimo_main_nav_fallback', /* menu fallback */
        'walker' => $walker /* customizes the output of the menu */
    ));
}

function gironimo_footer_links()
{
    wp_nav_menu(array(
        'menu' => 'footer_links', /* menu name */
        'theme_location' => 'footer_links', /* where in the theme it's assigned */
        'container_class' => 'footer-links clearfix', /* container class */
        'fallback_cb' => 'gironimo_footer_links_fallback' /* menu fallback */
    ));
}

function gironimo_main_nav_fallback()
{
    wp_page_menu('show_home=Home&menu_class=menu');
}

function gironimo_footer_links_fallback()
{
    // should I add?
}

// related posts
function gironimo_related_posts()
{
    global $post;
    echo '<ul class="clearfix">';
    
    $tags = wp_get_post_tags($post->ID);
    if ($tags)
    {
        foreach($tags as $tag) { $tag_arr .= $tag->slug . ','; }
        
        $args = array(
            'tag' => $tag_arr,
            'numberposts' => 5,
            'post__not_in' => array($post->ID)
        );
        
        $related_posts = get_posts($args);
        
        if ($related_posts) {
            foreach ($related_posts as $post) : setup_postdata($post); ?>
            <li class="related-post"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
        <?php endforeach; }
        else { ?>
            <li class="no-related-post">Keine Artikel gefunden!</li>
        <?php }
    }
    wp_reset_query();
    echo '</ul>';
}

// numeric page navi
function page_navi($before = '', $after = '')
{
	global $wpdb, $wp_query;
	
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	$numposts = $wp_query->found_posts;
	$max_page = $wp_query->max_num_pages;
	
	if ($numposts <= $posts_per_page) return;
	
	if (empty($paged) || $paged == 0) $paged = 1;
	
	$pages_to_show = 7;
	$pages_to_show_minus_1 = $pages_to_show-1;
	$half_page_start = floor($pages_to_show_minus_1 / 2);
	$half_page_end = ceil($pages_to_show_minus_1 / 2);
	$start_page = $paged - $half_page_start;
	
	if ($start_page <= 0) $start_page = 1;
	
	$end_page = $paged + $half_page_end;
	
	if (($end_page - $start_page) != $pages_to_show_minus_1)
	    $end_page = $start_page + $pages_to_show_minus_1;
	
	if ($end_page > $max_page) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	
	if ($start_page <= 0) $start_page = 1;
	
	echo $before.'<nav class="page-navigation"><ol class="gironimo_page_navi clearfix">'."";
	
	if ($start_page >= 2 && $pages_to_show < $max_page) {
		$first_page_text = "Erste";
		echo '<li class="gpn-first-page-link"><a href="' . get_pagenum_link() . '" title="' . $first_page_text . '">' . $first_page_text . '</a></li>';
	}
	
	echo '<li class="gpn-prev-link">';
	previous_posts_link('<<');
	echo '</li>';
	
	for ($i = $start_page; $i  <= $end_page; $i++)
	{
		if ($i == $paged)
		    echo '<li class="gpn-current">'.$i.'</li>';
		else
		    echo '<li><a href="' . get_pagenum_link($i) . '">' . $i .'</a></li>';
	}
	
	echo '<li class="gpn-next-link">';
	next_posts_link('>>');
	echo '</li>';
	
	if ($end_page < $max_page) {
		$last_page_text = "Letzte";
		echo '<li class="gpn-last-page-link"><a href="' . get_pagenum_link($max_page) . '" title="' . $last_page_text . '">' . $last_page_text . '</a></li>';
	}
	
	echo '</ol></nav>' . $after . "";
}

function filter_ptags_on_images($content)
{
    return preg_replace('/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '<figure>$1</figure>', $content);
}
add_filter('the_content', 'filter_ptags_on_images');

// remove annyoing divs on images and use figure & figcaption
function gironimo_img_caption_shortcode_filter($val, $attr, $content=null)
{
    extract(shortcode_atts(array(
        'id' => '',
        'align' => '',
        'width' => '',
        'caption' => ''
    ), $attr));
    
    if (1 > (int) $width || empty($caption)) return $val;
    
    return '<figure id="' . $id . '" class="wp-caption ' . esc_attr($align) . '" style="width: ' . $width . 'px;">' . do_shortcode($content) . '<figcaption class="wp-caption-text">' . $caption . '</figcaption></figure>';
}
add_filter('img_caption_shortcode', 'gironimo_img_caption_shortcode_filter', 10, 3);


// Enable shortcodes in widget areas
add_filter('widget_text', 'do_shortcode');

// Columns Shortcodes
// Don't forget to add _last behind the shortcode if it is the last column.

// Two Columns
function gironimo_shortcode_two_columns_one($atts, $content = null)
{
   return '<div class="two-columns-one"><p>' . $content . '</p></div>';
}
add_shortcode('two_columns_one', 'gironimo_shortcode_two_columns_one');


function gironimo_shortcode_two_columns_one_last($atts, $content = null)
{
   return '<div class="two-columns-one last"><p>' . $content . '</p></div>';
}
add_shortcode('two_columns_one_last', 'gironimo_shortcode_two_columns_one_last');


// Three Columns
function gironimo_shortcode_three_columns_one($atts, $content = null)
{
   return '<div class="three-columns-one"><p>' . $content . '</p></div>';
}
add_shortcode('three_columns_one', 'gironimo_shortcode_three_columns_one');


function gironimo_shortcode_three_columns_one_last($atts, $content = null)
{
   return '<div class="three-columns-one last"><p>' . $content . '</p></div>';
}
add_shortcode('three_columns_one_last', 'gironimo_shortcode_three_columns_one_last');


function gironimo_shortcode_three_columns_two($atts, $content = null)
{
   return '<div class="three-columns-two"><p>' . $content . '</p></div>';
}
add_shortcode('three_columns_two', 'gironimo_shortcode_three_columns');

function gironimo_shortcode_three_columns_two_last($atts, $content = null)
{
   return '<div class="three-columns-two last"><p>' . $content . '</p></div>';
}
add_shortcode('three_columns_two_last', 'gironimo_shortcode_three_columns_two_last');


// Four Columns
function gironimo_shortcode_four_columns_one($atts, $content = null)
{
   return '<div class="four-columns-one"><p>' . $content . '</p></div>';
}
add_shortcode('four_columns_one', 'gironimo_shortcode_four_columns_one');


function gironimo_shortcode_four_columns_one_last($atts, $content = null)
{
   return '<div class="four-columns-one last"><p>' . $content . '</p></div>';
}
add_shortcode('four_columns_one_last', 'gironimo_shortcode_four_columns_one_last');


function gironimo_shortcode_four_columns_two($atts, $content = null)
{
   return '<div class="four-columns-two"><p>' . $content . '</p></div>';
}
add_shortcode('four_columns_two', 'gironimo_shortcode_four_columns_two');


function gironimo_shortcode_four_columns_two_last($atts, $content = null)
{
   return '<div class="four-columns-two last"><p>' . $content . '</p></div>';
}
add_shortcode('four_columns_two_last', 'gironimo_shortcode_four_columns_two_last');


function gironimo_shortcode_four_columns_three($atts, $content = null)
{
   return '<div class="four-columns-three"><p>' . $content . '</p></div>';
}
add_shortcode('four_columns_three', 'gironimo_shortcode_four_columns_three');


function gironimo_shortcode_four_columns_three_last($atts, $content = null)
{
   return '<div class="four-columns-three last"><p>' . $content . '</p></div>';
}
add_shortcode('four_columns_three_last', 'gironimo_shortcode_four_columns_three_last');


// Divide Text Shortcode
function gironimo_shortcode_divider($atts, $content = null) {
   return '<div class="clear"></div>';
}
add_shortcode('divider', 'gironimo_shortcode_divider');


// highlight
function gironimo_shortcode_highlight($atts, $content=null)
{
   return '<mark>' . $content . '</mark>';
}
add_shortcode('highlight', 'gironimo_shortcode_highlight');


// boxes
function gironimo_shortcode_info_box($atts, $content=null)
{
   return '<div class="box info-box"><p>' . $content . '</p></div>';
}
add_shortcode('info_box', 'gironimo_shortcode_info_box');


function gironimo_shortcode_alert_box($atts, $content=null)
{
   return '<div class="box alert-box"><p>' . $content . '</p></div>';
}
add_shortcode('alert_box', 'gironimo_shortcode_alert_box');


function gironimo_shortcode_normal_box($atts, $content=null)
{
   return '<div class="box normal-box"><p>' . $content . '</p></div>';
}
add_shortcode('normal_box', 'gironimo_shortcode_normal_box' );


function gironimo_shortcode_download_box($atts, $content=null)
{
    return '<div class="box download-box"><p>' . $content . '</p></div>';
}
add_shortcode('download_box', 'gironimo_shortcode_download_box');

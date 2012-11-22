<?php

/**
 * file: functions.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

// require need functions
require_once('lib/gironimo.php');
require_once('lib/plugins.php');
require_once('lib/widgets.php');
require_once('lib/custom-post-type.php');
require_once('lib/admin.php');

// add some thumbail sizes
add_image_size('gironimo-thumb-600', 600, 150, true);
add_image_size('gironimo-thumb-300', 300, 100, true);
add_image_size('gironimo-thumb-262', 262, 205, true);

// activate sidebar
function gironimo_register_sidebars()
{
    register_sidebar(array(
        'id' => 'secondary',
        'name' => 'Rechte Sidebar',
        'description' => 'Die erste (primäre) Sidebar.',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
}

// bit.ly support
function gironimo_bitly()
{
    $url = get_permalink();
    $login = 'o_5il4s9gj2t';
    $apiKey = 'R_16adcaf932275dd3dbb4759458741ddb';
    $format = 'json';
    
    $bitly = 'http://api.bit.ly/v3/shorten?longUrl=' . urlencode($url) . '&login=' . $login . '&apiKey=' . $apiKey . '&format=' . $format;
    
    $response = get_remote_file($bitly);
    
    if (strtolower($format) == 'json')
    {
        $json = @json_decode($response, true);
        return $json['data']['url'];
    }
    else
    {
        $xml = simplexml_load_string($response);
        return 'http://bit.ly/' . $xml->results->nodeKeyVal->hash;
    }
}

function get_remote_file($url)
{
    if (ini_get('allow_url_fopen')) {
        return file_get_contents($url);
    }
    elseif (function_exists('curl_init')) {
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_HEADER, 0);
        $file = curl_exec($c);
        curl_close($c);
        return $file;
    }
    else {
        die('Error');
    }
}  

// comment layout
function gironimo_comments($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    switch ($comment->comment_type) :
        case 'pingback':
    ?>
    <li class="post pingback">
        <p><?php _e('Pingback:', 'gironimo'); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('Bearbeiten &rarr;', 'gironimo'), '<br/><span class="button button-edit">', '</span>'); ?></p>
    <?php
            break;
        case 'trackback':
    ?>
    <li class="post trackback">
        <p><?php _e('Trackback:', 'gironimo'); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('Bearbeiten &rarr;', 'gironimo'), '<br/><span class="button button-edit">', '</span>'); ?></p>
    <?php
            break;
        default:
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment">
            <header class="comment-meta clearfix">
                <figure class="comment-gravatar">
                    <?php echo get_avatar($comment, 65); ?>
                </figure>
                <?php printf(__( '%s', 'gironimo'), sprintf('<h4 class="fn">%s</h4>', get_comment_author_link() ) ); ?>
                <time datetime="<?php echo get_comment_date('Y-m-j'); ?>" pubdate><a href="<?php echo esc_url(get_comment_link( $comment->comment_ID )); ?>"><?php printf(__('%1$s um %2$s', 'gironimo'), get_comment_date(),  get_comment_time()); ?></a></time>
            </header>
            <section class="comment-content">
                <?php comment_text(); ?>
                <?php if ($comment->comment_approved == '0'): ?>
                    <em class="comment-awaiting-moderation"><?php _e('Dein Kommentar wird gerade von einem Moderator geprüft.', 'gironimo'); ?></em>
                <?php endif; ?>
                <div class="reply">
                    <?php comment_reply_link(array_merge($args, array('reply_text' => '<span class="button">Antworten <span>&darr;</span></span>', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                    <?php edit_comment_link(__('Bearbeiten &rarr;', 'gironimo'), '<span class="button button-edit">', '</span>'); ?>
                </div>
            </section>
        </article>
        <?php
            break;
    endswitch;
}

function gironimo_comment_fields($fields)
{
    $fields['author'] = '<div class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
                        '<input id="author" class="user-input" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></div>';
    
    $fields['email'] = '<div class="comment-form-email"><label for="email">' . __( 'Email' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
                       '<input id="email" class="user-input" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></div>';
    
    $fields['url'] = '<div class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
                     '<input id="url" class="user-input" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></div>';
    
    return $fields;
}
add_filter('comment_form_default_fields', 'gironimo_comment_fields');

// search form layout
function gironimo_wpsearch($form)
{
    $placeholder = 'z.B. HTML5';
    if (get_search_query() != '') $placeholder = get_search_query();
    
    $form = '<form role="search" method="get" class="searchform" action="' . get_bloginfo('url') . '" >
    <fieldset><legend class="hidden">Suchen:</legend><div class="clearfix"><label class="visuallyhidden" for="s">' . __('Suchen', 'gironimo') . '</label>
    <input type="search" class="user-input" placeholder="' . $placeholder . '" name="s" id="s" />
    <button type="submit" class="button button-search"><span>' . esc_attr__('Suchen', 'gironimo') . '</span></button>
    </div></fieldset>
    </form>';
    
    return $form;
}

?>

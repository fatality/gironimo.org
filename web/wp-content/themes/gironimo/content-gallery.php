<?php

/**
 * file: content-gallery.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
    <header class="entry-header">
        <h2 itemprop="headline"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
        <ul class="entry-meta">
            <li class="author"><?php _e('von', 'gironimo'); ?> <?php the_author_posts_link(); ?></li>
            <li class="date"><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date(); ?></time></li>
            <li class="category"><?php printf(__('%2$s', 'gironimo'), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list(', ')); ?></li>
            <li class="comment"><?php comments_popup_link(__('0 Kommentare', 'gironimo' ), __('1 Kommentar', 'gironmo'), __('% Kommentare', 'gironimo')); ?></li>
        </ul>
    </header>
    <section class="entry-content clearfix" itemprop="articleBody">
        <?php if (is_search()): ?>
            <?php the_excerpt(__('Guck dir die Bilder an &rarr;', 'gironimo')); ?>
        <?php else: ?>
            <?php if (post_password_required()): ?>
                <?php the_content(__('Guck dir die Bilder an &rarr;', 'gironimo')); ?>
            <?php else: ?>
                <?php
                    $images = get_children(array('post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999));
                    if ($images):
                        $total_images = count($images);
                        $image = array_shift($images);
                        $image_img_tag = wp_get_attachment_image($image->ID, 'medium');
                ?>
                <figure class="gallery-thumb">
                    <a href="<?php the_permalink(); ?>"><?php echo $image_img_tag; ?></a>
                </figure>
                    <?php endif; ?>
                <?php the_content(__('Guck dir die Bilder an &rarr;', 'gironimo')); ?>
            <?php endif; ?>
            <p class="pics-count">
                <?php
                    printf(
                        _n(
                            'In dieser Gallerie sind <a %1$s>%2$s Bilder</a> enthalten.', 
                            'In dieser Gallerie sind <a %1$s>%2$s Bilder</a>', 
                            $total_images, 
                            'gironimo'
                        ),
                        'href="' . get_permalink() . '" title="' . the_title_attribute() . '" rel="bookmark"',
                        number_format_i18n($total_images)
					);
                ?>
            </p>
            <?php wp_link_pages(array('before' => '<div class="page-link">' . __('Seiten:', 'gironimo'), 'after' => '</div>')); ?>
        <?php endif; ?>
    </section>
    <footer class="entry-footer">
        <ul class="entry-meta">
            <?php
                $tags = get_the_tag_list('', '');
                $shorturl = gironimo_bitly();
                if (!$shorturl && $shorturl == '')
                    $shorturl = wp_get_shortlink();
            ?>
            <?php if ($tags): ?><li class="tags">Tags: <?php printf(__('%2$s', 'gironimo'), 'entry-utility-prep entry-utility-prep-tag-links', get_the_tag_list('', '')); ?></li><?php endif; ?>
            <li class="shorturl">Shorturl: <a href="<?php echo $shorturl; ?>" rel="shorturl"><?php echo $shorturl; ?></a></li>
        </ul>
        <?php edit_post_link(__('Bearbeiten &rarr;', 'gironimo'), '<span class="button button-edit">', '</span>'); ?>
    </footer>
</article>

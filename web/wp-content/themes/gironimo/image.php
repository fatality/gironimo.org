<?php

/**
 * file: image.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<div id="main">
    <div id="content" role="main">
        <?php the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
            <header class="entry-header">
                <h2 rev="attachment"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                <ul class="entry-meta">
                    <li class="author"><?php _e('von', 'gironimo'); ?> <?php the_author_posts_link(); ?></li>
                    <li class="date"><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date(); ?></time>
                    <li class="category"><?php printf(__('%2$s', 'gironimo'), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list(', ')); ?></li>
                    <li class="comment"><?php comments_popup_link(__('0 Kommentare', 'gironimo' ), __('1 Kommentar', 'gironmo'), __('% Kommentare', 'gironimo')); ?></li>
                </ul>
            </header>
            <section class="entry-content clearfix">
                <nav class="image-nav">
                    <ul>
                        <li class="prev-link"><?php previous_image_link(false, __('&larr; vorheriges Bild', 'gironimo')); ?></li>
                        <li class="next-link"><?php next_image_link(false, __('Nächstes Bild &rarr;' , 'gironimo')); ?></li>
                    </ul>
                </nav>
                <figure class="post-image-full">
                    <?php
                        $attachments = array_values(get_children(array(
                            'post_parent' => $post->post_parent,
                            'post_status' => 'inherit',
                            'post_type' => 'attachment',
                            'post_mime_type' => 'image',
                            'order' => 'ASC',
                            'orderby' => 'menu_order ID'
                        )));
                        foreach ($attachments as $k => $attachment) {
                            if ($attachment->ID == $post->ID) break;
                        }
                        $k++;
                        if (count($attachments) > 1) {
                            if (isset($attachments[$k]))
                                $next_attachment_url = get_attachment_link($attachments[$k]->ID);
                            else
                                $next_attachment_url = get_attachment_link($attachments[0]->ID);
                        } else {
                            $next_attachment_url = wp_get_attachment_url();
                        }
                    ?>
                    <a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment">
                        <?php
                            $attachment_size = apply_filters('theme_attachment_size',  800);
                            echo wp_get_attachment_image($post->ID, array($attachment_size, 9999));
                        ?>
                    </a>
                    <?php if (!empty($post->post_excerpt)): ?>
                        <figcaption>
                            <?php the_excerpt(); ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
                <?php the_content(__('weiterlesen &rarr;', 'gironimo')); ?>
                <?php wp_link_pages(array('before' => '<div class="page-link">' . __('Seiten:', 'gironimo'), 'after' => '</div>')); ?>
            </section>
            <footer class="entry-footer">
                <ul class="entry-meta">
                    <?php $tags = get_the_tag_list('', ''); ?>
                    <?php if ($tags): ?><li class="tags">Tags: <?php printf(__('%2$s', 'gironimo'), 'entry-utility-prep entry-utility-prep-tag-links', get_the_tag_list('', '')); ?></li><?php endif; ?>
                    <li class="shorturl">Shorturl: <?php if (gironimo_bitly() != ''): ?><a href="<?php gironimo_bitly(); ?>" rel="shorturl"><?php gironimo_bitly(); ?></a><?php else: ?><?php endif; ?><a href="<?php echo wp_get_shortlink(); ?>" rel="shorturl"><?php echo wp_get_shortlink(); ?></a></li>
                </ul>
                <?php edit_post_link(__('Bearbeiten &rarr;', 'gironimo'), '<span class="edit-link">', '</span>'); ?>
            </footer>
        </article>
        <?php comments_template(); ?>
    </div>
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

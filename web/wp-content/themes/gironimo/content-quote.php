<?php

/**
 * file: content-quote.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
    <?php if (is_archive() || is_search()): ?>
        <?php if (has_post_thumbnail()): ?>
            <figure class="post-thumbnail">
                <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                    <?php the_post_thumbnail(array(125, 125)); ?>
                </a>
            </figure>
        <?php endif; ?>
        <header class="entry-header">
            <h2 itemprop="headline"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <ul class="entry-meta">
                <li class="date"><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date(); ?></time></li>
                <li class="comment"><?php comments_popup_link(__('0 Kommentare', 'gironimo' ), __('1 Kommentar', 'gironmo'), __('% Kommentare', 'gironimo')); ?></li>
            </ul>
        </header>
        <section class="entry-content archive clearfix" itemprop="articleBody">
            <?php the_excerpt(); ?>
        </section>
        <?php if (has_post_thumbnail()): ?><div class="clear"></div><?php endif; ?>
    <?php else: ?>
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
            <?php the_content(__('weiterlesen &rarr;', 'gironimo')); ?>
            <?php wp_link_pages(array('before' => '<div class="page-link">' . __('Seiten:', 'gironimo'), 'after' => '</div>')); ?>
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
    <?php endif; ?>
</article>

<?php

/*
This is the custom post type post template. If you edit the post type name, 
you've got to change the name of this template to reflect that name change.

i.e. if your custom post type is called register_post_type('bookmarks',) then 
your single template should be single-bookmarks.php
*/

/**
 * file: single-custom_type.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<div id="main">
    <div id="content" role="main">
        <?php if (have_posts()): while (have_posts()): the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
                <header class="entry-header">
                    <h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                    <ul class="entry-meta">
                        <li class="author">von <?php the_author_posts_link(); ?></li>
                        <li class="date"><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time>
                        <li class="category"><?php the_category(', '); ?></li>
                        <li class="comment"><?php comments_number('0 Kommentare', '1 Kommentar', '% Kommentare'); ?></li>
                    </ul>
                </header>
                <section class="entry-content clearfix">
                    <?php the_content(); ?>
                </section>
                <footer class="entry-footer">
                    <ul class="entry-meta">
                        <li class="tags"><?php echo get_the_term_list( get_the_ID(), 'custom_tag', '<span class="tags-title">Custom Tags:</span> ', ', ' ) ?></li>
                        <li class="shorturl"></li>
                    </ul>
                </footer>
            </article>
            <?php comments_template(); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <article id="post-not-found">
                <header class="entry-header">
                    <h2>Keine Artikel bisher</h2>
                </header>
                <section class="entry-content">
                    <p>Deine Suche ergab leider keine Treffer... versuche es erneut.</p>
                    <?php get_search_form(); ?>
                </section>
                <footer class="entry-footer"></footer>
            </article>
        <?php endif; ?>
    </div>
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

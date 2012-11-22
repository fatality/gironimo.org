<?php

/*
Template Name: Custom Page Example
*/

/**
 * file: page-custom.php
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
                    <h2><?php the_title(); ?></h2>
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
                        <li class="tags"><?php the_tags('', ', ', ''); ?></li>
                        <li class="shorturl"></li>
                    </ul>
                </footer>
            </article>
            <?php comments_template(); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <article id="post-not-found">
                <header class="entry-header">
                    <h2>404: Sorry, aber die angeforderte Seite konnte nicht gefunden werden!</h2>
                </header>
                <section class="entry-content">
                    <p>Da stimmt was nicht... Irgendwie konnte die von Ihnen gesuchte Seite nicht gefunden werden. Versuchen sie unsere Suche und probieren es noch einmal.</p>
                    <?php get_search_form(); ?>
                </section>
                <footer class="entry-footer"></footer>
            </article>
        <?php endif; ?>
    </div>
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

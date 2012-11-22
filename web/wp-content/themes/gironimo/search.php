<?php

/**
 * file: search.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<div id="main">
    <div id="content" role="main">
        <h1 class="archive_title">
            <?php _e('Suchergebnisse für:', 'gironimo'); ?> <mark><?php echo esc_attr(get_search_query()); ?></mark>
        </h1>
        <?php if (have_posts()) : ?>
            <?php while(have_posts()): the_post(); ?>
                <?php get_template_part('content', 'search'); ?>
            <?php endwhile; ?>
            <?php if (function_exists('page_navi')): ?>
                <?php page_navi(); ?>
            <?php else: ?>
                <nav class="wp-prev-next">
                    <ul class="clearfix">
                        <li class="prev-link"><?php next_posts_link(_e('Ältere Einträge', 'gironimo')); ?></li>
                        <li class="next-link"><?php previous_posts_link(_e('Neuere Einträge', 'gironimo')); ?></li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <article id="post-not-found">
                <header class="entry-header">
                    <h2><?php _e('Keine Ergebnisse gefunden', 'gironimo'); ?></h2>
                </header>
                <section class="entry-content">
                    <p><?php _e('Entschuldigung, jedoch wurde deine Sucheingabe nicht gefunden, probiers noch einmal.', 'gironimo'); ?></p>
                    <?php get_search_form(); ?>
                </section>
                <footer class="entry-footer"></footer>
            </article>
        <?php endif; ?>
    </div>
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

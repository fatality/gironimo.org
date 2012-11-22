<?php

/**
 * file: category.php
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
            <?php printf(__('Artikel der Kategorie: <mark>%s</mark>', 'gironimo'), single_cat_title('', false)); ?>
        </h1>
        
        <?php while(have_posts()): the_post(); ?>
            <?php get_template_part('content', get_post_format()); ?>
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
    </div>
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

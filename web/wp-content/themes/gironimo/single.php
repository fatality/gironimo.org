<?php

/**
 * file: single.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<div id="main">
    <div id="content" role="main">
        <?php if (have_posts()) while(have_posts()): the_post(); ?>
            <?php get_template_part('content', 'single'); ?>
            <?php comments_template('', true); ?>
        <?php endwhile; ?>
    </div>
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

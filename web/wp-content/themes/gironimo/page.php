<?php

/**
 * file: page.php
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
        <?php get_template_part('content', 'page'); ?>
        <?php comments_template('', true); ?>
    </div>
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

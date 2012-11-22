<?php

/**
 * file: author.php
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
        
        <h1 class="archive_title">
            Artikel von
            <?php
                $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
                $google_profile = get_the_author_meta('google_profile', $curauth->ID);
                if ($google_profile) {
                    echo '<a href="' . esc_url($google_profile) . '" rel="me">' . $curauth->display_name . '</a>';
                } else {
                    echo "<mark>" . get_author_name(get_query_var('author')) . "</mark>";
                }
            ?>
        </h1>
        
        <?php if (get_the_author_meta('description')): ?>
            <aside class="author-info" style="margin: 0 0 2em;">
                <?php echo get_avatar(get_the_author_meta('user_email'), apply_filters('gironimo_author_bio_avatar_size', 70)); ?>
                <div class="author-description">
                    <h3><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" title="<?php echo esc_attr(get_the_author()); ?>" rel="me"><?php echo get_the_author(); ?></a></h3>
                    <p><?php the_author_meta('description'); ?></p>
                </div>
            </aside>
        <?php endif; ?>
        
        <?php rewind_posts(); ?>
        
        <?php while (have_posts()): the_post(); ?>
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

<?php

/**
 * file: content-page.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
    <header class="entry-header">
        <h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
    </header>
    <section class="entry-content clearfix">
        <?php the_content(); ?>
        <?php wp_link_pages(array('before' => '<div class="page-link">' . __('Seiten:', 'gironimo'), 'after' => '</div>')); ?>
    </section>
    <footer class="entry-footer">
        <?php edit_post_link(__('Bearbeiten &rarr;', 'gironimo'), '<span class="button button-edit">', '</span>'); ?>
    </footer>
</article>

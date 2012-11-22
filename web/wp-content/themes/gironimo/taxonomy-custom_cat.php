<?php
/*
This is the custom post type taxonomy template. If you edit the custom taxonomy 
name, you've got to change the name of this template to reflect that name 
change.

i.e. if your custom taxonomy is called register_taxonomy('shoes',) then your 
single template should be taxonomy-shoes.php
*/

/**
 * file: taxonomy-custom_cat.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<div id="main">
    <div id="content" role="main">
        <h1 class="archive_title">Artikel kategorisiert unter <?php single_cat_title(); ?><7h1>
        <?php if (have_posts()): while (have_posts()): the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
                <header class="entry-header">
                    <h2 itemprop="headline"><?php the_title(); ?></h2>
                    <ul class="entry-meta">
                        <li class="author">von <?php the_author_posts_link(); ?></li>
                        <li class="date"><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time>
                        <li class="category"><?php the_category(', '); ?></li>
                        <li class="comment"><?php comments_number('0 Kommentare', '1 Kommentar', '% Kommentare'); ?></li>
                    </ul>
                </header>
                <section class="entry-content clearfix" itemprop="articleBody">
                    <?php the_post_thumbnail('gironimo-thumb-262'); ?>
                    <?php the_excerpt(); ?>
                </section>
                <footer class="entry-footer">
                    <ul class="entry-meta">
                        <li class="tags"><?php the_tags('', ', ', ''); ?></li>
                        <li class="shorturl"></li>
                    </ul>
                </footer>
            </article>
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

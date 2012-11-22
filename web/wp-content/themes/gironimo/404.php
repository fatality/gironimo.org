<?php

/**
 * file: 404.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<div id="main">
    <div id="content" role="main">
        <article id="post-not-found">
            <header class="entry-header">
                <h2><?php _e('404: Sorry, aber die angeforderte Seite konnte nicht gefunden werden!', 'gironimo'); ?></h2>
            </header>
            <section class="entry-content">
                <p><?php _e('Da stimmt was nicht... Irgendwie konnte die von Ihnen gesuchte Seite nicht gefunden werden. Versuchen sie unsere Suche und probieren es noch einmal.'); ?></p>
                <?php get_search_form(); ?>
                <script type="text/javascript">
                    document.getElementById('s') && document.getElementById('s').focus();
                </script>
            </section>
            <footer class="entry-footer"></footer>
        </article>
    </div>
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

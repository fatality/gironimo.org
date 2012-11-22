<?php

/**
 * file: content-single.php
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

get_header();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
    <header class="entry-header">
        <h2 itemprop="headline"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
        <ul class="entry-meta">
            <li class="author"><?php _e('von', 'gironimo'); ?> <?php the_author_posts_link(); ?></li>
            <li class="date"><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date(); ?></time></li>
            <li class="category"><?php printf(__('%2$s', 'gironimo'), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list(', ')); ?></li>
            <li class="comment"><?php comments_popup_link(__('0 Kommentare', 'gironimo' ), __('1 Kommentar', 'gironmo'), __('% Kommentare', 'gironimo')); ?></li>
        </ul>
        <ul class="social-media-bar">
            <li class="twitter">
                <a href="https://twitter.com/share" class="twitter-share-button" data-via="mrochow86" data-text="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>" data-lang="de">Twittern</a>
                <script type="text/javascript">
                    !function(d,s,id){
                        var js,fjs=d.getElementsByTagName(s)[0];
                        if(!d.getElementById(id)){
                            js=d.createElement(s);
                            js.id=id;js.src="//platform.twitter.com/widgets.js";
                            fjs.parentNode.insertBefore(js,fjs);
                        }
                    }
                    (document,"script","twitter-wjs");
                </script>
            </li>
            <li class="googleplus">
                <div class="g-plusone" data-size="medium" data-href="<?php the_permalink(); ?>"></div>
                <script type="text/javascript">
                    window.___gcfg = {lang: 'de'};
                    
                    (function() {
                        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                        po.src = 'https://apis.google.com/js/plusone.js';
                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                    })();
                </script>
            </li>
            <li class="facebook">
                <div class="fb-like" data-send="false" data-layout="button_count" data-width="200" data-show-faces="false" href="<?php echo get_permalink($post->ID); ?>"></div>
            </li>
        </ul>
    </header>
    <section class="entry-content clearfix" itemprop="articleBody">
        <?php the_content(__('weiterlesen &rarr;', 'gironimo')); ?>
        <?php wp_link_pages(array('before' => '<div class="page-link">' . __('Seiten:', 'gironimo'), 'after' => '</div>')); ?>
    </section>
    <footer class="entry-footer">
        <ul class="entry-meta">
            <?php
                $tags = get_the_tag_list('', '');
                $shorturl = gironimo_bitly();
                if (!$shorturl && $shorturl == '')
                    $shorturl = wp_get_shortlink();
            ?>
            <?php if ($tags): ?><li class="tags">Tags: <?php printf(__('%2$s', 'gironimo'), 'entry-utility-prep entry-utility-prep-tag-links', get_the_tag_list('', '')); ?></li><?php endif; ?>
            <li class="shorturl">Shorturl: <a href="<?php echo $shorturl; ?>" rel="shorturl"><?php echo $shorturl; ?></a></li>
        </ul>
        <?php edit_post_link(__('Bearbeiten &rarr;', 'gironimo'), '<span class="button button-edit">', '</span>'); ?>
    </footer>
    <?php if (function_exists('gironimo_related_posts')): ?>
        <aside class="related-posts">
            <h3>Verwandte Artikel:</h3>
            <?php gironimo_related_posts(); ?>
        </aside>
    <?php endif; ?>
    <?php if (get_the_author_meta('description')): ?>
        <aside class="author-info">
            <?php echo get_avatar(get_the_author_meta('user_email'), apply_filters('gironimo_author_bio_avatar_size', 70)); ?>
            <div class="author-description">
                <h3><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" title="<?php echo esc_attr(get_the_author()); ?>" rel="me"><?php echo get_the_author(); ?></a></h3>
                <p><?php the_author_meta('description'); ?></p>
            </div>
        </aside>
    <?php endif; ?>
</article>

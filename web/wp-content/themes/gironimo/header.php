<?php

/**
 * file: header.php
 *
 * includes html doctype, head and everything up until main content
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

?>

<!DOCTYPE html>

<!--[if IEMobile 7 ]> <html <?php language_attributes(); ?> class="no-js iem7"> <![endif]-->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 8)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html class="no-js" <?php language_attributes(); ?>><!--<![endif]-->

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    
    <title><?php wp_title(''); ?></title>
    
    <?php /* meta tags handled by SEO Plugin */ ?>
    
    <meta name="viewport" content="width=device-width" />
    <meta name="application-name" content="<?php bloginfo('name'); ?>" />
    
    <?php /* icons & favion */ ?>
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/lib/images/favicon.ico" />
    
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    
    <?php wp_head(); ?>
    
    <!--[if (lt IE 9) & (!IEMobile)]>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/lib/css/ie.css" />
    <![endif]-->
</head>

<body <?php body_class(); ?>>
    <div id="fb-root"></div>
    <div id="fb-root"></div>
    <script type="text/javascript">
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/de_DE/all.js#xfbml=1&appId=142960165750681";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    <header id="branding" role="banner">
        <hgroup id="site-title" class="clearfix">
            <h1>
                <a href="<?php echo home_url(); ?>" rel="home" title="Zurück zur Startseite">gironimo<span>.org</span></a>
            </h1>
            <h2><?php bloginfo('description'); ?></h2>
        </hgroup>
        <?php get_search_form(); ?>
        <nav id="main-nav" class="clearfix" role="navigation">
            <?php gironimo_main_nav(); ?>
        </nav>
        <blockquote id="mission" cite="http://www.tortugabay.org">
            <p>
                We can't just stand here, code something. Code is art!
            </p>
        </blockquote>
        <ul class="social-bar clearfix">
            <li class="fb"><a href="http://www.facebook.com/fatality86" title="gironimo.org auf FaceBook"><img src="<?php echo get_template_directory_uri(); ?>/lib/images/facebook.png" alt="Facebook" height="32" width="32" /></a></li>
            <li class="g+"><a href="https://plus.google.com/115607138108680330105" title="gironimo.org auf Google+"><img src="<?php echo get_template_directory_uri(); ?>/lib/images/googleplus.png" alt="Google+" height="32" width="32" /></a></li>
            <li class="twitter"><a href="https://twitter.com/#!/mrochow86" title="gironimo.org auf Twitter"><img src="<?php echo get_template_directory_uri(); ?>/lib/images/twitter.png" alt="Twitter" height="32" width="32" /></a></li>
            <li class="rss"><a href="<?php bloginfo('rss2_url'); ?>" title="gironimo.org RSS Feed"><img src="<?php echo get_template_directory_uri(); ?>/lib/images/rss.png" alt="RSS Feed" height="32" width="32" /></a></li>
        </ul>
        <div class="header-border"></div>
    </header>

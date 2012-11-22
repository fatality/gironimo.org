<?php

/**
 * file: admin.php
 * This file handles the admin area and functions. You can use this file to make
 * changes to the dashboard. Updates to this page are coming soon. It's turned 
 * off by default, but you can call it via the functions file.
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

// call really nice CSS file to style login
function gironimo_login_css()
{
    echo '<link rel="stylesheet" href="' . get_stylesheet_directory_uri() . '/lib/css/login.css" />';
}

function gironimo_login_url()
{
    echo bloginfo('url');
}

function gironimo_login_title()
{
    echo get_option('blogname');
}

add_action('login_head', 'gironimo_login_css');
add_filter('login_headerurl', 'gironimo_login_url');
add_filter('login_headertitle', 'gironimo_login_title');


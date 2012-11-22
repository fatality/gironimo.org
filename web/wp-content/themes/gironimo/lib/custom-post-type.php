<?php

/**
 * file: custom-post-type.php
 * This page walks you through creating a custom post type and taxonomies. You
 * can edit this one or copy the following code to create another one.
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

// let's create the function for the custom type
function custom_post_example()
{ 
    // creating (registering) the custom type 
    register_post_type('custom_type', array(
        'labels' => array(
            'name' => __('Custom Types', 'post type general name'),
            'singular_name' => __('Custom Post', 'post type singular name'),
            'add_new' => __('Add New', 'custom post type item'),
            'add_new_item' => __('Add New Custom Type'),
            'edit' => __('Edit'),
            'edit_item' => __('Edit Post Types'),
            'new_item' => __('New Post Type'),
            'view_item' => __('View Post Type'),
            'search_items' => __('Search Post Type'),
            'not_found' =>  __('Nothing found in the Database.'),
            'not_found_in_trash' => __('Nothing found in Trash'),
            'parent_item_colon' => ''
        ),
        'description' => __('This is the example custom post type'),
        'public' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'show_ui' => true,
        'query_var' => true,
        'menu_position' => 8,
        'menu_icon' => get_stylesheet_directory_uri() . '/lib/images/custom-post-icon.png',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky')
    ));
    register_taxonomy_for_object_type('category', 'custom_type');
    register_taxonomy_for_object_type('post_tag', 'custom_type');
}
add_action('init', 'custom_post_example');

register_taxonomy(
    'custom_cat',
    array('custom_type'),
    array(
        'hierarchical' => true,
        'labels' => array(
            'name' => __('Custom Categories'),
            'singular_name' => __('Custom Category'),
            'search_items' =>  __('Search Custom Categories'),
            'all_items' => __('All Custom Categories'),
            'parent_item' => __('Parent Custom Category'),
            'parent_item_colon' => __('Parent Custom Category:'),
            'edit_item' => __('Edit Custom Category'),
            'update_item' => __('Update Custom Category'),
            'add_new_item' => __('Add New Custom Category'),
            'new_item_name' => __('New Custom Category Name')
        ),
        'show_ui' => true,
        'query_var' => true,
    )
);

register_taxonomy(
    'custom_tag',
    array('custom_type'),
    array(
        'hierarchical' => false,
        'labels' => array(
            'name' => __('Custom Tags'),
            'singular_name' => __('Custom Tag'),
            'search_items' =>  __('Search Custom Tags'),
            'all_items' => __('All Custom Tags'),
            'parent_item' => __('Parent Custom Tag'),
            'parent_item_colon' => __('Parent Custom Tag:'),
            'edit_item' => __('Edit Custom Tag'),
            'update_item' => __('Update Custom Tag'),
            'add_new_item' => __('Add New Custom Tag'),
            'new_item_name' => __('New Custom Tag Name')
        ),
        'show_ui' => true,
        'query_var' => true,
    )
); 

?>

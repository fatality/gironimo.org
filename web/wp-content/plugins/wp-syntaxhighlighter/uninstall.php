<?php
/*
by Redcocker
Last modified: 2011/11/22
License: GPL v2
http://www.near-mint.com/blog/
*/

if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {exit();}
delete_option('wp_sh_setting_opt');
delete_option('wp_sh_version'); // For backward compatibility
delete_option('wp_sh_code_title');
delete_option('wp_sh_class_name');
delete_option('wp_sh_addl_style');
delete_option('wp_sh_collapse_lable_text');
delete_option('wp_sh_gutter'); // For backward compatibility
delete_option('wp_sh_first_line'); // For backward compatibility
delete_option('wp_sh_comment_hl_description_before');
delete_option('wp_sh_comment_hl_stylesheet');
delete_option('wp_sh_bbpress_hl_description_before');
delete_option('wp_sh_bbpress_hl_stylesheet');
delete_option('wp_sh_checkver_stamp');
delete_option('wp_sh_updated');
delete_option('wp_sh_language3');
delete_option('wp_sh_language2');
delete_option('wp_sh_brush_files');
delete_option('wp_sh_child_plugin');
delete_option('widget_wpsyntaxhighlighterwidget');
?>
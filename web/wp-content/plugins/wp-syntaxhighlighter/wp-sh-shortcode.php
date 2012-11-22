<?php
/*
Shortcode
by Redcocker
Last modified: 2012/2/7
License: GPL v2
http://www.near-mint.com/blog/

This file contains codes from /wp-includes/shortcodes.php.
*/

// Remove duplicate shortcode
add_action('init', 'wp_sh_remove_shortcode', 99);

function wp_sh_remove_shortcode() {
	remove_shortcode('sourcecode');
	remove_shortcode('source');
	remove_shortcode('code');
}

// Shortcode tags
function wp_sh_shortcode_tags() {
	$wp_sh_shortcode_tags = array(
		'sourcecode' => 'wp_sh_shortcode_handler',
		'source' => 'wp_sh_shortcode_handler',
		'code' => 'wp_sh_shortcode_handler',
	);
	return $wp_sh_shortcode_tags;
}

// Shortcode handler
function wp_sh_shortcode_handler($atts, $content = null) {
	global $wp_sh_setting_opt;

	extract(wp_sh_shortcode_atts(array(
		'language' => 'text',
		'lang' => '',
		'autolinks' => $wp_sh_setting_opt['auto_links'],
		'collapse' => $wp_sh_setting_opt['collapse'],
		'gutter' => $wp_sh_setting_opt['gutter'],
		'firstline' => $wp_sh_setting_opt['first_line'],
		'highlight' => '',
		'htmlscript' => 'false',
		'light' => 'false',
		'padlinenumbers' => $wp_sh_setting_opt['padding_line'],
		'toolbar' => $wp_sh_setting_opt['toolbar'],
		'wraplines' => $wp_sh_setting_opt['wrap'],
	), $atts));

	$language = esc_attr($language);
	if ($lang != "") {
		$lang = esc_attr($lang);
	}
	$autolinks = esc_attr($autolinks);
	$collapse = esc_attr($collapse);
	$gutter = esc_attr($gutter);
	$firstline = esc_attr($firstline);
	$highlight = esc_attr($highlight);
	$htmlscript = esc_attr($htmlscript);
	$light = esc_attr($light);
	$padlinenumbers = esc_attr($padlinenumbers);
	$toolbar = esc_attr($toolbar);
	$wraplines = esc_attr($wraplines);

	if ($lang == "") {
		if ($wp_sh_setting_opt['lib_version'] == "2.1") {
			return '<pre class="brush: '.$language.'; auto-links: '.$autolinks.'; collapse: '.$collapse.'; gutter: '.$gutter.'; first-line: '.$firstline.'; highlight: ['.$highlight.']; html-script: '.$htmlscript.'; light: '.$light.'; pad-line-numbers: '.$padlinenumbers.'; toolbar: '.$toolbar.'; wrap-lines: '.$wraplines.'">'.$content.'</pre>';
		} else {
			return '<pre class="brush: '.$language.'; auto-links: '.$autolinks.'; collapse: '.$collapse.'; gutter: '.$gutter.'; first-line: '.$firstline.'; highlight: ['.$highlight.']; html-script: '.$htmlscript.'; light: '.$light.'; pad-line-numbers: '.$padlinenumbers.'; toolbar: '.$toolbar.'\'">'.$content.'</pre>';
		}
	} else {
		if ($wp_sh_setting_opt['lib_version'] == "2.1") {
			return '<pre class="brush: '.$lang.'; auto-links: '.$autolinks.'; collapse: '.$collapse.'; gutter: '.$gutter.'; first-line: '.$firstline.'; highlight: ['.$highlight.']; html-script: '.$htmlscript.'; light: '.$light.'; pad-line-numbers: '.$padlinenumbers.'; toolbar: '.$toolbar.'; wrap-lines: '.$wraplines.'">'.$content.'</pre>';
		} else {
			return '<pre class="brush: '.$lang.'; auto-links: '.$autolinks.'; collapse: '.$collapse.'; gutter: '.$gutter.'; first-line: '.$firstline.'; highlight: ['.$highlight.']; html-script: '.$htmlscript.'; light: '.$light.'; pad-line-numbers: '.$padlinenumbers.'; toolbar: '.$toolbar.'\'">'.$content.'</pre>';
		}
	}
}

// Search content for shortcodes and filter shortcodes through their hooks.
function wp_sh_do_shortcode($content) {
	$wp_sh_shortcode_tags = wp_sh_shortcode_tags();

	if (empty($wp_sh_shortcode_tags) || !is_array($wp_sh_shortcode_tags))
		return $content;

	$pattern = wp_sh_get_shortcode_regex();
	return preg_replace_callback('/'.$pattern.'/s', 'wp_sh_do_shortcode_tag', $content);
}

// Retrieve the shortcode regular expression for searching.
function wp_sh_get_shortcode_regex() {
	$wp_sh_shortcode_tags = wp_sh_shortcode_tags();
	$tagnames = array_keys($wp_sh_shortcode_tags);
	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
	return '(.?)\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)';
}

// Regular Expression callable for do_shortcode() for calling shortcode hook.
function wp_sh_do_shortcode_tag( $m ) {
	$wp_sh_shortcode_tags = wp_sh_shortcode_tags();

	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	$tag = $m[2];
	$attr = wp_sh_shortcode_parse_atts( $m[3] );

	if ( isset( $m[5] ) ) {
		// enclosing tag - extra parameter
		return $m[1] . call_user_func( $wp_sh_shortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
	} else {
		// self-closing tag
		return $m[1] . call_user_func( $wp_sh_shortcode_tags[$tag], $attr, NULL,  $tag ) . $m[6];
	}
}

// Retrieve all attributes from the shortcodes tag.
function wp_sh_shortcode_parse_atts($text) {
	$atts = array();
	$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
	$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
	if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
		foreach ($match as $m) {
			if (!empty($m[1]))
				$atts[strtolower($m[1])] = stripcslashes($m[2]);
			elseif (!empty($m[3]))
				$atts[strtolower($m[3])] = stripcslashes($m[4]);
			elseif (!empty($m[5]))
				$atts[strtolower($m[5])] = stripcslashes($m[6]);
			elseif (isset($m[7]) and strlen($m[7]))
				$atts[] = stripcslashes($m[7]);
			elseif (isset($m[8]))
				$atts[] = stripcslashes($m[8]);
		}
	} else {
		$atts = ltrim($text);
	}
	return $atts;
}

// Combine user attributes with known attributes and fill in defaults when need
function wp_sh_shortcode_atts($pairs, $atts) {
	$atts = (array)$atts;
	$out = array();
	foreach($pairs as $name => $default) {
		if ( array_key_exists($name, $atts) )
			$out[$name] = $atts[$name];
		else
			$out[$name] = $default;
	}
	return $out;
}

// Remove all shortcode tags
function wp_sh_strip_shortcodes($content) {
	$wp_sh_shortcode_tags = wp_sh_shortcode_tags();

	if (empty($wp_sh_shortcode_tags) || !is_array($wp_sh_shortcode_tags))
		return $content;

	$pattern = wp_sh_get_shortcode_regex();

	return preg_replace_callback( "/$pattern/s", 'strip_shortcode_tag', $content );
}

function wp_sh_strip_shortcode_tag( $m ) {
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	return $m[1] . $m[6];
}

?>
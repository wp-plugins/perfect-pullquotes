<?php
/*
Plugin Name: Perfect Pullquotes
Plugin URI: http://github.com/adamdehaven/perfect-pullquotes
Description: Add left or right-aligned, beautifully styled pullquotes. Includes two custom buttons for the Visual Editor as well as a custom shortcode.
Author: Adam Dehaven
Version: 1.0.2
Author URI: http://adamdehaven.com
*/
add_action( 'wp_enqueue_scripts', 'adamdehaven_pullquote_styles' );
function adamdehaven_pullquote_styles()
{
    // Register the style like this for a plugin:
    wp_register_style( 'perfect-pullquotes-styles', plugins_url( '/pullquote.css', __FILE__ ), array(), '1.0.2', 'all' ); 
    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style( 'perfect-pullquotes-styles' );
}

add_shortcode( 'pullquote', 'adamdehaven_pullquote' );
function adamdehaven_pullquote( $atts, $content = null ) {
	$a = shortcode_atts( array(
        'align' => 'left', // Align pullquote to the left or right. Default left.
        'color' => null, // Provide the HEX value of the border-color. Default #EEEEEE
        'class' => null, // Add additional classes to the div.pullquote object
        'cite'	=> null, // Add the name/source of the quote
        'link'	=> null, // Add a link to the cited source, must be http or https link
        ), $atts );

	// Pullquote alignment (left or right)
	$alignment = '';
	switch ( $a['align'] ) {
		case 'right':
			$alignment = 'pullquote-align-right';
			break;
		default:
			$alignment = 'pullquote-align-left';
			break;
	}

	// border-color: HEX value
	if ( isset($a['color']) && preg_match("/#([a-fA-F0-9]{3}){1,2}\b/",$a['color']) ):
        $color = ' style="border-color:'.$a['color'].' !important;"';
    else:
        $color = null;
    endif;

    // Check for cite
    if ( isset($a['cite']) ):
    	$citeText = strip_tags( $a['cite'] );
    else:
    	$citeText = null;
    endif;

    // Check for link
    if ( isset($a['link']) && preg_match("/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", $a['link']) ):
    	$citeLink = '<a href="'.$a['link'].'" target="_blank">'.$citeText.'</a>';
    else:
    	$citeLink = null;
    endif;

    // Create footer
    if ($citeLink && $citeText):
	    $citeFooter = '<footer><cite>'.$citeLink.'</cite></footer>';
	elseif($citeText):
		$citeFooter = '<footer><cite>'.$citeText.'</cite></footer>';
	else:
		$citeFooter = null;
	endif;

    return '<div class="pullquote '.$alignment.' '.esc_attr($a['class']).'"'.$color.'><blockquote><p>'.do_shortcode($content).'</p>'.$citeFooter.'</blockquote></div>';
}

add_action( 'init', 'adamdehaven_buttons' );
function adamdehaven_buttons() {
	add_filter("mce_external_plugins", "adamdehaven_add_buttons");
    add_filter('mce_buttons', 'adamdehaven_register_buttons');
}	
function adamdehaven_add_buttons($plugin_array) {
	$plugin_array['adamdehaven_perfect_pullquotes'] = plugins_url( '/pullquote-shortcode.js', __FILE__ );
	return $plugin_array;
}
function adamdehaven_register_buttons($buttons) {
	array_push( $buttons, 'pullquote-left', 'pullquote-right' );
	return $buttons;
}

?>
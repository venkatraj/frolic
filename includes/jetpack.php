<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Frolic
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function frolic_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'content',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'frolic_jetpack_setup' );

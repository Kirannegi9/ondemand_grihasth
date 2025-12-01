<?php
/**
 * Kivicare Design functions and definitions
 *
 * This file must be parseable by PHP 5.2.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package kivicare
 */

define( 'KIVICARE_MINIMUM_WP_VERSION', '4.5' );
define( 'KIVICARE_MINIMUM_PHP_VERSION', '7.0' );

// Bail if requirements are not met.
if ( version_compare( $GLOBALS['wp_version'], KIVICARE_MINIMUM_WP_VERSION, '<' ) || version_compare( phpversion(), KIVICARE_MINIMUM_PHP_VERSION, '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}
function kivicare_add_custom_css() {
    wp_enqueue_style(
        'kivicare-custom-style',
        get_template_directory_uri() . '/custom-style.css',
        array(),
        filemtime( get_template_directory() . '/custom-style.css' )
    );
}
add_action( 'wp_enqueue_scripts', 'kivicare_add_custom_css' );

// Include WordPress shims.
require get_template_directory() . '/inc/wordpress-shims.php';
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';
require_once get_template_directory() . '/inc/import.php';

// Setup autoloader (via Composer or custom).
if ( file_exists( get_template_directory() . '/vendor/autoload.php' ) ) {
	require get_template_directory() . '/vendor/autoload.php';
} else {
	/**
	 * Custom autoloader function for theme classes.
	 *
	 * @access private
	 *
	 * @param string $class_name Class name to load.
	 * @return bool True if the class was loaded, false otherwise.
	 */
	function kivicare_autoload( $class_name ) {
		$namespace = 'Kivicare\Kivicare';

		if ( strpos( $class_name, $namespace . '\\' ) !== 0 ) {
			return false;
		}

		$parts = explode( '\\', substr( $class_name, strlen( $namespace . '\\' ) ) );

		$path = get_template_directory() . '/inc';
		foreach ( $parts as $part ) {
			$path .= '/' . $part;
		}
		$path .= '.php';

		if ( ! file_exists( $path ) ) {
			return false;
		}

		require_once $path;

		return true;
	}
	spl_autoload_register( 'kivicare_autoload' );
}

function kivicare_register_top_bar_widgets() {
    register_sidebar( array(
        'name'          => 'Top Bar Left',
        'id'            => 'top-bar-left',
        'before_widget' => '<div class="widget-content">',
        'after_widget'  => '</div>',
    ) );
    register_sidebar( array(
        'name'          => 'Top Bar center',
        'id'            => 'top-bar-center',
        'before_widget' => '<div class="widget-content">',
        'after_widget'  => '</div>',
    ) );
    register_sidebar( array(
        'name'          => 'Top Bar Right',
        'id'            => 'top-bar-right',
        'before_widget' => '<div class="widget-content">',
        'after_widget'  => '</div>',
    ) );
}
add_action( 'widgets_init', 'kivicare_register_top_bar_widgets' );

// Load the `kivicare()` entry point function.
require get_template_directory() . '/inc/functions.php';
// Initialize the theme.
call_user_func( 'Kivicare\Kivicare\kivicare' );
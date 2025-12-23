<?php
/**
 * Plugin Name: Phantom AI Example Plugin
 * Plugin URI: https://demewebsolutions.com/phantom-ai
 * Description: Example plugin created with Phantom.ai workflow automation
 * Version: 1.0.0
 * Author: Deme Web Solutions
 * Author URI: https://demewebsolutions.com
 * License: Proprietary
 * Text Domain: phantom-ai
 * Domain Path: /languages
 *
 * @package PhantomAI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom blocks
 */
function phantom_ai_register_blocks() {
	// Register block types here
	// Example:
	// register_block_type( __DIR__ . '/blocks/example-block' );
}
add_action( 'init', 'phantom_ai_register_blocks' );

/**
 * Enqueue block editor assets
 */
function phantom_ai_enqueue_block_editor_assets() {
	wp_enqueue_style(
		'phantom-ai-editor-styles',
		plugins_url( 'build/editor.css', __FILE__ ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'build/editor.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'phantom_ai_enqueue_block_editor_assets' );

/**
 * Enqueue frontend assets
 */
function phantom_ai_enqueue_assets() {
	wp_enqueue_style(
		'phantom-ai-styles',
		plugins_url( 'build/style.css', __FILE__ ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'build/style.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'phantom_ai_enqueue_assets' );

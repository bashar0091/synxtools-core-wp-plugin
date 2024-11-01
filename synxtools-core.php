<?php

/**
 * Plugin Name: Synxtools Core
 * Description: 
 * Version:     1.0.0
 * Author:      Atiq
 * Author URI:  
 * Text Domain: synxtools-core
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access to the plugin file
defined('ABSPATH') || exit;

/**
 * Require files
 */
require_once plugin_dir_path(__FILE__) . 'includes/helper-function.php';
require_once plugin_dir_path(__FILE__) . 'shortcode/register-form.php';

/**
 * CSS and JS added
 */
function syntools_enqueue_scripts()
{
    // CSS file 
    wp_enqueue_style('customize-style', plugin_dir_url(__FILE__) . 'assets/css/customize.css', false, '1.0.0', '');
}
add_action('wp_enqueue_scripts', 'syntools_enqueue_scripts');

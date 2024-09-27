<?php
/**
 * Plugin Name: 🪄 Webpath Custom Admin
 * Plugin URI: https://webpath.ro
 * Description: Webpath Custom Admin
 * Version: 0.1.0
 * Author: Sebastian Patraș
 * Author URI: https://webpath.ro
 * License: GPL2
 */

 require 'plugin-update-checker/plugin-update-checker.php';

 $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/webpath-ro/webpath-custom-admin/', // GitHub repository URL
    __FILE__, // Full path to the main plugin file
    'webpath-custom-admin' // Plugin slug, must match the plugin folder name
);

// Optional: Set the branch to check for updates (default is 'master')
$myUpdateChecker->setBranch('main');

 $plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );
 define( 'CUSTOM_PLUGIN_VERSION', $plugin_data['Version'] );

 function custom_admin_font_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">';
}

// Hook the function to the admin head action
add_action( 'admin_head', 'custom_admin_font_preconnect' );

 // Enqueue CSS for the admin dashboard only
function custom_plugin_enqueue_admin_styles() {
    wp_enqueue_style(
        'flaticons',
        plugin_dir_url( __FILE__ ) . 'uicons-regular-rounded/css/uicons-regular-rounded.css',
        array(),
        CUSTOM_PLUGIN_VERSION,
        'all'
    );
    wp_enqueue_style(
        'webpath-custom-admin-css',
        plugin_dir_url( __FILE__ ) . 'webpath-custom-admin.css',
        array(),
        CUSTOM_PLUGIN_VERSION,
        'all'
    );

    function custom_plugin_add_google_fonts() {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">' . "\n";
    }
    add_action( 'wp_head', 'custom_plugin_add_google_fonts' );
}
add_action( 'admin_enqueue_scripts', 'custom_plugin_enqueue_admin_styles' );

// Function to enqueue the custom JavaScript file in the WordPress admin
function custom_admin_enqueue_scripts() {
    wp_enqueue_script(
        'webpath-custom-admin-script',
        plugin_dir_url( __FILE__ ) . 'webpath-custom-admin.js', // Path to the JavaScript file
        array( 'jquery' ), // Dependencies
        CUSTOM_PLUGIN_VERSION,
        true // Load in the footer
    );
}
add_action( 'admin_enqueue_scripts', 'custom_admin_enqueue_scripts' );



function custom_admin_footer_text( $default_text ) {
    return 'Webpath CMS v'.CUSTOM_PLUGIN_VERSION;
}
add_filter( 'admin_footer_text', 'custom_admin_footer_text' );


// Function to remove all dashboard widgets
function custom_remove_all_dashboard_widgets() {
    global $wp_meta_boxes;

    // Remove all dashboard widgets
    $wp_meta_boxes['dashboard'] = array();
}
add_action( 'wp_dashboard_setup', 'custom_remove_all_dashboard_widgets', 999 );

// Function to remove the welcome panel
function custom_remove_welcome_panel() {
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
}
add_action( 'admin_head', 'custom_remove_welcome_panel' );

// Function to hide the dashboard-widgets-wrap and welcome panel with CSS
// function custom_remove_dashboard_widgets_wrap() {
//     echo '<style>
//         #dashboard-widgets-wrap, #welcome-panel {
//             display: none !important;
//         }
//     </style>';
// }
// add_action( 'admin_head', 'custom_remove_dashboard_widgets_wrap' );



add_action('admin_menu', 'webpath_admin_menu');

function webpath_admin_menu() {
    global $menu;
    $menu[0] = array( "Webpath", 'read', '/wp-admin', '', 'cms-logo');
    $menu[1] = array( "Către website", 'read', "/", '', 'menu-top website');
    $menu[2] = array( '', 'read', 'separator', '', 'wp-menu-separator' );
}

add_filter( 'show_admin_bar', '__return_false' );


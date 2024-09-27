<?php
/**
 * Plugin Name: 🪄 Webpath Custom Admin
 * Plugin URI: https://webpath.ro
 * Description: Webpath Custom Admin
 * Version: 1.1.0
 * Author: Webpath
 * Author URI: https://webpath.ro
 * License: GPL2
 */


require plugin_dir_path( __FILE__ ) . 'lib/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/webpath-ro/webpath-custom-admin/', // GitHub repository URL
    __FILE__, // Full path to the main plugin file
    'webpath-custom-admin' // Plugin slug, must match the plugin folder name
);

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
        plugin_dir_url( __FILE__ ) . 'lib/uicons-regular-rounded/css/uicons-regular-rounded.css',
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

function custom_admin_enqueue_scripts() {
    // Enqueue the JavaScript file
    wp_enqueue_script(
        'webpath-custom-admin-script',
        plugin_dir_url( __FILE__ ) . 'webpath-custom-admin.js', // Path to the JavaScript file
        array( 'jquery' ), // Dependencies
        CUSTOM_PLUGIN_VERSION, // Replace with your plugin version
        true // Load in the footer
    );

    // Get the current user's display name
    $current_user = wp_get_current_user();
    $username = $current_user->display_name;

    // Get the website domain (use get_home_url() or site_url())
    $website_domain = get_home_url(); // This will return the full URL (e.g., https://example.com)

    // Function to clean the domain (remove https, www, and .com or other extensions)
    function get_clean_domain( $url ) {
        // Parse the URL to extract the host (domain)
        $parsed_url = parse_url( $url, PHP_URL_HOST );

        // If the URL contains 'www.', remove it
        $clean_domain = preg_replace( '/^www\./', '', $parsed_url );

        // Remove the domain extension (e.g., .com, .net, .org)
        $clean_domain = preg_replace( '/\.[a-z]{2,6}$/', '', $clean_domain );

        return $clean_domain;
    }

    // Get the cleaned domain
    $cleaned_domain = get_clean_domain( $website_domain );

    // Add inline script to pass the username and cleaned website domain
    $inline_script = "var dashboardData = {
        username: '" . esc_js( $username ) . "',
        website: '" . esc_js( $cleaned_domain ) . "'
    };";
    
    wp_add_inline_script( 'webpath-custom-admin-script', $inline_script, 'before' );
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


add_action('admin_menu', 'webpath_admin_menu');

function webpath_admin_menu() {
    global $menu;
    $custom_menu_items = array(
        array( "Webpath", 'read', '/wp-admin', '', 'cms-logo' ),
        array( "Către website", 'read', "/", '', 'menu-top website' ),
        array( '', 'read', 'separator', '', 'wp-menu-separator' )
    );

    // Push the items at the beginning of the $menu array
    foreach ( array_reverse( $custom_menu_items ) as $item ) {
        array_unshift( $menu, $item );
    }
}

add_filter( 'show_admin_bar', '__return_false' );


function webpath_add_custom_role() {
    // Add the custom role with specific capabilities
    add_role(
        'webpath_client', // Internal role name (slug)
        'Webpath Client', // Display name for the role
        array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'publish_posts' => true,
            'upload_files' => true,
            'edit_pages' => true,
            'manage_options' => false,
        )
    );
    $role = get_role( 'webpath_client' );

    if ( $role ) {
        // Add WooCommerce capabilities
        $role->add_cap( 'manage_woocommerce' );
        $role->add_cap( 'view_woocommerce_reports' );
        $role->add_cap( 'edit_products' );
        $role->add_cap( 'publish_products' );
        $role->add_cap( 'read_shop_order' );
        $role->add_cap( 'edit_shop_orders' );
        $role->add_cap( 'manage_woocommerce_settings' );
    }
}
add_action( 'init', 'webpath_add_custom_role' );
// register_activation_hook( __FILE__, 'webpath_add_custom_role' );

// remove_role( 'webpath_client' );
// function webpath_remove_custom_role_on_deactivation() {
//     remove_role( 'webpath_client' );
// }
// register_deactivation_hook( __FILE__, 'webpath_remove_custom_role_on_deactivation' );

function custom_remove_menus_for_webpath_client() {
    // Check if the current user has the 'webpath_client' role
    if ( current_user_can( 'webpath_client' ) ) {
        // Remove Tools menu
        remove_menu_page( 'tools.php' );

        // Remove Kadence menu (replace with the correct slug if needed)
        remove_menu_page( 'kadence-blocks' );
    }
}
add_action( 'admin_menu', 'custom_remove_menus_for_webpath_client', 999 );
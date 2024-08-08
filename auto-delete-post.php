<?php
/**
 *
 * @package AutoDeletePost
 *
 * Plugin Name:       Auto Delete Post
 * Plugin URI:        https://wordpress.org/plugin/auto-delete-post
 * Description:       This plugin automatically deletes a post after a certain time
 * Version:           1.1.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Shahadat Hossain
 * Author URI:        https://www.linkedin.com/in/palash-dev/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       auto-delete-post
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // EXIT IF ACCESSED DIRECTLY

/**
 * All constants
 */
// Constants for version
$adp_version = '1.1.2';
define( 'ADP_VERSION', $adp_version );

// Constants for css file path
define( 'ADP_CSS', plugin_dir_url( __FILE__ ) . 'assets/css/' );
define( 'ADP_JS', plugin_dir_url( __FILE__ ) . 'assets/js/' );

// Include necessary files
require_once plugin_dir_path( __FILE__ ) . 'includes/class-auto-delete-option-selection.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-auto-delete-post.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-post-column.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-delete-post-meta-on-restore.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-quick-edit-option.php';
require_once plugin_dir_path( __FILE__ ) . 'assetsManager/assets-manager.php';

// Initialize classes
new ADP_Auto_Delete_Option_Selection();
new ADP_Auto_Delete_Post();
new ADP_Custom_Post_Column();
new Delete_Post_Meta_On_Post_Restore();
new Assets_Manager();
new ADP_Quick_Edit();

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_auto_delete_post() {

    if ( ! class_exists( 'Appsero\Client' ) ) {
        require_once __DIR__ . '/appsero/src/Client.php';
    }

    $client = new Appsero\Client( '777556b2-6bfc-4558-a1ef-08a41e10ee58', 'Auto Delete Post &#8211; Ultimate plugin for deleting a post automatically', __FILE__ );

    // Active insights
    $client->insights()->init();

}
appsero_init_tracker_auto_delete_post();
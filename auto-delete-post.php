<?php 
/**
 * Plugin Name:       Auto Delete Post
 * Plugin URI:        https://wordpress.org/plugin/auto-delete-post
 * Description:       This plugin automatically deletes a post after a certain time
 * Version:           1.1.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Shahadat Hossain
 * Author URI:        https://www.linkedin.com/in/palash-dev/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       auto-delete-post
 * Domain Path:       /languages
 */

/**
 * All constants
 */
// Constants for version
$adp_version = '1.1.1';
define( 'ADP_VERSION', $adp_version );

// Constants for css file path
define( 'ADP_CSS', plugin_dir_url( __FILE__ ) . 'assets/css' );

/**
 * All script file inclusion
 */
function adp_all_scripts_inclusion() {
    wp_enqueue_style( 'style', ADP_CSS . '/style.css', [], ADP_VERSION, 'all' );
}
add_action( 'admin_enqueue_scripts', 'adp_all_scripts_inclusion' );

/**
 * Custom meta box to auto delete a post
 */
class ADP_Auto_Delete_Post {
    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'adp_create_auto_delete_post_meta_box' ] );
        add_action( 'save_post', [ $this, 'adp_save_auto_delete_post_meta_box' ] );
        add_action( 'init', [ $this, 'delete' ] );
    }

    // creating custom meta box
    public function adp_create_auto_delete_post_meta_box() {
        add_meta_box(
            'meta_box_for_auto_post_delete', // meta box id
            '<p class="adp-meta-box-title">Delete Post Automatically: </p>',
            [ $this, 'adp_html_output_for_auto_delete_meta_box' ], // callback function name for html output
            'post'
        );
    }

    // callback function called in the add_meta_box function
    public function adp_html_output_for_auto_delete_meta_box( $post ) {
            if( ! empty( $_GET['post'] ) ) {
                $current_post_id = sanitize_text_field( $_GET['post'] );
                $meta_date_time_value = get_post_meta( $current_post_id, 'auto_delete_post_time_key', true );
            }
        ?>
            <label for="adp-time"><?php echo esc_html__( 'Select Time', 'auto-delete-post' ); ?></label>
            <input class="adp-input" type="datetime-local" name="adp-time" id="adp-time" value="<?php if( !empty( $meta_date_time_value ) ) { echo esc_attr( $meta_date_time_value ); } ?>" />
        <?php 
    }

    // function for saving the value of auto delete meta box value
    public function adp_save_auto_delete_post_meta_box( $post_id ) {
        if( ! empty( $_POST['adp-time'] ) ) {
            $post_time = sanitize_text_field( $_POST['adp-time'] );
            update_post_meta( $post_id, 'auto_delete_post_time_key', $post_time );
        }
    }

    // auto post deletion mechanism
    public function delete() {
        $adp_custom_query = new WP_Query( array(
            'post_type' => 'post',
            'posts_per_page' => -1,
        ));
        if( $adp_custom_query->have_posts() ) {
            while( $adp_custom_query->have_posts() ) {
                $adp_custom_query->the_post();
                
                $unique_post_id = get_the_ID();
                $final_meta_value = get_post_meta( $unique_post_id, 'auto_delete_post_time_key', true );
                $converted_user_date_time = strtotime( $final_meta_value );
                $converted_in_date_format = date('Y-m-d H:i', $converted_user_date_time).' ';
                $final_user_date_time = strtotime( $converted_in_date_format );
                $current_server_time = current_time('timestamp');
                if( $current_server_time >= $final_user_date_time && $final_user_date_time > 0 ) {
                        wp_delete_post( $unique_post_id );
                }
            }
        }
        
        wp_reset_postdata();
    }
}

$obj = new ADP_Auto_Delete_Post(); // class initialization




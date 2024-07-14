<?php 
/**
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

// quick_edit_custom_box allows to add HTML in Quick Edit
add_action( 'quick_edit_custom_box',  'misha_quick_edit_fields', 10, 2 );

function misha_quick_edit_fields( $column_name, $post_type ) {
    $product_price = get_post_meta( get_the_ID(), 'product_price', true );
    ?>
         <fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
                <label>
                    <span class="title">Price</span>
                    <input type="text" name="price" value="<?php echo $product_price; ?>">
                </label>
            </div>
         </fieldset>
     <?php
}

add_action( 'save_post', 'misha_quick_edit_save' );

function misha_quick_edit_save( $post_id ){
    // check inlint edit nonce
    if ( ! wp_verify_nonce( $_POST[ '_inline_edit' ], 'inlineeditnonce' ) ) {
        return;
    }

    // update the price
    $price = ! empty( $_POST[ 'price' ] ) ? absint( $_POST[ 'price' ] ) : 0;
    update_post_meta( $post_id, 'product_price', $price );
}

/**
 * All constants
 */
// Constants for version
$adp_version = '1.1.2';
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

/**
 * Creating options of all post type to choose from, where this functionality will be available
 */
class ADP_Auto_Delete_Option_Selection {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'adding_menu_page_for_all_types' ] );
    }

    function adding_menu_page_for_all_types() {
        // Adding a menu page with list of custom post types which will be selected for any purpose for (auto delete posts plugin)
        add_menu_page(
            esc_html__( 'Auto Delete Posts', 'auto-delete-post' ), // title to show in the page tab section
            esc_html__( 'Auto Delete Posts', 'auto-delete-post' ), // title to show in the page menu bar
            'manage_options', // capability
            'adp-post-list', // slug of the page
            'adp_posts_list_callback', // callback function
            'dashicons-table-col-delete', // icon to show on menu bar
            2 // location of the menu in the dashboard where it should be displayed
        );

        // duplicating the menu page with 'add_submenu_page' function to display a different title other than the page title used in the 'add_menu_page' function
        add_submenu_page( 
            'adp-post-list', // menu slug
            esc_html__( 'All Post Type List', 'auto-delete-post' ), // title to show in the page
            esc_html__( 'All Post Type List', 'auto-delete-post' ), // title to show in the submenu page
            'manage_options', // capability of the user
            'adp-post-list' // mneu slug
        );

        // we will have to reuse the 'add_submenu_page' function to show the first submenu under the menu page
        add_submenu_page( 
            'adp-post-list', // menu slug
            null, // title to show in the page, but in this case we don't want a second submenu. Therefor we 'nullified' it, thus it will not show any title
            null, // title to show in the menu, but in this case we don't want a second submenu. Therefor we 'nullified' it, thus it will not show any title
            'manage_options',  // capability
            'adp-post-list' // menu slug, in this case it is not doing. We are just using it for the sake of function parameters validity
        );

        function adp_posts_list_callback() {
            ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Choose from the options, where you want to add this functionality.', 'auto-delete-post' ); ?></h1>
                <ul>
                    <?php 
                    if( isset( $_POST['submit'] )   ) {
                        if( empty( $_POST['adp-posts-type-lists'] ) ) {
                            $post_types_meta_values = '';
                            update_option( 'auto_delete_post_option', $post_types_meta_values ); 
                            echo '<div class="notice notice-success is-dismissible"><p>';
                            esc_html_e( 'Saved successfully!', 'auto-delete-post' );
                            echo '</p></div>'; // showing the success message after form submission.
                        }
                        else {
                            $post_types_meta_values = $_POST['adp-posts-type-lists'];
                            update_option( 'auto_delete_post_option', $post_types_meta_values );   
                            echo '<div class="notice notice-success is-dismissible"><p>';
                            esc_html_e( 'Saved successfully!', 'auto-delete-post' );
                            echo '</p></div>'; // showing the success message after form submission.
                        }                        
                    }
                    $opt_result = get_option( 'auto_delete_post_option' );

                    $post_types_args = [ 'public' => true ];
                    $post_types = get_post_types( $post_types_args ); // getting all the post types of WP
                        
                    unset( $post_types['attachment'] ); // unsettings the builtin post type attachment from the post types list
                    ?>
                    <form action="<?php esc_url( admin_url( 'options.php' ) ); ?>" method="post">
                    <?php                   
                    foreach( $post_types as $post_type) {                          
                    ?>
                    <li>
                        <input type="checkbox" name="adp-posts-type-lists[]" id="<?php echo esc_attr( $post_type ); ?>" value="<?php echo esc_attr__( $post_type, 'auto-delete-post' ); ?>" 
                        <?php 
                        if( !empty( $opt_result ) ) {
                            if( in_array( $post_type, $opt_result ) ){ echo esc_attr( 'checked' ); }
                        } 
                        ?>>
                        <label for="<?php echo esc_attr( $post_type ); ?>">
                        <?php echo esc_html__( $post_type, 'auto-delete-post' ); ?>
                        </label>
                    </li>
                    <?php 
                    }
                    ?>
                    <?php 
                    submit_button( esc_html__( 'Save', 'auto-delete-post' ), 'primary' );  
                    ?>
                    </form>
                    <?php 
                    ?>
                </ul>
            </div>
            <?php 
        }
    }
}

$adp_all_selected_post_types_obj = new ADP_Auto_Delete_Option_Selection();


/**
 * Custom meta box to auto delete a post
 */
class ADP_Auto_Delete_Post {
    public $post_opt_result;
    
    public function __construct() {
        $this->post_opt_result = get_option( 'auto_delete_post_option' ); // getting the result of all selected post type
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
            $this->post_opt_result
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
            'post_type' => $this->post_opt_result,
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

$adp_obj = new ADP_Auto_Delete_Post(); // class initialization

// Class for creating a custom column on posts list showing the deletion time
class ADP_Custom_Post_Column {
    public $counter;

    public function __construct() {
        add_filter( 'manage_posts_columns', [ $this, 'custom_post_delete_column' ] ); // filter hook to add a new column on the posts list

        add_action( 'manage_posts_custom_column', [ $this, 'custom_post_delete_column_content' ], 10, 2 ); // action hook to add content in our new custom column on the posts list
    }

    // callback of custom column
    public function custom_post_delete_column( $column ) {
        $column['adp_post_deletion_time_column'] = 'Auto Deletion Time';
        return $column;
    }

    // callback of custom column content
    public function custom_post_delete_column_content( $column_name, $post_id ) {
        $this->counter = get_post_meta( $post_id, 'auto_delete_post_time_key', true );
        $converted_user_date_time = strtotime( $this->counter );

        if ( empty( $converted_user_date_time ) ) {
            $converted_in_date_format = 'Time not set';
        } else {
            $converted_in_date_format = date( 'Y-m-d h:i A', $converted_user_date_time ) . ' ';
        }    

        // $final_user_date_time = strtotime( $converted_in_date_format );
        if ( 'adp_post_deletion_time_column' == $column_name ) {
            // Display the deletion time
            printf( esc_html__( '%s', 'auto-delete-post' ), esc_html( $converted_in_date_format ) );
        }
    }
}

$adp_custom_post_columb_obj = new ADP_Custom_Post_Column(); // class initialization

class Delete_Post_Meta_On_Post_Restore {
    public function __construct() {
        add_action( 'untrashed_post', [ $this, 'delete_auto_delte_post_meta' ] );
    }

    // Deleting post meta on clicking restore for every post
    public function delete_auto_delte_post_meta( $post_id ) {
        // Specifying the meta key
        $meta_key_to_delete = 'auto_delete_post_time_key';

        // Delete the post meta
        delete_post_meta( $post_id, $meta_key_to_delete );
    }
}

$adp_delete_post_meta_on_restore = new Delete_Post_Meta_On_Post_Restore(); // class initialization




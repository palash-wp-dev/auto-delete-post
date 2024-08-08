<?php
if ( ! defined( 'ABSPATH' ) ) exit; // EXIT IF ACCESSED DIRECTLY

/**
 * Creating options of all post type to choose from, where this functionality will be available
 */
class ADP_Auto_Delete_Option_Selection {

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method __construct
     * @return void
     * @since 1.0.0
     * Registers all hooks that are needed
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'adding_menu_page_for_all_types' ] );
    }

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method adding_menu_page_for_all_types
     * @return void
     * @since 1.0.0
     * Adding a menu page called 'Auto Delete Post' in the sidebar area
     */
    public function adding_menu_page_for_all_types() {
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
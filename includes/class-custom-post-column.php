<?php
if ( ! defined( 'ABSPATH' ) ) exit; // EXIT IF ACCESSED DIRECTLY

/**
 * Class for creating a custom column on posts list showing the deletion time
 */
class ADP_Custom_Post_Column {
    public $counter;

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method __construct
     * @return void
     * @since 1.0.0
     * Registers all hooks that are needed
     */
    public function __construct() {
        // filter hook to add a new column on the posts list
        add_filter( 'manage_posts_columns', [ $this, 'custom_post_delete_column' ] );
        // action hook to add content in our new custom column on the posts list
        add_action( 'manage_posts_custom_column', [ $this, 'custom_post_delete_column_content' ], 10, 2 );
    }

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method custom_post_delete_column
     * @return void
     * @since 1.0.0
     * Callback of custom column
     */
    public function custom_post_delete_column( $column ) {
        $column['adp_post_deletion_time_column'] = 'Auto Deletion Time';
        return $column;
    }

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method custom_post_delete_column_content
     * @return void
     * @since 1.0.0
     * Callback of custom column content
     */
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
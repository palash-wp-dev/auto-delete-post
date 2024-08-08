<?php
if ( ! defined( 'ABSPATH' ) ) exit; // EXIT IF ACCESSED DIRECTLY
class Delete_Post_Meta_On_Post_Restore {

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method __construct
     * @return void
     * @since 1.0.0
     * Registers all hooks that are needed
     */
    public function __construct() {
        add_action( 'untrashed_post', [ $this, 'delete_auto_delte_post_meta' ] );
    }

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method delete_auto_delte_post_meta
     * @return void
     * @since 1.0.0
     * Deleting post meta on clicking restore for every post
     */
    public function delete_auto_delte_post_meta( $post_id ) {
        // Specifying the meta key
        $meta_key_to_delete = 'auto_delete_post_time_key';

        // Delete the post meta
        delete_post_meta( $post_id, $meta_key_to_delete );
    }
}
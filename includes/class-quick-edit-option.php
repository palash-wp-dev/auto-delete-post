<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // EXIT IF ACCESSED DIRECTLY

class ADP_Quick_Edit {

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method __construct
     * @return void
     * @since 1.0.0
     * Registers all hooks that are needed
     */
	public function __construct() {
		// quick_edit_custom_box allows to add HTML in Quick Edit
    	add_action( 'quick_edit_custom_box', [ $this, 'adp_quick_edit_fields' ], 10, 2 );
    	add_action( 'save_post', [ $this, 'adp_quick_edit_save' ] );
	}

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method adp_quick_edit_fields
     * @return void
     * @since 1.0.0
     * Adding an input field for selecting date and time
     */
	public function adp_quick_edit_fields( $column_name, $post_type ) {
        if ( $column_name == 'adp_post_deletion_time_column' ) {
            $delete_time = get_post_meta( get_the_ID(), 'auto_delete_post_time_key', true );
            ?>
            <fieldset class="inline-edit-col-left">
                <div class="inline-edit-col">
                    <label>
                        <span class="title">Auto Delete Time</span>
                        <input type="datetime-local" id="adp-time" name="adp-time" value="<?php echo esc_attr( $delete_time ); ?>">
                    </label>
                </div>
            </fieldset>
            <?php
        }
    }

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method adp_quick_edit_save
     * @return void
     * @since 1.0.0
     * Saving the value of quick edit date and time option
     */
    public function adp_quick_edit_save( $post_id ) {
        // Check inline edit nonce
        if ( ! isset( $_POST['_inline_edit'] ) || ! wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce' ) ) {
            return;
        }

        // Update the auto delete time
        if ( isset( $_POST['adp-time'] ) ) {
            $auto_delete_time = sanitize_text_field( $_POST['adp-time'] );
            update_post_meta( $post_id, 'auto_delete_post_time_key', $auto_delete_time );
        }
    }
}
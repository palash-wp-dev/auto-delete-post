<?php
if ( ! defined( 'ABSPATH' ) ) exit; // EXIT IF ACCESSED DIRECTLY

class Assets_Manager {

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method __construct
     * @return void
     * @since 1.0.0
     * Registers all hooks that are needed
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'adp_all_scripts_inclusion' ] );
    }

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method adp_assets_prefix
     * @return string
     * @since 1.0.0
     * Registers all hooks that are needed
     */
    public function adp_assets_prefix( $handle ) {
        return 'adp-' . $handle;
    }

    /**
     * @package AutoDeletePost
     * @author Shahadat Hossain
     * @method adp_all_scripts_inclusion
     * @return void
     * @since 1.0.0
     * Including all scripts
     */
    public function adp_all_scripts_inclusion() {
        wp_enqueue_style( $this->adp_assets_prefix( 'style' ), ADP_CSS . 'style.css', [], ADP_VERSION, 'all' );
    }
}
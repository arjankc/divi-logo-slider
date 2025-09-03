<?php

if ( ! class_exists( 'LogoSliderAdmin' ) ) {

class LogoSliderAdmin {

    public function __construct() {
        add_action( 'init', array( $this, 'register_logo_post_type' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 9 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_ajax_lsfd_save_logo', array( $this, 'ajax_save_logo' ) );
        add_action( 'wp_ajax_lsfd_update_logo', array( $this, 'ajax_update_logo' ) );
        add_action( 'wp_ajax_lsfd_delete_logo', array( $this, 'ajax_delete_logo' ) );
        add_action( 'wp_ajax_lsfd_get_logos', array( $this, 'ajax_get_logos' ) );
        add_action( 'wp_ajax_lsfd_reorder_logos', array( $this, 'ajax_reorder_logos' ) );
        
        // Debug: Add an admin notice to confirm the class is loaded
        add_action( 'admin_notices', array( $this, 'debug_admin_notice' ) );
    }

    /**
     * Debug admin notice (remove this after confirming it works)
     */
    public function debug_admin_notice() {
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'logo-slider-admin' ) {
            return; // Don't show on our own page
        }
        echo '<div class="notice notice-info is-dismissible"><p>Logo Slider Admin class loaded successfully!</p></div>';
    }

    /**
     * Register custom post type for logos
     */
    public function register_logo_post_type() {
        $args = array(
            'label'               => esc_html__( 'Logos', 'logo-slider-for-divi' ),
            'public'              => false,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'rewrite'             => false,
            'query_var'           => false,
            'supports'            => array( 'title', 'thumbnail', 'custom-fields', 'page-attributes' ),
        );
        register_post_type( 'lsfd_logo', $args );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Try both approaches to ensure menu appears
        add_menu_page(
            esc_html__( 'Logo Slider', 'logo-slider-for-divi' ),
            esc_html__( 'Logo Slider', 'logo-slider-for-divi' ),
            'manage_options',
            'logo-slider-admin',
            array( $this, 'admin_page' ),
            'dashicons-images-alt2',
            30
        );
        
        // Also add under Settings as backup
        add_options_page(
            esc_html__( 'Logo Slider Settings', 'logo-slider-for-divi' ),
            esc_html__( 'Logo Slider', 'logo-slider-for-divi' ),
            'manage_options',
            'logo-slider-settings',
            array( $this, 'admin_page' )
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        if ( 'toplevel_page_logo-slider-admin' !== $hook && 'settings_page_logo-slider-settings' !== $hook ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_style( 'lsfd-admin-styles', plugins_url( '../css/admin-styles.css', __FILE__ ), array(), '1.0.0' );
        wp_enqueue_script( 'lsfd-admin-scripts', plugins_url( '../js/admin-scripts.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ), '1.0.0', true );
        
        wp_localize_script( 'lsfd-admin-scripts', 'lsfd_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'lsfd_nonce' ),
        ) );
    }

    /**
     * Admin page content
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Logo Slider Management', 'logo-slider-for-divi' ); ?></h1>
            
            <div class="lsfd-admin-container">
                <div class="lsfd-add-logo-section">
                    <h2><?php esc_html_e( 'Add New Logo', 'logo-slider-for-divi' ); ?></h2>
                    <form id="lsfd-add-logo-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="logo-title"><?php esc_html_e( 'Logo Title', 'logo-slider-for-divi' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="logo-title" name="logo_title" class="regular-text" required />
                                    <p class="description"><?php esc_html_e( 'Enter a title for this logo (for internal reference)', 'logo-slider-for-divi' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="logo-image"><?php esc_html_e( 'Logo Image', 'logo-slider-for-divi' ); ?></label>
                                </th>
                                <td>
                                    <input type="hidden" id="logo-image" name="logo_image" />
                                    <button type="button" id="upload-logo-btn" class="button"><?php esc_html_e( 'Upload Logo', 'logo-slider-for-divi' ); ?></button>
                                    <div id="logo-preview" style="margin-top: 10px;"></div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="logo-url"><?php esc_html_e( 'Logo URL', 'logo-slider-for-divi' ); ?></label>
                                </th>
                                <td>
                                    <input type="url" id="logo-url" name="logo_url" class="regular-text" />
                                    <p class="description"><?php esc_html_e( 'Optional: Enter the URL this logo should link to', 'logo-slider-for-divi' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="logo-alt"><?php esc_html_e( 'Alt Text', 'logo-slider-for-divi' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="logo-alt" name="logo_alt" class="regular-text" />
                                    <p class="description"><?php esc_html_e( 'Enter alt text for accessibility', 'logo-slider-for-divi' ); ?></p>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <button type="submit" class="button button-primary"><?php esc_html_e( 'Add Logo', 'logo-slider-for-divi' ); ?></button>
                        </p>
                    </form>
                </div>

                <div class="lsfd-logos-list-section">
                    <h2><?php esc_html_e( 'Manage Logos', 'logo-slider-for-divi' ); ?></h2>
                    <div id="logos-list" class="lsfd-logos-grid">
                        <!-- Logos will be loaded here via AJAX -->
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX handler to save logo
     */
    public function ajax_save_logo() {
        check_ajax_referer( 'lsfd_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'logo-slider-for-divi' ) );
        }

        $logo_title = sanitize_text_field( $_POST['logo_title'] );
        $logo_image = sanitize_text_field( $_POST['logo_image'] );
        $logo_url   = esc_url_raw( $_POST['logo_url'] );
        $logo_alt   = sanitize_text_field( $_POST['logo_alt'] );

        $post_data = array(
            'post_title'   => $logo_title,
            'post_type'    => 'lsfd_logo',
            'post_status'  => 'publish',
            'meta_input'   => array(
                'logo_image' => $logo_image,
                'logo_url'   => $logo_url,
                'logo_alt'   => $logo_alt,
            ),
        );

        $post_id = wp_insert_post( $post_data );

        if ( $post_id ) {
            set_post_thumbnail( $post_id, attachment_url_to_postid( $logo_image ) );
            wp_send_json_success( array(
                'message' => esc_html__( 'Logo saved successfully!', 'logo-slider-for-divi' ),
                'logo_id' => $post_id,
            ) );
        } else {
            wp_send_json_error( array(
                'message' => esc_html__( 'Failed to save logo.', 'logo-slider-for-divi' ),
            ) );
        }
    }

    /**
     * AJAX handler to update logo
     */
    public function ajax_update_logo() {
        check_ajax_referer( 'lsfd_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'logo-slider-for-divi' ) );
        }

        $logo_id    = intval( $_POST['logo_id'] );
        $logo_title = sanitize_text_field( $_POST['logo_title'] );
        $logo_image = sanitize_text_field( $_POST['logo_image'] );
        $logo_url   = esc_url_raw( $_POST['logo_url'] );
        $logo_alt   = sanitize_text_field( $_POST['logo_alt'] );

        $post_data = array(
            'ID'         => $logo_id,
            'post_title' => $logo_title,
        );

        $updated = wp_update_post( $post_data );

        if ( $updated ) {
            update_post_meta( $logo_id, 'logo_image', $logo_image );
            update_post_meta( $logo_id, 'logo_url', $logo_url );
            update_post_meta( $logo_id, 'logo_alt', $logo_alt );
            
            set_post_thumbnail( $logo_id, attachment_url_to_postid( $logo_image ) );
            
            wp_send_json_success( array(
                'message' => esc_html__( 'Logo updated successfully!', 'logo-slider-for-divi' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => esc_html__( 'Failed to update logo.', 'logo-slider-for-divi' ),
            ) );
        }
    }

    /**
     * AJAX handler to delete logo
     */
    public function ajax_delete_logo() {
        check_ajax_referer( 'lsfd_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'logo-slider-for-divi' ) );
        }

        $logo_id = intval( $_POST['logo_id'] );
        
        if ( wp_delete_post( $logo_id, true ) ) {
            wp_send_json_success( array(
                'message' => esc_html__( 'Logo deleted successfully!', 'logo-slider-for-divi' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => esc_html__( 'Failed to delete logo.', 'logo-slider-for-divi' ),
            ) );
        }
    }

    /**
     * AJAX handler to get logos
     */
    public function ajax_get_logos() {
        check_ajax_referer( 'lsfd_nonce', 'nonce' );

        $logos = $this->get_all_logos();
        wp_send_json_success( $logos );
    }

    /**
     * AJAX handler to reorder logos
     */
    public function ajax_reorder_logos() {
        check_ajax_referer( 'lsfd_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'logo-slider-for-divi' ) );
        }

        $logo_order = $_POST['logo_order'];
        
        foreach ( $logo_order as $index => $logo_id ) {
            wp_update_post( array(
                'ID'         => intval( $logo_id ),
                'menu_order' => $index,
            ) );
        }

        wp_send_json_success( array(
            'message' => esc_html__( 'Logo order updated successfully!', 'logo-slider-for-divi' ),
        ) );
    }

    /**
     * Get all logos from database
     */
    public function get_all_logos() {
        $logos = get_posts( array(
            'post_type'      => 'lsfd_logo',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ) );

        $logo_data = array();
        foreach ( $logos as $logo ) {
            $logo_data[] = array(
                'id'    => $logo->ID,
                'title' => $logo->post_title,
                'image' => get_post_meta( $logo->ID, 'logo_image', true ),
                'url'   => get_post_meta( $logo->ID, 'logo_url', true ),
                'alt'   => get_post_meta( $logo->ID, 'logo_alt', true ),
            );
        }

        return $logo_data;
    }
}

}

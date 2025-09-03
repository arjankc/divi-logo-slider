<?php
/**
 * Admin functionality for Logo Slider
 */

if (!defined('ABSPATH')) {
    exit;
}

class LSFD_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_lsfd_save_logo', array($this, 'ajax_save_logo'));
        add_action('wp_ajax_lsfd_update_logo', array($this, 'ajax_update_logo'));
        add_action('wp_ajax_lsfd_delete_logo', array($this, 'ajax_delete_logo'));
        add_action('wp_ajax_lsfd_get_logos', array($this, 'ajax_get_logos'));
        add_action('wp_ajax_lsfd_reorder_logos', array($this, 'ajax_reorder_logos'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Logo Slider', 'logo-slider-for-divi'),
            __('Logo Slider', 'logo-slider-for-divi'),
            'manage_options',
            'logo-slider',
            array($this, 'admin_page'),
            'dashicons-images-alt2',
            30
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_logo-slider' !== $hook) {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
        
        wp_enqueue_style(
            'lsfd-admin-style',
            LSFD_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            LSFD_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'lsfd-admin-script',
            LSFD_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable', 'wp-media'),
            LSFD_PLUGIN_VERSION,
            true
        );
        
        wp_localize_script('lsfd-admin-script', 'lsfd_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lsfd_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this logo?', 'logo-slider-for-divi'),
                'uploading' => __('Uploading...', 'logo-slider-for-divi'),
                'saving' => __('Saving...', 'logo-slider-for-divi'),
                'updating' => __('Updating...', 'logo-slider-for-divi'),
            )
        ));
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Logo Slider Management', 'logo-slider-for-divi'); ?></h1>
            
            <div class="lsfd-admin-container">
                <!-- Add Logo Form -->
                <div class="lsfd-form-section">
                    <h2 id="form-title"><?php _e('Add New Logo', 'logo-slider-for-divi'); ?></h2>
                    
                    <form id="lsfd-logo-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="logo_title"><?php _e('Logo Title', 'logo-slider-for-divi'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="logo_title" name="logo_title" class="regular-text" required />
                                    <p class="description"><?php _e('Enter a title for this logo (for your reference)', 'logo-slider-for-divi'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Logo Image', 'logo-slider-for-divi'); ?></label>
                                </th>
                                <td>
                                    <div class="lsfd-image-upload">
                                        <input type="hidden" id="logo_image" name="logo_image" />
                                        <div id="image-preview" class="lsfd-image-preview">
                                            <div class="lsfd-placeholder">
                                                <div class="dashicons dashicons-format-image"></div>
                                                <p><?php _e('No image selected', 'logo-slider-for-divi'); ?></p>
                                            </div>
                                        </div>
                                        <div class="lsfd-image-buttons">
                                            <button type="button" id="select-image-btn" class="button"><?php _e('Select Image', 'logo-slider-for-divi'); ?></button>
                                            <button type="button" id="remove-image-btn" class="button" style="display:none;"><?php _e('Remove Image', 'logo-slider-for-divi'); ?></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="logo_url"><?php _e('Logo URL', 'logo-slider-for-divi'); ?></label>
                                </th>
                                <td>
                                    <input type="url" id="logo_url" name="logo_url" class="regular-text" />
                                    <p class="description"><?php _e('Optional: Enter the URL this logo should link to', 'logo-slider-for-divi'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="logo_alt"><?php _e('Alt Text', 'logo-slider-for-divi'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="logo_alt" name="logo_alt" class="regular-text" />
                                    <p class="description"><?php _e('Enter alt text for accessibility', 'logo-slider-for-divi'); ?></p>
                                </td>
                            </tr>
                        </table>
                        
                        <div class="lsfd-form-actions">
                            <input type="hidden" id="logo_id" name="logo_id" value="" />
                            <input type="hidden" id="form_action" name="form_action" value="add" />
                            <button type="submit" class="button button-primary" id="submit-btn"><?php _e('Add Logo', 'logo-slider-for-divi'); ?></button>
                            <button type="button" class="button" id="cancel-btn" style="display:none;"><?php _e('Cancel', 'logo-slider-for-divi'); ?></button>
                        </div>
                    </form>
                </div>
                
                <!-- Logos List -->
                <div class="lsfd-logos-section">
                    <h2><?php _e('Manage Logos', 'logo-slider-for-divi'); ?></h2>
                    <div id="logos-container">
                        <!-- Logos will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function ajax_save_logo() {
        check_ajax_referer('lsfd_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions.', 'logo-slider-for-divi'));
        }
        
        $title = sanitize_text_field($_POST['logo_title']);
        $image = esc_url_raw($_POST['logo_image']);
        $url = esc_url_raw($_POST['logo_url']);
        $alt = sanitize_text_field($_POST['logo_alt']);
        
        if (empty($title) || empty($image)) {
            wp_send_json_error(array('message' => __('Title and image are required.', 'logo-slider-for-divi')));
        }
        
        // Get the highest menu order
        $logos = get_posts(array(
            'post_type' => 'lsfd_logo',
            'numberposts' => 1,
            'orderby' => 'menu_order',
            'order' => 'DESC',
            'post_status' => 'publish'
        ));
        
        $menu_order = 0;
        if (!empty($logos)) {
            $menu_order = $logos[0]->menu_order + 1;
        }
        
        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_type' => 'lsfd_logo',
            'post_status' => 'publish',
            'menu_order' => $menu_order,
            'meta_input' => array(
                'logo_image' => $image,
                'logo_url' => $url,
                'logo_alt' => $alt,
            ),
        ));
        
        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => __('Failed to save logo.', 'logo-slider-for-divi')));
        }
        
        // Set featured image if possible
        $attachment_id = attachment_url_to_postid($image);
        if ($attachment_id) {
            set_post_thumbnail($post_id, $attachment_id);
        }
        
        wp_send_json_success(array(
            'message' => __('Logo saved successfully!', 'logo-slider-for-divi'),
            'logo_id' => $post_id
        ));
    }
    
    public function ajax_update_logo() {
        check_ajax_referer('lsfd_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions.', 'logo-slider-for-divi'));
        }
        
        $logo_id = intval($_POST['logo_id']);
        $title = sanitize_text_field($_POST['logo_title']);
        $image = esc_url_raw($_POST['logo_image']);
        $url = esc_url_raw($_POST['logo_url']);
        $alt = sanitize_text_field($_POST['logo_alt']);
        
        if (empty($title) || empty($image)) {
            wp_send_json_error(array('message' => __('Title and image are required.', 'logo-slider-for-divi')));
        }
        
        $result = wp_update_post(array(
            'ID' => $logo_id,
            'post_title' => $title,
        ));
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => __('Failed to update logo.', 'logo-slider-for-divi')));
        }
        
        update_post_meta($logo_id, 'logo_image', $image);
        update_post_meta($logo_id, 'logo_url', $url);
        update_post_meta($logo_id, 'logo_alt', $alt);
        
        // Update featured image
        $attachment_id = attachment_url_to_postid($image);
        if ($attachment_id) {
            set_post_thumbnail($logo_id, $attachment_id);
        }
        
        wp_send_json_success(array('message' => __('Logo updated successfully!', 'logo-slider-for-divi')));
    }
    
    public function ajax_delete_logo() {
        check_ajax_referer('lsfd_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions.', 'logo-slider-for-divi'));
        }
        
        $logo_id = intval($_POST['logo_id']);
        
        if (wp_delete_post($logo_id, true)) {
            wp_send_json_success(array('message' => __('Logo deleted successfully!', 'logo-slider-for-divi')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete logo.', 'logo-slider-for-divi')));
        }
    }
    
    public function ajax_get_logos() {
        check_ajax_referer('lsfd_admin_nonce', 'nonce');
        
        $logos = get_posts(array(
            'post_type' => 'lsfd_logo',
            'numberposts' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_status' => 'publish'
        ));
        
        $logo_data = array();
        foreach ($logos as $logo) {
            $logo_data[] = array(
                'id' => $logo->ID,
                'title' => $logo->post_title,
                'image' => get_post_meta($logo->ID, 'logo_image', true),
                'url' => get_post_meta($logo->ID, 'logo_url', true),
                'alt' => get_post_meta($logo->ID, 'logo_alt', true),
            );
        }
        
        wp_send_json_success($logo_data);
    }
    
    public function ajax_reorder_logos() {
        check_ajax_referer('lsfd_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions.', 'logo-slider-for-divi'));
        }
        
        $logo_order = $_POST['logo_order'];
        
        if (is_array($logo_order)) {
            foreach ($logo_order as $index => $logo_id) {
                wp_update_post(array(
                    'ID' => intval($logo_id),
                    'menu_order' => $index,
                ));
            }
            wp_send_json_success(array('message' => __('Logo order updated!', 'logo-slider-for-divi')));
        } else {
            wp_send_json_error(array('message' => __('Invalid data.', 'logo-slider-for-divi')));
        }
    }
}

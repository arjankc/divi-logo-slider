<?php
/**
 * Plugin Name: Logo Slider for Divi
 * Plugin URI: https://github.com/Gurkha-Technology-Open-Source/divi-logo-slider
 * Description: A Divi module to display a logo slider with centralized admin management.
 * Version: 1.0.0
 * Author: Gurkha Technology
 * Author URI: https://github.com/Gurkha-Technology-Open-Source
 * Text Domain: logo-slider-for-divi
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('LSFD_PLUGIN_VERSION', '1.0.0');
define('LSFD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LSFD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LSFD_PLUGIN_FILE', __FILE__);

/**
 * Main Plugin Class
 */
class LogoSliderForDiviPlugin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Initialize admin functionality
        if (is_admin()) {
            add_action('init', array($this, 'init_admin'));
        }
        
        // Initialize Divi module
        add_action('et_builder_ready', array($this, 'init_divi_module'));
    }
    
    public function init() {
        // Register custom post type for logos
        $this->register_logo_post_type();
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('logo-slider-for-divi', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    public function init_admin() {
        require_once LSFD_PLUGIN_DIR . 'includes/admin/class-admin.php';
        new LSFD_Admin();
    }
    
    public function init_divi_module() {
        if (class_exists('ET_Builder_Module')) {
            require_once LSFD_PLUGIN_DIR . 'includes/modules/class-logo-slider-module.php';
        }
    }
    
    public function register_logo_post_type() {
        register_post_type('lsfd_logo', array(
            'labels' => array(
                'name' => __('Logos', 'logo-slider-for-divi'),
                'singular_name' => __('Logo', 'logo-slider-for-divi'),
            ),
            'public' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title', 'thumbnail', 'custom-fields', 'page-attributes'),
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'rewrite' => false,
        ));
    }
}

// Initialize the plugin
LogoSliderForDiviPlugin::get_instance();
<?php
/**
 * Plugin Name: Logo Slider for Divi
 * Plugin URI: https://github.com/Gurkha-Technology-Open-Source/divi-logo-slider
 * Description: A Divi module to display a logo slider with centralized admin management.
 * Version: 1.0.2
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

// Define plugin constants (guarded to avoid redefinition if multiple copies are present)
if (!defined('LSFD_PLUGIN_VERSION')) {
    define('LSFD_PLUGIN_VERSION', '1.0.2');
}
if (!defined('LSFD_PLUGIN_DIR')) {
    define('LSFD_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('LSFD_PLUGIN_URL')) {
    define('LSFD_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('LSFD_PLUGIN_FILE')) {
    define('LSFD_PLUGIN_FILE', __FILE__);
}

/**
 * Main Plugin Class
 */
if (!class_exists('LogoSliderForDiviPlugin')) {
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

    // Shortcode fallback to render slider when shortcode text appears in content
    add_action('init', array($this, 'register_shortcode'));
    }
    
    public function init() {
        // Register custom post type for logos
        $this->register_logo_post_type();
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('logo-slider-for-divi', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    public function init_admin() {
        $admin_file = LSFD_PLUGIN_DIR . 'includes/admin/class-admin.php';
        if (file_exists($admin_file)) {
            require_once $admin_file;
            if (class_exists('LSFD_Admin')) {
                new LSFD_Admin();
            } else {
                add_action('admin_notices', function() use ($admin_file) {
                    echo '<div class="notice notice-error"><p>Logo Slider for Divi: Admin class not found in ' . esc_html($admin_file) . '.</p></div>';
                });
            }
        } else {
            add_action('admin_notices', function() use ($admin_file) {
                echo '<div class="notice notice-error"><p>Logo Slider for Divi: Missing file ' . esc_html($admin_file) . '. Please reinstall the plugin.</p></div>';
            });
            error_log('[Logo Slider for Divi] Missing admin file: ' . $admin_file);
        }
    }
    
    public function init_divi_module() {
        if (class_exists('ET_Builder_Module')) {
            require_once LSFD_PLUGIN_DIR . 'includes/modules/class-logo-slider-module.php';
        }
    }

    public function register_shortcode() {
        add_shortcode('lsfd_logo_slider', function($atts) {
            $atts = shortcode_atts(array(
                'slides_per_view' => 5,
                'space_between'   => 30,
            ), $atts, 'lsfd_logo_slider');

            // Load all admin-managed logos
            $logos = get_posts(array(
                'post_type'      => 'lsfd_logo',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ));

            if (empty($logos)) {
                return '<div class="lsfd-no-logos"><p>' . esc_html__('No logos to display. Please add logos first.', 'logo-slider-for-divi') . '</p></div>';
            }

            $logos_data = array();
            foreach ($logos as $logo) {
                $image = get_post_meta($logo->ID, 'logo_image', true);
                $url   = get_post_meta($logo->ID, 'logo_url', true);
                $alt   = get_post_meta($logo->ID, 'logo_alt', true);
                $title = get_the_title($logo->ID);
                if ($image) {
                    $logos_data[] = array(
                        'image' => $image,
                        'url'   => $url,
                        'alt'   => $alt ?: $title,
                        'title' => $title,
                    );
                }
            }

            if (empty($logos_data)) {
                return '<div class="lsfd-no-logos"><p>' . esc_html__('No logos to display. Please add logos first.', 'logo-slider-for-divi') . '</p></div>';
            }

            // Ensure assets are enqueued when used via shortcode
            if (!is_admin()) {
                // Mimic module's enqueue: CSS only (JS enqueued via wp_footer when needed)
                wp_enqueue_style('lsfd-frontend-style', LSFD_PLUGIN_URL . 'assets/css/frontend.css', array(), LSFD_PLUGIN_VERSION);
                wp_enqueue_style('swiper-css', 'https://unpkg.com/swiper@8/swiper-bundle.min.css', array(), '8.0.0');
                wp_enqueue_script('swiper-js', 'https://unpkg.com/swiper@8/swiper-bundle.min.js', array(), '8.0.0', true);
                wp_enqueue_script('lsfd-frontend-script', LSFD_PLUGIN_URL . 'assets/js/frontend.js', array('jquery', 'swiper-js'), LSFD_PLUGIN_VERSION, true);
            }

            $slider_id = 'lsfd-slider-' . wp_rand(1000, 9999);
            $data_attrs = array(
                'data-slides-per-view' => esc_attr(intval($atts['slides_per_view'])),
                'data-space-between'   => esc_attr(intval($atts['space_between'])),
                'data-slider-speed'    => 500,
                'data-autoplay'        => 'on',
                'data-pause-on-hover'  => 'off',
                'data-navigation'      => 'off',
                'data-pagination'      => 'off',
            );

            ob_start();
            ?>
            <div class="lsfd-logo-slider-wrapper">
                <div id="<?php echo esc_attr($slider_id); ?>" class="lsfd-logo-slider swiper" <?php echo implode(' ', array_map(function($k, $v) { return $k . '="' . $v . '"'; }, array_keys($data_attrs), $data_attrs)); ?>>
                    <div class="swiper-wrapper">
                        <?php foreach ($logos_data as $logo) : ?>
                            <div class="swiper-slide">
                                <div class="lsfd-logo-item">
                                    <?php if (!empty($logo['url'])) : ?>
                                        <a href="<?php echo esc_url($logo['url']); ?>" target="_blank" rel="noopener">
                                    <?php endif; ?>
                                    <img src="<?php echo esc_url($logo['image']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>" title="<?php echo esc_attr($logo['title']); ?>" />
                                    <?php if (!empty($logo['url'])) : ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        });
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
}

// Initialize the plugin
LogoSliderForDiviPlugin::get_instance();
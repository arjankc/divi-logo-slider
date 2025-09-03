<?php
/**
 * Logo Slider Divi Module
 */

if (!defined('ABSPATH')) {
    exit;
}

class LSFD_LogoSliderModule extends ET_Builder_Module {
    
    public $slug       = 'lsfd_logo_slider';
    public $vb_support = 'on';
    
    protected $module_credits = array(
        'module_uri' => 'https://github.com/Gurkha-Technology-Open-Source/divi-logo-slider',
        'author'     => 'Gurkha Technology',
        'author_uri' => 'https://github.com/Gurkha-Technology-Open-Source',
    );
    
    public function init() {
        $this->name = esc_html__('Logo Slider', 'logo-slider-for-divi');
        $this->icon_path = LSFD_PLUGIN_DIR . 'assets/images/logo-slider-icon.svg';
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    /**
     * Detect Divi Visual Builder reliably (frontend builder)
     */
    private function is_visual_builder_active() {
        if (function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled()) {
            return true;
        }
        if (function_exists('et_fb_is_enabled') && et_fb_is_enabled()) {
            return true;
        }
        if (isset($_GET['et_fb']) && '1' === $_GET['et_fb']) {
            return true;
        }
        return false;
    }
    
    public function enqueue_assets() {
    // Detect Divi Visual Builder
    $is_vb = $this->is_visual_builder_active();

        // Always enqueue frontend CSS
        wp_enqueue_style(
            'lsfd-frontend-style',
            LSFD_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            LSFD_PLUGIN_VERSION
        );

        // Only enqueue Swiper and JS when NOT in Visual Builder to avoid VB rendering issues
        if (!$is_vb) {
            // Swiper CSS/JS
            wp_enqueue_style(
                'swiper-css',
                'https://unpkg.com/swiper@8/swiper-bundle.min.css',
                array(),
                '8.0.0'
            );

            wp_enqueue_script(
                'swiper-js',
                'https://unpkg.com/swiper@8/swiper-bundle.min.js',
                array(),
                '8.0.0',
                true
            );

            // Plugin frontend behavior (initializes Swiper)
            wp_enqueue_script(
                'lsfd-frontend-script',
                LSFD_PLUGIN_URL . 'assets/js/frontend.js',
                array('jquery', 'swiper-js'),
                LSFD_PLUGIN_VERSION,
                true
            );
        }
    }
    
    public function get_fields() {
        $admin_logos = $this->get_admin_logos_options();
        
        return array(
            'logo_source' => array(
                'label'           => esc_html__('Logo Source', 'logo-slider-for-divi'),
                'type'            => 'select',
                'option_category' => 'basic_option',
                'options'         => array(
                    'admin'  => esc_html__('Use Admin Managed Logos', 'logo-slider-for-divi'),
                    'custom' => esc_html__('Add Custom Logos', 'logo-slider-for-divi'),
                ),
                'default'         => 'admin',
                'affects'         => array('selected_logos', 'custom_logos'),
                'tab_slug'        => 'general',
                'toggle_slug'     => 'main_content',
            ),
            'selected_logos' => array(
                'label'           => esc_html__('Select Logos', 'logo-slider-for-divi'),
                'type'            => 'multiple_checkboxes',
                'option_category' => 'basic_option',
                'options'         => $admin_logos,
                'depends_show_if' => 'admin',
                'tab_slug'        => 'general',
                'toggle_slug'     => 'main_content',
            ),
            'custom_logos' => array(
                'label'           => esc_html__('Custom Logos', 'logo-slider-for-divi'),
                'type'            => 'composite',
                'option_category' => 'basic_option',
                'composite_type'  => 'add_new',
                'depends_show_if' => 'custom',
                'tab_slug'        => 'general',
                'toggle_slug'     => 'main_content',
                'composite_structure' => array(
                    'image' => array(
                        'label'              => esc_html__('Logo Image', 'logo-slider-for-divi'),
                        'type'               => 'upload',
                        'option_category'    => 'basic_option',
                        'upload_button_text' => esc_attr__('Upload an image', 'logo-slider-for-divi'),
                        'choose_text'        => esc_attr__('Choose an Image', 'logo-slider-for-divi'),
                        'update_text'        => esc_attr__('Set As Image', 'logo-slider-for-divi'),
                    ),
                    'url' => array(
                        'label'           => esc_html__('Logo URL', 'logo-slider-for-divi'),
                        'type'            => 'text',
                        'option_category' => 'basic_option',
                    ),
                    'alt' => array(
                        'label'           => esc_html__('Alt Text', 'logo-slider-for-divi'),
                        'type'            => 'text',
                        'option_category' => 'basic_option',
                    ),
                ),
            ),
            'slides_per_view' => array(
                'label'           => esc_html__('Logos per View', 'logo-slider-for-divi'),
                'type'            => 'range',
                'option_category' => 'layout',
                'default'         => '5',
                'range_settings'  => array(
                    'min'  => '1',
                    'max'  => '10',
                    'step' => '1',
                ),
                'tab_slug'        => 'advanced',
                'toggle_slug'     => 'slider_settings',
            ),
            'space_between' => array(
                'label'           => esc_html__('Space Between Logos', 'logo-slider-for-divi'),
                'type'            => 'range',
                'option_category' => 'layout',
                'default'         => '30',
                'range_settings'  => array(
                    'min'  => '0',
                    'max'  => '100',
                    'step' => '1',
                ),
                'tab_slug'        => 'advanced',
                'toggle_slug'     => 'slider_settings',
            ),
            'slider_speed' => array(
                'label'           => esc_html__('Slider Speed (ms)', 'logo-slider-for-divi'),
                'type'            => 'range',
                'option_category' => 'layout',
                'default'         => '500',
                'range_settings'  => array(
                    'min'  => '100',
                    'max'  => '2000',
                    'step' => '100',
                ),
                'tab_slug'        => 'advanced',
                'toggle_slug'     => 'slider_settings',
            ),
            'autoplay' => array(
                'label'           => esc_html__('Autoplay', 'logo-slider-for-divi'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('On', 'logo-slider-for-divi'),
                    'off' => esc_html__('Off', 'logo-slider-for-divi'),
                ),
                'default'         => 'on',
                'tab_slug'        => 'advanced',
                'toggle_slug'     => 'slider_settings',
            ),
            'pause_on_hover' => array(
                'label'           => esc_html__('Pause on Hover', 'logo-slider-for-divi'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('On', 'logo-slider-for-divi'),
                    'off' => esc_html__('Off', 'logo-slider-for-divi'),
                ),
                'default'         => 'on',
                'tab_slug'        => 'advanced',
                'toggle_slug'     => 'slider_settings',
            ),
            'navigation_arrows' => array(
                'label'           => esc_html__('Navigation Arrows', 'logo-slider-for-divi'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('On', 'logo-slider-for-divi'),
                    'off' => esc_html__('Off', 'logo-slider-for-divi'),
                ),
                'default'         => 'on',
                'tab_slug'        => 'advanced',
                'toggle_slug'     => 'navigation',
            ),
            'pagination_dots' => array(
                'label'           => esc_html__('Pagination Dots', 'logo-slider-for-divi'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('On', 'logo-slider-for-divi'),
                    'off' => esc_html__('Off', 'logo-slider-for-divi'),
                ),
                'default'         => 'on',
                'tab_slug'        => 'advanced',
                'toggle_slug'     => 'navigation',
            ),
        );
    }
    
    public function get_settings_modal_toggles() {
        return array(
            'general' => array(
                'toggles' => array(
                    'main_content' => esc_html__('Logo Content', 'logo-slider-for-divi'),
                ),
            ),
            'advanced' => array(
                'toggles' => array(
                    'slider_settings' => esc_html__('Slider Settings', 'logo-slider-for-divi'),
                    'navigation' => esc_html__('Navigation', 'logo-slider-for-divi'),
                ),
            ),
        );
    }
    
    private function get_admin_logos_options() {
        $logos = get_posts(array(
            'post_type'      => 'lsfd_logo',
            'numberposts'    => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'post_status'    => 'publish'
        ));
        
        $options = array();
        foreach ($logos as $logo) {
            $options[$logo->ID] = $logo->post_title;
        }
        
        if (empty($options)) {
            $options[''] = esc_html__('No logos found. Please add logos in the admin first.', 'logo-slider-for-divi');
        }
        
        return $options;
    }
    
    public function render($attrs, $content = null, $render_slug) {
        $logo_source       = $this->props['logo_source'];
        $selected_logos    = $this->props['selected_logos'];
        $custom_logos      = $this->props['custom_logos'];
        $slides_per_view   = $this->props['slides_per_view'];
        $space_between     = $this->props['space_between'];
        $slider_speed      = $this->props['slider_speed'];
        $autoplay          = $this->props['autoplay'];
        $pause_on_hover    = $this->props['pause_on_hover'];
        $navigation_arrows = $this->props['navigation_arrows'];
        $pagination_dots   = $this->props['pagination_dots'];

        // Prepare logos data
        $logos_data = array();

        if ('admin' === $logo_source) {
            $logo_ids = array();

            if (!empty($selected_logos)) {
                // Use explicitly selected logos from module settings
                $raw = trim($selected_logos);
                $tokens = array($raw);
                if (strpos($raw, '|') !== false) {
                    $tokens = explode('|', $raw);
                } elseif (strpos($raw, ',') !== false) {
                    $tokens = explode(',', $raw);
                }
                $logo_ids = array_filter(array_map(function($v){
                    return intval(trim($v));
                }, $tokens));
            }

            // Fallback to all admin-managed logos if no valid selections resolved
            if (empty($logo_ids)) {
                // Fallback: load all admin-managed logos in menu_order
                $logos = get_posts(array(
                    'post_type'        => 'lsfd_logo',
                    'posts_per_page'   => -1,
                    'post_status'      => 'publish',
                    'orderby'          => 'menu_order',
                    'order'            => 'ASC',
                    'suppress_filters' => true,
                ));
                if (!empty($logos)) {
                    $logo_ids = wp_list_pluck($logos, 'ID');
                }
            }

            foreach ($logo_ids as $logo_id) {
                if (empty($logo_id)) continue;

                $image = get_post_meta($logo_id, 'logo_image', true);
                $url   = get_post_meta($logo_id, 'logo_url', true);
                $alt   = get_post_meta($logo_id, 'logo_alt', true);
                $title = get_the_title($logo_id);

                if ($image) {
                    $logos_data[] = array(
                        'image' => $image,
                        'url'   => $url,
                        'alt'   => $alt ?: $title,
                        'title' => $title,
                    );
                }
            }
        } elseif ('custom' === $logo_source && $custom_logos) {
            foreach ($custom_logos as $logo) {
                if (!empty($logo['image'])) {
                    $logos_data[] = array(
                        'image' => $logo['image'],
                        'url'   => $logo['url'],
                        'alt'   => $logo['alt'],
                        'title' => $logo['alt'],
                    );
                }
            }
        }
        
        if (empty($logos_data)) {
            return '<div class="lsfd-no-logos"><p>' . esc_html__('No logos to display. Please add logos first.', 'logo-slider-for-divi') . '</p></div>';
        }
        
    // Detect Divi Visual Builder
    $is_vb = $this->is_visual_builder_active();

        ob_start();

        if ($is_vb) {
            // Visual Builder: render a minimal placeholder box (no JS, no images)
            $count = count($logos_data);
            ?>
            <div class="lsfd-vb-placeholder" style="border:1px dashed #cbd5e1;background:#f8fafc;padding:16px;border-radius:6px;display:flex;align-items:center;gap:12px;min-height:56px;">
                <div style="width:28px;height:28px;border-radius:4px;background:#e2e8f0;display:inline-block;"></div>
                <div>
                    <div style="font-weight:600;color:#111827;">Logo Slider</div>
                    <div style="font-size:12px;color:#6b7280;">Preview disabled in builder<?php echo $count ? ' • ' . intval($count) . ' logo' . ($count>1?'s':'') : ''; ?></div>
                </div>
            </div>
            <?php
        } else {
            // Frontend: render Swiper structure
            $slider_id = 'lsfd-slider-' . wp_rand(1000, 9999);

            $data_attrs = array(
                'data-slides-per-view' => esc_attr($slides_per_view),
                'data-space-between'   => esc_attr($space_between),
                'data-slider-speed'    => esc_attr($slider_speed),
                // Force behavior: autoplay on, no nav/pagination for smooth auto scroll
                'data-autoplay'        => 'on',
                'data-pause-on-hover'  => 'off',
                'data-navigation'      => 'off',
                'data-pagination'      => 'off',
            );
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
                                    <img src="<?php echo esc_url($logo['image']); ?>"
                                         alt="<?php echo esc_attr($logo['alt']); ?>"
                                         title="<?php echo esc_attr($logo['title']); ?>" />
                                    <?php if (!empty($logo['url'])) : ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Navigation and pagination intentionally omitted for continuous auto-scroll -->
                </div>
            </div>
            <?php
        }

        return ob_get_clean();
    }
}

// Register the module
new LSFD_LogoSliderModule();

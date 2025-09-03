# Logo Slider for Divi

A powerful WordPress plugin that adds a professional logo slider module to the Divi Builder, featuring centralized admin management and extensive customization options.

![License](https://img.shields.io/badge/license-GPL%20v2%2B-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![Divi](https://img.shields.io/badge/Divi-Compatible-green.svg)

## 🎯 Features

### 🏢 **Centralized Logo Management**
- Upload and manage all logos from WordPress admin dashboard
- Visual drag & drop interface for reordering logos
- Edit, delete, and organize logos in one central location
- Bulk logo selection for Divi modules

### 🎨 **Flexible Design Options**
- Choose between admin-managed logos or custom logos per module
- Responsive design that works on all devices
- Touch/swipe support for mobile users
- Professional navigation with arrows and pagination dots

### ⚙️ **Extensive Customization**
- **Logos per View**: Display 1-10 logos at once
- **Spacing Control**: Adjust space between logos (0-100px)
- **Speed Settings**: Control transition speed (100-2000ms)
- **Autoplay Options**: Enable/disable with pause on hover
- **Navigation Controls**: Toggle arrows and pagination dots
- **Responsive Breakpoints**: Automatic adjustment for different screen sizes

### 🔧 **Developer Friendly**
- Clean, semantic HTML output
- WordPress coding standards compliant
- Extensible architecture
- Translation ready
- Open source with GPL v2+ license

## 📋 Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **Divi Theme**: Latest version recommended
- **Modern Browser**: For admin interface (Chrome, Firefox, Safari, Edge)

## 🚀 Installation

### Method 1: WordPress Admin (Recommended)

1. Download the `logo-slider-for-divi.zip` file
2. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Click **Activate Plugin**
5. Navigate to **Logo Slider** in the admin menu to start adding logos

### Method 2: Manual Installation

1. Download and extract the plugin files
2. Upload the `logo-slider-for-divi` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress
4. Access **Logo Slider** in the admin menu

## 📖 Usage Guide

### Adding Logos (Admin Dashboard)

1. **Navigate to Logo Management**
   - Go to **Logo Slider** in your WordPress admin menu

2. **Add Your First Logo**
   - Fill in the logo title (for your reference)
   - Click **Select Image** to upload or choose from media library
   - Add optional URL for click-through functionality
   - Enter alt text for accessibility
   - Click **Add Logo**

3. **Manage Existing Logos**
   - View all logos in the visual grid below the form
   - **Drag and drop** to reorder logos
   - **Edit** any logo by clicking the Edit button
   - **Delete** logos you no longer need

### Using in Divi Builder

1. **Add the Module**
   - Edit any page with Divi Builder
   - Click **+ Add Module**
   - Search for **"Logo Slider"**
   - Add the module to your page

2. **Choose Logo Source**
   - **Admin Managed Logos**: Select from your centrally managed collection
   - **Custom Logos**: Add logos directly in the builder

3. **Customize Settings**
   - **Content Tab**: Choose logos and source
   - **Design Tab**: Customize appearance (future enhancement)
   - **Advanced Tab**: Configure slider behavior and navigation

4. **Configure Slider Settings**
   - Set logos per view (responsive)
   - Adjust spacing between logos
   - Control transition speed
   - Enable/disable autoplay and navigation

## 🎨 Customization Options

### Content Settings
| Option | Description | Default |
|--------|-------------|---------|
| Logo Source | Choose between admin-managed or custom logos | Admin Managed |
| Selected Logos | Pick specific logos from admin collection | All available |
| Custom Logos | Add logos directly in builder | Empty |

### Slider Settings
| Option | Description | Range | Default |
|--------|-------------|--------|---------|
| Logos per View | Number of logos visible at once | 1-10 | 5 |
| Space Between | Spacing between logos in pixels | 0-100px | 30px |
| Slider Speed | Transition speed in milliseconds | 100-2000ms | 500ms |
| Autoplay | Automatic sliding | On/Off | On |
| Pause on Hover | Pause when user hovers | On/Off | On |

### Navigation Settings
| Option | Description | Default |
|--------|-------------|---------|
| Navigation Arrows | Show prev/next arrows | On |
| Pagination Dots | Show dot indicators | On |

## 📱 Responsive Behavior

The plugin automatically adjusts for different screen sizes:

- **Desktop (1200px+)**: Shows configured number of logos
- **Tablet (1024px)**: Shows up to 4 logos maximum
- **Mobile Large (768px)**: Shows up to 3 logos maximum  
- **Mobile Small (320px)**: Shows up to 2 logos maximum

Spacing is also automatically adjusted for optimal viewing on each device.

## 🛠️ Development

### File Structure
```
logo-slider-for-divi/
├── logo-slider-for-divi.php      # Main plugin file
├── includes/
│   ├── admin/
│   │   └── class-admin.php        # Admin functionality
│   └── modules/
│       └── class-logo-slider-module.php  # Divi module
├── assets/
│   ├── css/
│   │   ├── admin.css             # Admin styles
│   │   └── frontend.css          # Frontend styles
│   └── js/
│       ├── admin.js              # Admin JavaScript
│       └── frontend.js           # Frontend JavaScript
├── languages/                    # Translation files
├── readme.txt                    # WordPress plugin readme
└── README.md                     # This file
```

### Hooks and Filters

The plugin provides several hooks for customization:

```php
// Modify default slider settings
add_filter('lsfd_default_settings', function($settings) {
    $settings['slides_per_view'] = 6;
    return $settings;
});

// Add custom CSS classes
add_filter('lsfd_slider_classes', function($classes) {
    $classes[] = 'my-custom-class';
    return $classes;
});
```

### Contributing

We welcome contributions! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 🐛 Troubleshooting

### Common Issues

**Logo Slider menu not appearing in admin:**
- Ensure you have administrator privileges
- Check that the plugin is activated
- Try deactivating and reactivating the plugin

**Module not showing in Divi Builder:**
- Verify Divi theme is active and up to date
- Clear any caching plugins
- Check for JavaScript console errors

**Images not displaying:**
- Verify image URLs are correct and accessible
- Check file permissions on uploads directory
- Ensure images are properly uploaded to media library

**Slider not working on frontend:**
- Check for JavaScript conflicts with other plugins
- Ensure Swiper.js is loading correctly
- Verify no console errors on the page

### Getting Help

1. Check our [GitHub Issues](https://github.com/Gurkha-Technology-Open-Source/divi-logo-slider/issues)
2. Search existing issues before creating a new one
3. Provide detailed information about your setup and the issue

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 👥 Credits

- **Developer**: [Gurkha Technology](https://github.com/Gurkha-Technology-Open-Source)
- **Swiper.js**: Modern slider library by Vladimir Kharlampidi
- **WordPress**: Content management system
- **Divi**: Page builder by Elegant Themes

## 🔄 Changelog

### v1.0.1
- Fix: Media Library not opening from admin "Select Image" button due to invalid script dependency. Removed bad dependency and ensured `wp_enqueue_media()` is loaded only on our admin page.
- Fix: Prevent "constant already defined" warnings if more than one copy of the plugin is present by guarding constants and main class definitions.

### v1.0.2
- Packaging: Ensure zip contains a single top-level `logo-slider-for-divi` folder to avoid duplicate installs. Additional guards for safer activation.

### v1.0.0 (Initial Release)
- ✅ Centralized logo management dashboard
- ✅ Divi Builder module integration
- ✅ Responsive design with touch/swipe support
- ✅ Customizable slider settings
- ✅ Drag and drop logo reordering
- ✅ SEO-friendly implementation
- ✅ Mobile-first responsive design
- ✅ Professional navigation controls

---

**Made with ❤️ by [Gurkha Technology](https://github.com/Gurkha-Technology-Open-Source)**

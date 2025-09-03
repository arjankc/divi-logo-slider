=== Logo Slider for Divi ===
Contributors: gurkhaTechnology
Donate link: https://github.com/Gurkha-Technology-Open-Source
Tags: divi, logo, slider, carousel, logo slider, logo carousel, divi module, admin dashboard, partners, clients
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful Divi module for creating beautiful logo sliders with centralized admin management and extensive customization options.

== Description ==

Logo Slider for Divi is a comprehensive WordPress plugin that adds a professional logo slider module to the Divi Builder. Perfect for showcasing partners, clients, sponsors, or any collection of logos in an elegant, responsive slider format.

**🎯 Key Features:**

* **Centralized Logo Management** - Upload and manage all logos from WordPress admin dashboard
* **Flexible Logo Sources** - Choose between admin-managed logos or add custom logos directly in Divi Builder
* **Drag & Drop Reordering** - Easily reorder logos with intuitive drag and drop functionality
* **Responsive Design** - Automatically adapts to all screen sizes and devices
* **Customizable Settings** - Full control over slides per view, spacing, speed, autoplay, and navigation
* **SEO Friendly** - Add alt text and links to logos for better accessibility and SEO
* **Touch/Swipe Support** - Native mobile gesture support for better user experience
* **Professional Navigation** - Optional arrow navigation and pagination dots

**✨ Admin Dashboard Features:**

* Visual logo management interface
* Upload logos directly through WordPress media library
* Add titles, URLs, and alt text for each logo
* Drag and drop reordering
* Edit and delete logos easily
* Bulk logo selection for Divi modules

**🎨 Divi Builder Integration:**

* Native Divi module with familiar interface
* Choose from centrally managed logos or add custom ones
* Extensive customization options organized in logical tabs
* Real-time preview in Divi Builder
* Compatible with Divi's responsive design tools

**⚙️ Customization Options:**

* Logos per view (1-10)
* Space between logos (0-100px)
* Slider transition speed (100-2000ms)
* Autoplay with custom timing
* Pause on hover functionality
* Navigation arrows (show/hide)
* Pagination dots (show/hide)
* Responsive breakpoints

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/logo-slider-for-divi` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to "Logo Slider" in your WordPress admin menu to manage your logos.
4. Add logos using the admin interface or directly in the Divi Builder.
5. Use the "Logo Slider" module in any Divi page or post.

== Frequently Asked Questions ==

= How do I add logos to the slider? =

You have two options:
1. **Admin Dashboard**: Go to "Logo Slider" in your WordPress admin menu to upload and manage logos centrally. Then use the Divi module and select "Use Admin Managed Logos".
2. **Divi Builder**: In the module settings, choose "Add Custom Logos" and add logos directly within the builder.

= Can I reorder my logos? =

Yes! In the admin dashboard, you can drag and drop logos to reorder them. The order will be reflected in your sliders automatically.

= Can I use the same logos across multiple pages? =

Absolutely! Admin-managed logos can be reused across multiple pages and sliders, making it easy to maintain consistency.

= Is the slider responsive? =

Yes, the slider is fully responsive and automatically adjusts the number of visible logos based on screen size.

= Can I customize the slider appearance? =

Yes, you can customize:
- Number of logos displayed at once
- Spacing between logos
- Slider transition speed
- Autoplay settings
- Navigation arrows and pagination dots
- Responsive behavior

= Does it work with other themes? =

While optimized for Divi, the plugin uses standard WordPress practices and should work with most themes. However, Divi is required for the builder module functionality.

= Can I add links to the logos? =

Yes, each logo can have an optional URL that will open in a new tab when clicked.

= Is it SEO friendly? =

Yes, you can add alt text to each logo for better accessibility and SEO. Links are also properly structured.

== Screenshots ==

1. Admin dashboard - Logo management interface
2. Divi Builder - Logo Slider module settings
3. Frontend - Example logo slider display
4. Admin dashboard - Adding a new logo
5. Frontend - Mobile responsive view

== Changelog ==

= 1.0.2 =
* Packaging: Ensure zip includes a single `logo-slider-for-divi` directory to prevent side-by-side duplicates. Additional safety guards.

= 1.0.1 =
* Fix: Media Library button not working on admin page. Removed invalid 'wp-media' script dependency and rely on wp_enqueue_media().
* Fix: Guard constants and main class to avoid "already defined" warnings if multiple copies are installed.

= 1.0.0 =
* Initial release
* Centralized logo management dashboard
* Divi Builder module integration
* Responsive design with touch/swipe support
* Customizable slider settings
* Drag and drop logo reordering
* SEO-friendly implementation
* Mobile-first responsive design

== Upgrade Notice ==

= 1.0.0 =
Initial release of Logo Slider for Divi. Install to start creating beautiful logo sliders with centralized management.

== Support ==

For support, please visit our [GitHub repository](https://github.com/Gurkha-Technology-Open-Source/divi-logo-slider) or create an issue there.

== Contributing ==

We welcome contributions! Please see our [GitHub repository](https://github.com/Gurkha-Technology-Open-Source/divi-logo-slider) for contribution guidelines.

== License ==

This plugin is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

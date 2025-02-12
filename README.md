# === Animated Gutenberg Gallery ===
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.0
Stable tag: 1.2.5
**License:** Commercial License ([See License Terms](#licensing))  

# Animated Gutenberg Gallery

Beautiful GSAP animations for WordPress Gutenberg gallery blocks.

## Description
Add professional animations and lightbox functionality to your WordPress gallery blocks with GSAP animations.

## Actions & Filters
- `agg_animation_options` - Modify available animation options
- `agg_hover_effects` - Modify available hover effects
- `agg_gallery_settings` - Filter gallery settings before output


## Installation & Activation  
1. **Download & Install**  
   - Purchase the plugin from [Matysiewicz Studio](https://matysiewicz.studio).  
   - Download the `.zip` file from your account dashboard.  
   - Upload the plugin via **Plugins > Add New > Upload Plugin** in WordPress.  
   - Click **Activate Plugin**.  

2. **License Activation (Required)**  
   - After activation, you'll be prompted to **enter your license key**.  
   - Enter the key provided in your account after purchase.  
   - Click **Activate** to enable updates & support.  

💡 **Note:** You must activate the license to receive automatic updates & support.  

## Requirements
- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern browsers supporting CSS3 animations

## Usage
1. Create a gallery block in WordPress
2. The animations will be automatically applied
3. Customize settings in the AG Gallery settings page

    ## Structure
```
animated-gutenberg-gallery/
├── assets/                 # Frontend resources
│   ├── css/                # Stylesheets
│   │   ├── agg-admin.css   # Admin styles
│   │   └── agg-public.css  # Public styles
│   ├── js/                 # JavaScript files
│   │   ├── agg-admin.js    # Admin scripts
│   │   └── agg-public.js   # Public scripts
│   │   └── agg-editor.js   # Gutenberg block scripts
│   └── images/             # Images and icons
├── includes/               # PHP classes
│   ├── admin/              # Admin functionality
│   │   ├── class-agg-admin.php
│   │   └── views/
│   ├── core/               # Core functionality
│   │   ├── class-agg-activator.php
│   │   ├── class-agg-assets.php
│   │   └── ...
│   └── frontend/           # Frontend functionality
├── languages/              # Translations
└── animated-gutenberg-gallery.php
```

## Licensing  
This plugin is sold under a **Commercial License** and requires a valid **Freemius license key** for activation.  

### **License Types**  
- **Single-Site License:** Use on **1 website**  
- **Three-Site License:** Use on **up to 3 websites**  
- **Annual License:** Includes **1 year of updates & support** (must renew to continue receiving updates).  
- **Lifetime License:** Includes **lifetime updates & support** (one-time payment).  

🔹 Your license can be **managed in your account** at [Matysiewicz Studio](matysiewicz.studio/account/#!/licenses)  
🔹 Licenses can be **moved between sites** via the **Freemius dashboard**.  

📜 **Read full license terms in LICENSE.txt**  

## Support
For support inquiries:
- Email: support@matysiewicz.studio
- Website: https://matysiewicz.studio

## Version History
- 1.2.6: Load same animations on frontend and backend
- 1.2.5: Alternate Scroll bug fix
- 1.2.4: Added Animation Style
- 1.2.3: Litespeed Cache image lazy load compatibility added
- 1.2.2: Added Lenis Scroll and scroll effect
- 1.2.1: Added branding
- 1.2.0: Lightbox works asynchronously with fade animations
- 1.0.9: Removed effects on single images
- 1.0.8: Added Switcher on/off to Galleries
- 1.0.7: Added lazy load animation, 
- 1.0.6: Added Compatibility with Polylang and WPML
- 1.0.5: Added CSS fixes
- 1.0.4: Added Live Preview
- 1.0.3: Added Hover Effects
- 1.0.2: Added Animation Effects
- 1.0.1: Added Lightbox Functionality
- 1.0.0: Initial release

## Credits
Created by Matysiewicz Studio
Copyright (c) 2024 Matysiewicz Studio
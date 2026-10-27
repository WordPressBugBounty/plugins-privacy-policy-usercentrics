# Usercentrics Privacy Policy Generator - WordPress Plugin

Privacy Policy Generator plugin for WordPress that integrates with the Usercentrics platform.

## 📋 Overview

This plugin allows WordPress users to automatically display their Usercentrics privacy policy on any page without manual script insertion.

### Key Features
- Automatic policy injection via `the_content` filter
- Real-time policy loading from Usercentrics API
- Multi-language support (browser detection)
- Live policy preview in WordPress editor
- Preserves original page content in database
- Clean deactivation (restores original content)

---

## 🚀 Installation

### Requirements
- WordPress 6.0+
- PHP 7.4+
- Node.js 18+ (for development)

### Setup

```bash
# Install dependencies
composer install --no-dev  # Production
npm install
npm run build
```

---

## 📁 Project Structure

```
privacy-policy-usercentrics/
├── src/                          # PHP Classes (PSR-4, MVC-inspired)
│   ├── Admin/
│   │   ├── Controllers/          # Page controllers (Dashboard, Settings, Support)
│   │   ├── Actions/              # Business logic (PolicyActions, PageActions)
│   │   ├── AssetsManager.php     # Script/style enqueuing
│   │   ├── EditorContent.php     # Editor preview functionality
│   │   └── MainMenu.php          # Admin menu registration
│   ├── Core/                     # Plugin initialization
│   ├── Frontend/                 # Frontend policy injection
│   └── Settings/                 # Settings service & validation
├── templates/                    # PHP view templates
│   └── admin/                    # Admin page templates (settings, overview, support)
├── assets/
│   ├── src/
│   │   ├── scss/                 # Modular SCSS (modern Sass, no deprecations)
│   │   └── js/                   # ES6+ modules
│   └── dist/                     # Compiled assets (Vite build output)
├── privacy-policy-usercentrics.php          # Main plugin file
├── uninstall.php                 # Cleanup on uninstall
└── readme.txt                    # WordPress.org readme
```

---

## 🔧 Development

### Build Commands

```bash
# Development mode (watch files)
npm run dev

# Production build (minified)
npm run build

# Create distribution ZIP
npm run zip
```

### Build Stack
- **Vite 7.x** - Lightning-fast build tool
- **SASS 1.86** - Modern CSS preprocessor (no deprecations)
- **ESM** - Native JavaScript modules
- **Terser** - Code minification

### Code Standards

```bash
# Check PHP code standards (WordPress Coding Standards)
composer run phpcs

# Auto-fix PHP code standards
composer run phpcbf
```

### JavaScript Architecture
- **Modular ES6+** - No jQuery dependency
- **3 Core Modules**:
  - `ModalManager` - Handles all modal dialogs
  - `FormValidator` - Client-side validation with UX feedback
  - `PolicyForm` - Form logic, page selection, disconnect handling
- **Simple & Maintainable** - No over-engineering, clear separation of concerns

---

## 🏗️ Architecture

### MVC-Inspired Pattern
- **Controllers** (`Admin/Controllers/`) - Handle page rendering and request routing
- **Actions** (`Admin/Actions/`) - Business logic separated from controllers
- **Views** (`templates/`) - Clean PHP templates with minimal logic
- **Services** (`Settings/SettingsService`) - Centralized settings management

### Content Injection (Non-Invasive)
- Uses `the_content` filter to replace page content on frontend
- Original content never modified in database
- Automatic restoration on plugin deactivation or page change

### Editor Preview
- Fetches real policy from Usercentrics API
- Displays in editor (read-only preview)
- Works with Gutenberg and Classic Editor

### State Management
Plugin tracks 3 states:
1. **Not Connected** - No Policy ID set
2. **Connected Not Published** - Policy ID set, page not selected
3. **Published** - Policy active and live

### Smart Page Sorting
Settings page intelligently sorts pages:
1. WordPress designated privacy page (⭐)
2. Pages with "privacy" or "policy" in title
3. All other pages (alphabetical)

---

## ⚙️ Configuration

### WordPress Options
- `ppguc_usercentrics_policy_id` - Policy UUID
- `ppguc_usercentrics_display_page` - Selected page ID
- `ppguc_usercentrics_onboarding_complete` - Onboarding flag

---

## 🔐 Security

- Input sanitization (`sanitize_text_field`, `absint`)
- Output escaping (`esc_html`, `esc_attr`, `esc_url`, `esc_js`)
- Nonce verification on all forms
- Capability checks (`manage_options`)
- UUID validation for Policy ID
- Direct file access prevention

---

## 📝 Version History

See `readme.txt` for detailed changelog.

---

## 📄 License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

---

## 🔗 Links

- [Usercentrics Platform](https://usercentrics.com)
- [WordPress Plugin Directory](https://wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)

# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Wland Chat iA** is a professional WordPress plugin that integrates AI-powered chat via N8N webhooks. Built as a Gutenberg block with both global display and per-page insertion options. Developed by Carlos Vera (BravesLab) and Mikel Marqués for Weblandia.es.

## Development Environment

This is a WordPress plugin located within a XAMPP installation:
- **Plugin path**: `/Applications/XAMPP/xamppfiles/htdocs/wordpress/wp-content/plugins/Wland-Chat-iA`
- **WordPress root**: `/Applications/XAMPP/xamppfiles/htdocs/wordpress`
- Test in browser at the WordPress site running on XAMPP

## Architecture Overview

### OOP Structure with PHP Namespaces

All PHP classes use the `WlandChat` namespace and follow the Singleton pattern:

**Main Plugin File**: `wland-chat-ia.php`
- Entry point that initializes the plugin
- Defines constants (`WLAND_CHAT_VERSION`, `WLAND_CHAT_PLUGIN_DIR`, etc.)
- Loads all class dependencies
- Handles activation/deactivation hooks

**Core Classes** (in `includes/`):
- **`class-settings.php`**: Settings API implementation for admin panel configuration
  - Registers all plugin options (webhook URL, N8N auth token, titles, messages, etc.)
  - Handles sanitization and validation
  - Renders admin settings page at **Settings > Wland Chat iA**

- **`class-customizer.php`**: WordPress Customizer API integration for live preview

- **`class-block.php`**: Gutenberg block registration
  - Registers `wland/chat-widget` block type
  - Dynamically generates block.js, block-editor.css, and block-style.css if missing
  - Server-side rendering via `render_block()` method

- **`class-frontend.php`**: Frontend asset management and rendering
  - **Conditional asset loading** (WPO optimization) - only loads CSS/JS when chat should display
  - Handles global chat rendering (when enabled site-wide)
  - Passes N8N auth token to JavaScript via `wp_localize_script()`

- **`class-helpers.php`**: Utility functions
  - `should_display_chat()`: Checks exclusions and availability
  - `is_within_availability_hours()`: Timezone-aware scheduling
  - `get_chat_config()`: Returns full config including N8N token
  - `sanitize_block_attributes()`: Sanitizes block parameters

### Frontend JavaScript Architecture

**Modal Mode** (`assets/js/wland-chat-block-modal.js`):
- `WlandChatModal` class handles chat window lifecycle
- Fetches from N8N webhook with `X-N8N-Auth` header authentication
- Maintains conversation history in `conversationHistory` array
- Sends payload format: `{prompt, sessionId, history}`

**Fullscreen Mode** (`assets/js/wland-chat-block-screen.js`):
- Similar architecture for fullscreen display mode

**Gutenberg Block** (`assets/js/block.js`):
- Registers block with WordPress block editor
- Provides InspectorControls for per-block customization
- Shows preview in editor with live attribute updates

### Templates

**`templates/modal.php`**: HTML structure for modal chat widget
**`templates/screen.php`**: HTML structure for fullscreen chat widget

Both templates:
- Extract variables from `$attributes` array
- Include Lottie animation container
- Render with sanitized values from PHP

## Key Features

### N8N Webhook Integration
- Webhook URL configurable in settings
- **Authentication**: N8N token stored in `wland_chat_n8n_auth_token` option
- Token passed to frontend JS and sent as `X-N8N-Auth` header
- Expected payload: `{prompt: string, sessionId: string, history: array}`
- Expected response: `{output: string}` or `{response: string}`

### Display Modes
- **Modal**: Floating chat widget (bottom-right, bottom-left, or center)
- **Fullscreen**: Full-page chat interface

### Conditional Display Logic
1. **Page exclusions**: Multi-select of WordPress pages in settings
2. **Availability hours**: Start/end time with timezone support
3. **Global vs Block**: Can enable site-wide or use Gutenberg block per-page
4. **WPO optimization**: Assets only load when chat should display

### Settings Structure
All options prefixed with `wland_chat_`:
- `webhook_url`: N8N endpoint
- `n8n_auth_token`: Authentication token for N8N
- `global_enable`: Boolean for site-wide display
- `header_title`, `header_subtitle`, `welcome_message`: UI text
- `position`: 'bottom-right' | 'bottom-left' | 'center'
- `display_mode`: 'modal' | 'fullscreen'
- `excluded_pages`: Array of page IDs
- `availability_enabled`: Boolean for hour restrictions
- `availability_start`, `availability_end`: 'HH:MM' format
- `availability_timezone`: PHP timezone identifier
- `availability_message`: Message shown outside hours

## Development Workflow

### Testing Changes
1. Changes to PHP files require WordPress cache clear or page refresh
2. JavaScript changes may need browser cache clear (Cmd+Shift+R)
3. Test admin panel at: `/wp-admin/options-general.php?page=wland-chat-settings`
4. Test block editor: Create/edit any post/page, add "Wland Chat iA" block

### Git Workflow
- Main branch: `main`
- Current branch: `claude-edits`
- Repository includes git history

### File Generation
The plugin auto-generates missing asset files on initialization:
- `assets/js/block.js`
- `assets/css/block-editor.css`
- `assets/css/block-style.css`

Don't manually create these - let `class-block.php` methods handle it.

## Important Implementation Details

### Security
- All inputs sanitized (Settings API callbacks)
- Output escaped (esc_html, esc_attr, esc_url)
- Nonce verification for AJAX
- Singleton pattern prevents multiple instances

### Hooks & Filters
Plugin provides filters (check README.md for full list):
- `wland_chat_config`: Modify configuration
- `wland_chat_welcome_message`: Customize welcome text
- `wland_chat_block_attributes`: Alter block defaults

### Internationalization
- Text domain: `wland-chat`
- POT file: `languages/wland-chat.pot`
- All strings use `__()`, `_e()`, `_x()` functions

## Common Modifications

### Adding New Settings Field
1. Register setting in `class-settings.php::register_settings()`
2. Add callback method for field rendering
3. Add sanitization callback if needed
4. Update `class-helpers.php::get_chat_config()` if passed to frontend

### Modifying N8N Request
Edit `assets/js/wland-chat-block-modal.js::sendMessage()`:
- Change headers object
- Modify payload structure
- Adjust response parsing

### Changing Display Logic
Modify `class-helpers.php::should_display_chat()`:
- Add conditions
- Check additional options
- Update exclusion rules

## Dependencies

### PHP
- WordPress 5.8+
- PHP 7.4+
- WordPress Settings API, Customizer API, Block API

### JavaScript
- WordPress block editor packages (wp-blocks, wp-element, wp-components)
- Lottie Web 5.12.2 (via CDN)
- Native Fetch API for webhook requests

### Assets
- Lottie animation: `assets/media/chat.json`
- CSS files load conditionally based on display mode

## Code Style

**IMPORTANT**: This codebase follows strict naming conventions:

### Naming Convention
- **snake_case for everything**: All functions and variables MUST use snake_case
- **NO camelCase**: Never use camelCase for functions or variables
- Examples:
  - ✅ `generate_session_id()`, `webhook_url`, `auth_token`
  - ❌ `generateSessionId()`, `webhookUrl`, `authToken`

### Documentation
- **JSDoc required**: Every JavaScript function must have JSDoc comment block above it
- **PHP DocBlocks**: Use standard WordPress DocBlock format for PHP functions

### Standards
- PHP: WordPress Coding Standards + snake_case naming
- JavaScript: ES6+ with class syntax + snake_case naming + JSDoc
- CSS: BEM-like naming for chat components
- Namespace all PHP classes under `WlandChat`

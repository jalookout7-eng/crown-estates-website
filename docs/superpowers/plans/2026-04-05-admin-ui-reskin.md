# Admin UI Reskin Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Reskin the entire WordPress admin with Crowns Estates branding — white background, dark sidebar, gold accents, Inter typography.

**Architecture:** All styles live in a single `css/admin.css` file enqueued via a dedicated `inc/admin-styles.php` hook file. No JS changes. No core edits. High-specificity CSS overrides WP defaults.

**Tech Stack:** PHP (WordPress hooks), CSS (no preprocessor)

---

## File Structure

| File | Action | Purpose |
|---|---|---|
| `wp-content/themes/crowns-estates/inc/admin-styles.php` | Create | Enqueue admin CSS + Google Fonts in admin |
| `wp-content/themes/crowns-estates/css/admin.css` | Create | All admin styles — sidebar, top bar, buttons, inputs, tables, notices, edit screens |
| `wp-content/themes/crowns-estates/functions.php` | Modify | Add `require` for `admin-styles.php` |

---

### Task 1: Create `admin-styles.php` and wire into `functions.php`

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/admin-styles.php`
- Modify: `wp-content/themes/crowns-estates/functions.php`

- [ ] **Step 1: Create `inc/admin-styles.php`**

```php
<?php
// wp-content/themes/crowns-estates/inc/admin-styles.php
defined('ABSPATH') || exit;

add_action('admin_enqueue_scripts', function (): void {
    wp_enqueue_style(
        'ce-admin-ui',
        get_template_directory_uri() . '/css/admin.css',
        [],
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style(
        'ce-admin-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600&display=swap',
        [],
        null
    );
});

// Hide the WP logo in the admin bar, show CE wordmark instead
add_action('admin_bar_menu', function (WP_Admin_Bar $bar): void {
    $bar->remove_node('wp-logo');
}, 999);

add_action('admin_bar_menu', function (WP_Admin_Bar $bar): void {
    $bar->add_node([
        'id'    => 'ce-wordmark',
        'title' => '<span style="font-family:\'Playfair Display\',serif;font-size:14px;font-weight:600;color:#C4973A;letter-spacing:0.02em">Crowns Estates</span>',
        'href'  => admin_url(),
        'meta'  => ['class' => 'ce-wordmark-node'],
    ]);
}, 999);
```

- [ ] **Step 2: Add require to `functions.php`**

Open `wp-content/themes/crowns-estates/functions.php`. After the existing last `require` line (currently `require get_template_directory() . '/inc/login-page.php';`), add:

```php
require get_template_directory() . '/inc/admin-styles.php';
```

- [ ] **Step 3: Create empty `css/admin.css` placeholder to confirm enqueue works**

```css
/* Crowns Estates Admin UI */
```

- [ ] **Step 4: Verify it loads**

In Local WP Admin, open browser DevTools (F12) → Network tab → reload → confirm `admin.css` appears in the network requests with status 200.

- [ ] **Step 5: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/admin-styles.php \
        wp-content/themes/crowns-estates/css/admin.css \
        wp-content/themes/crowns-estates/functions.php
git commit -m "feat: scaffold admin UI reskin — enqueue hook and empty stylesheet"
```

---

### Task 2: Style the admin bar (top bar)

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Add admin bar styles to `css/admin.css`**

```css
/* ── Variables ────────────────────────────────────────────────────────────── */
:root {
    --ce-gold:        #C4973A;
    --ce-gold-hover:  #B8892E;
    --ce-dark:        #0A0A0A;
    --ce-sidebar:     #1A1A1A;
    --ce-bg:          #FAFAFA;
    --ce-surface:     #FFFFFF;
    --ce-text:        #0A0A0A;
    --ce-text-muted:  #666666;
    --ce-border:      #E8E8E8;
    --ce-error:       #EF4444;
    --ce-success:     #22C55E;
}

/* ── Admin Bar ────────────────────────────────────────────────────────────── */
#wpadminbar {
    background: var(--ce-surface) !important;
    border-bottom: 1px solid var(--ce-border);
    font-family: 'Inter', sans-serif;
}

#wpadminbar * {
    font-family: 'Inter', sans-serif !important;
}

#wpadminbar .ab-item,
#wpadminbar a.ab-item,
#wpadminbar .ab-empty-item {
    color: var(--ce-text) !important;
}

#wpadminbar .ab-item:hover,
#wpadminbar a.ab-item:hover,
#wpadminbar li:hover > .ab-item,
#wpadminbar li.hover > .ab-item {
    background: var(--ce-bg) !important;
    color: var(--ce-gold) !important;
}

/* Howdy / user name in gold */
#wpadminbar #wp-admin-bar-my-account .ab-item {
    color: var(--ce-text-muted) !important;
}

#wpadminbar #wp-admin-bar-my-account .display-name {
    color: var(--ce-gold) !important;
    font-weight: 500;
}

/* Dropdown menus */
#wpadminbar .menupop .ab-sub-wrapper {
    background: var(--ce-surface);
    border: 1px solid var(--ce-border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

#wpadminbar .ab-submenu .ab-item {
    color: var(--ce-text) !important;
}

#wpadminbar .ab-submenu li:hover .ab-item {
    background: var(--ce-bg) !important;
    color: var(--ce-gold) !important;
}

/* Site name link */
#wpadminbar #wp-admin-bar-site-name .ab-item {
    color: var(--ce-gold) !important;
    font-weight: 500;
}
```

- [ ] **Step 2: Verify in browser**

Visit `http://crowns-estates.local/wp-admin/`. The top bar should be white with dark text and gold hover states. The WP logo should be gone, replaced by "Crowns Estates" wordmark.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — white top bar with gold accents"
```

---

### Task 3: Refine the sidebar navigation

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Append sidebar styles to `css/admin.css`**

```css
/* ── Sidebar Navigation ───────────────────────────────────────────────────── */
#adminmenuback,
#adminmenuwrap {
    background: var(--ce-sidebar) !important;
}

#adminmenu,
#adminmenu * {
    font-family: 'Inter', sans-serif !important;
}

/* All menu items */
#adminmenu li a,
#adminmenu li .wp-menu-name {
    color: #CCCCCC !important;
    font-size: 13px;
    font-weight: 400;
}

/* Hover state */
#adminmenu li:hover > a,
#adminmenu li:hover .wp-menu-name,
#adminmenu li > a:focus {
    color: #FFFFFF !important;
    background: rgba(196,151,58,0.15) !important;
}

/* Active / current menu item */
#adminmenu .wp-has-current-submenu .wp-submenu-head,
#adminmenu a.wp-has-current-submenu,
#adminmenu li.current > a,
#adminmenu .current .wp-menu-name {
    background: var(--ce-gold) !important;
    color: #FFFFFF !important;
    font-weight: 500 !important;
}

/* Active menu item left bar indicator */
#adminmenu .wp-has-current-submenu .wp-submenu-head::before,
#adminmenu li.current > a::before {
    color: #FFFFFF !important;
}

/* Submenu */
#adminmenu .wp-submenu {
    background: #111111 !important;
}

#adminmenu .wp-submenu li a {
    color: #AAAAAA !important;
    font-size: 12px;
}

#adminmenu .wp-submenu li a:hover,
#adminmenu .wp-submenu li.current a {
    color: var(--ce-gold) !important;
    background: transparent !important;
}

/* Menu icons */
#adminmenu .menu-icon-generic div.wp-menu-image:before,
#adminmenu div.wp-menu-image:before {
    color: #888888 !important;
}

#adminmenu li.current div.wp-menu-image:before,
#adminmenu li:hover div.wp-menu-image:before {
    color: #FFFFFF !important;
}

/* Collapse button */
#collapse-button {
    color: #888888 !important;
}

#collapse-button:hover {
    color: var(--ce-gold) !important;
}
```

- [ ] **Step 2: Verify in browser**

The sidebar should show dark background with grey text, gold active state, and subtle gold hover tint. Navigate to Properties, Enquiries — confirm active item highlights in gold.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — refined sidebar with gold active states"
```

---

### Task 4: Page background, headings, and layout

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Append page layout styles to `css/admin.css`**

```css
/* ── Page Background + Layout ────────────────────────────────────────────── */
body.wp-admin,
#wpwrap,
#wpcontent,
#wpbody,
#wpbody-content {
    background: var(--ce-bg) !important;
    font-family: 'Inter', sans-serif !important;
}

/* Page headings */
.wrap h1,
.wrap h1.wp-heading-inline {
    font-family: 'Playfair Display', serif !important;
    font-size: 22px !important;
    font-weight: 600 !important;
    color: var(--ce-text) !important;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--ce-gold);
    margin-bottom: 16px;
}

.wrap h2 {
    font-family: 'Inter', sans-serif !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    color: var(--ce-text) !important;
}

/* Page title action buttons (e.g. "Add New") */
.page-title-action {
    background: var(--ce-gold) !important;
    border-color: var(--ce-gold) !important;
    color: #FFFFFF !important;
    border-radius: 4px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    text-shadow: none !important;
    box-shadow: none !important;
    padding: 4px 12px !important;
}

.page-title-action:hover {
    background: var(--ce-gold-hover) !important;
    border-color: var(--ce-gold-hover) !important;
    color: #FFFFFF !important;
}

/* Admin footer */
#wpfooter {
    border-top: 1px solid var(--ce-border);
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    color: #999999;
}

#wpfooter a {
    color: var(--ce-gold);
}
```

- [ ] **Step 2: Verify in browser**

Visit `http://crowns-estates.local/wp-admin/`. The background should be `#FAFAFA`, the "Dashboard" heading should use Playfair Display with a gold underline. The footer should show the CE branding text.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — page background, headings, layout"
```

---

### Task 5: Buttons

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Append button styles to `css/admin.css`**

```css
/* ── Buttons ─────────────────────────────────────────────────────────────── */
.wp-core-ui .button-primary,
.wp-core-ui input[type="submit"].button-primary,
#publish {
    background: var(--ce-gold) !important;
    border-color: var(--ce-gold) !important;
    color: #FFFFFF !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    text-shadow: none !important;
    box-shadow: none !important;
    border-radius: 4px !important;
    padding: 6px 16px !important;
    height: auto !important;
    line-height: 1.6 !important;
    transition: background 0.2s, box-shadow 0.2s !important;
}

.wp-core-ui .button-primary:hover,
.wp-core-ui input[type="submit"].button-primary:hover,
#publish:hover {
    background: var(--ce-gold-hover) !important;
    border-color: var(--ce-gold-hover) !important;
    box-shadow: 0 2px 8px rgba(196,151,58,0.3) !important;
}

.wp-core-ui .button-primary:focus,
#publish:focus {
    box-shadow: 0 0 0 2px rgba(196,151,58,0.4) !important;
    outline: none !important;
}

/* Secondary button */
.wp-core-ui .button,
.wp-core-ui .button-secondary {
    background: var(--ce-surface) !important;
    border-color: var(--ce-border) !important;
    color: var(--ce-text) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    text-shadow: none !important;
    box-shadow: none !important;
    border-radius: 4px !important;
    padding: 6px 16px !important;
    height: auto !important;
    line-height: 1.6 !important;
    transition: border-color 0.2s !important;
}

.wp-core-ui .button:hover,
.wp-core-ui .button-secondary:hover {
    background: var(--ce-bg) !important;
    border-color: var(--ce-gold) !important;
    color: var(--ce-gold) !important;
}

/* Destructive button */
.wp-core-ui .button.button-link-delete,
a.submitdelete {
    color: var(--ce-error) !important;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}

.wp-core-ui .button.button-link-delete:hover,
a.submitdelete:hover {
    color: #DC2626 !important;
    text-decoration: underline !important;
}
```

- [ ] **Step 2: Verify in browser**

Go to `http://crowns-estates.local/wp-admin/post-new.php`. The Publish button should be gold. Go to `http://crowns-estates.local/wp-admin/edit.php` — the "Add New" button should be gold. Hover states should darken slightly.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — gold primary buttons, clean secondary buttons"
```

---

### Task 6: Form inputs

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Append input styles to `css/admin.css`**

```css
/* ── Form Inputs ─────────────────────────────────────────────────────────── */
.wp-admin input[type="text"],
.wp-admin input[type="email"],
.wp-admin input[type="url"],
.wp-admin input[type="password"],
.wp-admin input[type="number"],
.wp-admin input[type="search"],
.wp-admin textarea,
.wp-admin select {
    background: var(--ce-surface) !important;
    border: 1px solid var(--ce-border) !important;
    border-radius: 4px !important;
    color: var(--ce-text) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    box-shadow: none !important;
    padding: 8px 12px !important;
    transition: border-color 0.2s, box-shadow 0.2s !important;
}

.wp-admin input[type="text"]:focus,
.wp-admin input[type="email"]:focus,
.wp-admin input[type="url"]:focus,
.wp-admin input[type="password"]:focus,
.wp-admin input[type="number"]:focus,
.wp-admin input[type="search"]:focus,
.wp-admin textarea:focus,
.wp-admin select:focus {
    border-color: var(--ce-gold) !important;
    box-shadow: 0 0 0 2px rgba(196,151,58,0.15) !important;
    outline: none !important;
}

/* Labels */
.wp-admin label,
.wp-admin th.label label {
    font-family: 'Inter', sans-serif !important;
    font-weight: 500 !important;
    color: var(--ce-text) !important;
    font-size: 13px !important;
}

/* Search box in list tables */
.wp-admin .search-box input[type="search"] {
    width: 240px;
}

/* Checkbox and radio accent */
.wp-admin input[type="checkbox"],
.wp-admin input[type="radio"] {
    accent-color: var(--ce-gold);
}
```

- [ ] **Step 2: Verify in browser**

Go to `http://crowns-estates.local/wp-admin/post-new.php` and click on the title input — it should get a gold focus ring. Check `http://crowns-estates.local/wp-admin/admin.php?page=ce-enquiries` — the search input should also have the gold focus ring.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — clean inputs with gold focus ring"
```

---

### Task 7: Tables (WP List Tables)

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Append table styles to `css/admin.css`**

```css
/* ── WP List Tables ───────────────────────────────────────────────────────── */
.wp-list-table {
    background: var(--ce-surface) !important;
    border: 1px solid var(--ce-border) !important;
    border-radius: 6px !important;
    overflow: hidden;
    font-family: 'Inter', sans-serif !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04) !important;
}

/* Table header */
.wp-list-table thead th,
.wp-list-table thead td {
    background: var(--ce-bg) !important;
    border-bottom: 2px solid var(--ce-gold) !important;
    color: var(--ce-text) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    padding: 10px 12px !important;
}

/* Sort link in header */
.wp-list-table thead th a {
    color: var(--ce-text) !important;
}

.wp-list-table thead th.sorted a,
.wp-list-table thead th.sorting a {
    color: var(--ce-gold) !important;
}

/* Table rows */
.wp-list-table tbody tr {
    background: var(--ce-surface) !important;
    border-bottom: 1px solid var(--ce-border) !important;
    transition: background 0.15s;
}

.wp-list-table tbody tr:hover {
    background: rgba(196,151,58,0.04) !important;
}

.wp-list-table tbody tr.alternate {
    background: var(--ce-bg) !important;
}

.wp-list-table tbody tr.alternate:hover {
    background: rgba(196,151,58,0.04) !important;
}

/* Cell text */
.wp-list-table td,
.wp-list-table th.check-column {
    color: var(--ce-text) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    padding: 10px 12px !important;
}

/* Row action links (Edit, Trash, View) */
.wp-list-table .row-actions span a {
    color: var(--ce-text-muted) !important;
    font-size: 12px;
}

.wp-list-table .row-actions span a:hover {
    color: var(--ce-gold) !important;
}

.wp-list-table .row-actions .trash a,
.wp-list-table .row-actions .delete a {
    color: var(--ce-error) !important;
}

/* Post title link */
.wp-list-table .column-title a.row-title,
.wp-list-table strong a {
    color: var(--ce-text) !important;
    font-weight: 500 !important;
}

.wp-list-table .column-title a.row-title:hover,
.wp-list-table strong a:hover {
    color: var(--ce-gold) !important;
}

/* Tablenav (pagination + bulk actions) */
.tablenav {
    background: transparent !important;
    font-family: 'Inter', sans-serif !important;
}

.tablenav .tablenav-pages a,
.tablenav .tablenav-pages span.current {
    border-radius: 4px !important;
    font-size: 12px !important;
    font-family: 'Inter', sans-serif !important;
}

.tablenav .tablenav-pages span.current {
    background: var(--ce-gold) !important;
    border-color: var(--ce-gold) !important;
    color: #FFFFFF !important;
}

/* Bulk action select */
.tablenav .bulkactions select {
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
}

/* Status filter links (All | Published | Draft) */
.subsubsub a {
    color: var(--ce-text-muted) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 12px !important;
}

.subsubsub a.current {
    color: var(--ce-gold) !important;
    font-weight: 600 !important;
}
```

- [ ] **Step 2: Verify in browser**

Go to `http://crowns-estates.local/wp-admin/edit.php?post_type=ce_property`. The table header should have a gold bottom border, rows should highlight gold on hover. Check `http://crowns-estates.local/wp-admin/admin.php?page=ce-enquiries` for the enquiries table.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — list tables with gold header and hover states"
```

---

### Task 8: Notices and alerts

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Append notice styles to `css/admin.css`**

```css
/* ── Notices + Alerts ────────────────────────────────────────────────────── */
.notice,
div.updated,
div.error,
div.notice {
    background: var(--ce-surface) !important;
    border-left-width: 4px !important;
    border-radius: 0 4px 4px 0 !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    color: var(--ce-text) !important;
    padding: 12px 16px !important;
    margin: 8px 0 16px !important;
}

/* Info / general */
.notice-info,
div.updated,
.notice-warning {
    border-left-color: var(--ce-gold) !important;
}

/* Error */
.notice-error,
div.error {
    border-left-color: var(--ce-error) !important;
}

/* Success */
.notice-success {
    border-left-color: var(--ce-success) !important;
}

/* Notice dismiss button */
.notice .notice-dismiss::before {
    color: var(--ce-text-muted) !important;
}

.notice .notice-dismiss:hover::before {
    color: var(--ce-text) !important;
}

/* Notice links */
.notice a,
div.updated a {
    color: var(--ce-gold) !important;
}
```

- [ ] **Step 2: Verify in browser**

Go to `http://crowns-estates.local/wp-admin/options-general.php` and save settings — the green "Settings saved" notice should appear with a gold left border. Check for any error notice by typing a wrong value — it should show a red left border.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — notices with coloured left borders"
```

---

### Task 9: Post edit screen and metaboxes

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Append edit screen styles to `css/admin.css`**

```css
/* ── Post Edit Screen + Metaboxes ────────────────────────────────────────── */

/* Edit screen background */
.wp-admin.post-php #poststuff,
.wp-admin.post-new-php #poststuff {
    background: transparent;
    font-family: 'Inter', sans-serif !important;
}

/* Metabox container */
.postbox {
    background: var(--ce-surface) !important;
    border: 1px solid var(--ce-border) !important;
    border-radius: 6px !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04) !important;
    margin-bottom: 16px !important;
}

/* Metabox header */
.postbox .postbox-header,
.postbox h2.hndle,
.postbox h3.hndle {
    background: var(--ce-bg) !important;
    border-bottom: 1px solid var(--ce-border) !important;
    border-left: 3px solid var(--ce-gold) !important;
    border-radius: 6px 6px 0 0 !important;
    padding: 10px 14px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: var(--ce-text) !important;
}

.postbox .postbox-header .hndle,
.postbox h2.hndle span,
.postbox h3.hndle span {
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: var(--ce-text) !important;
}

/* Metabox content */
.postbox .inside {
    padding: 14px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    color: var(--ce-text) !important;
}

/* Publish metabox */
#submitdiv .postbox-header {
    border-left-color: var(--ce-gold) !important;
}

#submitdiv #misc-publishing-actions,
#submitdiv #major-publishing-actions {
    background: var(--ce-bg) !important;
    border-color: var(--ce-border) !important;
    padding: 10px 14px !important;
}

#submitdiv #publishing-action {
    float: right;
}

/* Post title input */
#title,
#titlewrap #title {
    font-family: 'Playfair Display', serif !important;
    font-size: 22px !important;
    font-weight: 600 !important;
    color: var(--ce-text) !important;
    border-color: var(--ce-border) !important;
    background: var(--ce-surface) !important;
    padding: 10px 14px !important;
    border-radius: 4px !important;
}

#title:focus {
    border-color: var(--ce-gold) !important;
    box-shadow: 0 0 0 2px rgba(196,151,58,0.15) !important;
}

/* ACF field groups */
.acf-postbox .postbox-header {
    border-left-color: var(--ce-gold) !important;
}

.acf-field .acf-label label {
    font-family: 'Inter', sans-serif !important;
    font-weight: 500 !important;
    color: var(--ce-text) !important;
    font-size: 13px !important;
}

.acf-field .acf-input input,
.acf-field .acf-input textarea,
.acf-field .acf-input select {
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    border-color: var(--ce-border) !important;
    border-radius: 4px !important;
    color: var(--ce-text) !important;
}

.acf-field .acf-input input:focus,
.acf-field .acf-input textarea:focus,
.acf-field .acf-input select:focus {
    border-color: var(--ce-gold) !important;
    box-shadow: 0 0 0 2px rgba(196,151,58,0.15) !important;
}
```

- [ ] **Step 2: Verify in browser**

Go to `http://crowns-estates.local/wp-admin/post-new.php?post_type=ce_property`. Metabox headers should have a gold left border. The title input should use Playfair Display. The Publish metabox should have a gold Publish button. If ACF Pro is installed, ACF field groups should also have the gold border.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — metaboxes, post edit screen, ACF fields"
```

---

### Task 10: ACF Options pages + final polish

**Files:**
- Modify: `wp-content/themes/crowns-estates/css/admin.css`

- [ ] **Step 1: Append ACF options + polish styles to `css/admin.css`**

```css
/* ── ACF Options Pages ───────────────────────────────────────────────────── */
.acf-options-page .acf-fields {
    background: var(--ce-surface) !important;
    border: 1px solid var(--ce-border) !important;
    border-radius: 6px !important;
}

.acf-field {
    border-color: var(--ce-border) !important;
}

.acf-field:first-child {
    border-top: none !important;
}

/* ACF tab navigation */
.acf-tab-wrap .acf-tab-group {
    border-color: var(--ce-border) !important;
    background: var(--ce-bg) !important;
}

.acf-tab-wrap .acf-tab-group li a {
    color: var(--ce-text-muted) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    border-color: var(--ce-border) !important;
}

.acf-tab-wrap .acf-tab-group li.active a {
    color: var(--ce-gold) !important;
    border-bottom-color: var(--ce-gold) !important;
    font-weight: 600 !important;
}

/* ── Dashboard Widget Polish ─────────────────────────────────────────────── */
#ce_dashboard_main .postbox-header {
    border-left-color: var(--ce-gold) !important;
}

/* ── Scrollbar (Webkit) ──────────────────────────────────────────────────── */
.wp-admin ::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.wp-admin ::-webkit-scrollbar-track {
    background: var(--ce-bg);
}

.wp-admin ::-webkit-scrollbar-thumb {
    background: var(--ce-border);
    border-radius: 3px;
}

.wp-admin ::-webkit-scrollbar-thumb:hover {
    background: var(--ce-gold);
}

/* ── Media Library ───────────────────────────────────────────────────────── */
.media-modal .media-frame-title h1 {
    font-family: 'Inter', sans-serif !important;
    font-size: 16px !important;
    color: var(--ce-text) !important;
}

/* ── Screen Options + Help tabs ─────────────────────────────────────────── */
#screen-options-link-wrap a,
#contextual-help-link-wrap a {
    font-family: 'Inter', sans-serif !important;
    font-size: 12px !important;
    color: var(--ce-text-muted) !important;
    background: var(--ce-surface) !important;
    border-color: var(--ce-border) !important;
}

#screen-options-link-wrap a:hover,
#contextual-help-link-wrap a:hover {
    color: var(--ce-gold) !important;
    border-color: var(--ce-gold) !important;
}
```

- [ ] **Step 2: Verify in browser**

If ACF Pro is installed, go to `http://crowns-estates.local/wp-admin/admin.php?page=ce-site-settings`. Fields should have clean styling with gold focus states and borders. Check the custom scrollbar appears on hover in any scrollable area.

- [ ] **Step 3: Final check — visit all key screens**

| Screen | URL | What to verify |
|---|---|---|
| Dashboard | `/wp-admin/` | Stats widget, dark sidebar, white content area |
| Properties list | `/wp-admin/edit.php?post_type=ce_property` | Gold table header, hover states |
| Add Property | `/wp-admin/post-new.php?post_type=ce_property` | Metaboxes, gold Publish button |
| Enquiries | `/wp-admin/admin.php?page=ce-enquiries` | Table, search, status tabs |
| Site Settings | `/wp-admin/admin.php?page=ce-site-settings` | ACF fields, gold focus |
| Login page | `/wp-login.php` | Confirm login page still looks correct |

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/css/admin.css
git commit -m "feat: admin reskin — ACF options polish, scrollbar, final touches"
```

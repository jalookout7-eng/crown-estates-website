# Crowns Estates Admin UI Reskin — Design Spec

## Goal

Reskin the entire WordPress admin interface with Crowns Estates branding — white/off-white background, dark sidebar, gold accents, Inter typography. No functionality changes. Visual only.

## Colour Palette

| Token | Value | Usage |
|---|---|---|
| Background | `#FAFAFA` | All admin page backgrounds |
| Surface | `#FFFFFF` | Cards, panels, table rows |
| Sidebar | `#1A1A1A` | Left navigation (already dark) |
| Top bar | `#FFFFFF` | Admin bar background |
| Gold | `#C4973A` | Buttons, active states, highlights, borders |
| Gold hover | `#B8892E` | Button hover state |
| Text primary | `#0A0A0A` | Headings, body text |
| Text secondary | `#666666` | Labels, meta text |
| Border | `#E8E8E8` | Input borders, table dividers |
| Error | `#EF4444` | Error notices |
| Success | `#22C55E` | Success notices |

## Typography

- Font: **Inter** (300, 400, 500, 600) — loaded via Google Fonts in admin
- Base size: 13px (matches WP admin default)
- Headings: Playfair Display for page titles only

## Scope — What Gets Styled

### 1. Admin Bar (top bar)
- White background, `#E8E8E8` bottom border
- Gold highlight on hover states
- "Howdy, [name]" text in gold
- WP logo replaced with CE wordmark text

### 2. Sidebar Navigation
- Background stays `#1A1A1A` (already set)
- Active menu item: gold background `#C4973A`, white text
- Hover state: `rgba(196,151,58,0.15)` gold tint
- Submenu background: `#111111`
- All sidebar text: `#CCCCCC`, active: `#FFFFFF`

### 3. Page Background + Wrap
- `#FAFAFA` background across all admin screens
- `.wrap` headings: Inter 600, `#0A0A0A`
- Page title underline: gold `2px` border

### 4. Buttons
- Primary (`.button-primary`): gold background, white text, no text-shadow
- Secondary (`.button`): white background, `#E8E8E8` border, dark text
- Hover: gold hover colour with subtle shadow
- Destructive: red outline style

### 5. Form Inputs
- White background, `#E8E8E8` border, 4px radius
- Focus: gold border + `rgba(196,151,58,0.15)` glow
- Labels: Inter 500, `#0A0A0A`

### 6. Tables (WP List Tables)
- Header row: `#FAFAFA` background, gold bottom border
- Row hover: `rgba(196,151,58,0.04)` tint
- Alternating rows: subtle `#FAFAFA` / `#FFFFFF`
- Action links (Edit, Delete): gold on hover

### 7. Notices + Alerts
- `.notice-info` / `.updated`: gold left border, white background
- `.notice-error`: red left border
- `.notice-success`: green left border
- Remove default blue WordPress notice colour

### 8. Post Edit Screen
- Metabox headers: Inter 600, gold left border
- Publish metabox: gold "Publish" button
- ACF field groups: clean white panels

### 9. Admin Footer
- Already set to "Crowns Estates Admin Panel — Built by 3D Visual Pro"
- Style: `#999`, centered, Inter 12px

## File Structure

| File | Purpose |
|---|---|
| `inc/admin-styles.php` | Hooks: enqueue CSS + load Google Fonts in admin |
| `css/admin.css` | All admin styles |

`admin-styles.php` is required from `functions.php`.

## What Does NOT Change

- WordPress admin functionality — no JS behaviour changes
- The enquiries page and dashboard widget (already styled, will naturally inherit)
- ACF field UI beyond colour tokens
- WordPress core files (no core edits)

## Implementation Notes

- All styles go in `css/admin.css`, enqueued via `admin_enqueue_scripts`
- Use high-specificity selectors where needed to override WP defaults
- Inter font loaded via `admin_enqueue_scripts` (separate from front-end enqueue)
- Test on: Dashboard, Posts list, Property list, Enquiries page, Site Settings (ACF)

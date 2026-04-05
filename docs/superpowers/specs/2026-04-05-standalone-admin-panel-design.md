# Crowns Estates Standalone Admin Panel — Design Spec

## Goal

A standalone Next.js 14 admin panel at `crownsestates.co.uk/admin` that connects to the existing WordPress REST API. Independent of WordPress admin. White/gold Crowns Estates branding throughout.

## Architecture

### Tech Stack
- **Framework:** Next.js 14 (App Router)
- **Styling:** Tailwind CSS
- **Auth:** WordPress Application Passwords (WP built-in, no extra plugins)
- **Data:** Fetch against existing CE REST API (`/wp-json/ce/v1/*`) and WP REST API (`/wp-json/wp/v2/*`)
- **Location:** `admin-panel/` directory inside the WordPress project root

### Folder Structure

```
admin-panel/
├── app/
│   ├── layout.tsx              # Root layout — font loading, global styles
│   ├── page.tsx                # Redirects to /login or /dashboard
│   ├── login/
│   │   └── page.tsx            # Login page
│   ├── dashboard/
│   │   └── page.tsx            # Dashboard overview
│   ├── enquiries/
│   │   └── page.tsx            # Enquiries table
│   ├── properties/
│   │   └── page.tsx            # Properties list
│   ├── testimonials/
│   │   └── page.tsx            # Testimonials list
│   └── settings/
│       └── page.tsx            # Settings (admin only)
├── components/
│   ├── Sidebar.tsx             # Left navigation
│   ├── TopBar.tsx              # Top header bar
│   ├── StatCard.tsx            # Dashboard stat card
│   ├── DataTable.tsx           # Reusable table component
│   └── ProtectedLayout.tsx     # Wraps authenticated pages, checks cookie
├── lib/
│   ├── auth.ts                 # login(), logout(), getSession(), isAdmin()
│   └── api.ts                  # fetchWP() — authenticated fetch wrapper
├── middleware.ts               # Redirect unauthenticated users to /login
├── next.config.js              # basePath: '/admin'
├── tailwind.config.js
└── package.json
```

### Base Path

Next.js configured with `basePath: '/admin'` so all routes are prefixed `/admin/*` automatically.

---

## Authentication

### How it works

1. User enters WP **username** and **application password** on the login page
   - Application passwords are created in WP Admin → Users → Profile → Application Passwords
2. Next.js server action encodes `username:apppassword` as Base64
3. Validates by calling `GET /wp-json/wp/v2/users/me` with `Authorization: Basic <encoded>`
4. If valid, stores the encoded credentials in an **httpOnly cookie** (`ce_admin_token`, 24h expiry)
5. Also fetches user capabilities to determine role (admin vs editor)
6. Stores role in a second cookie (`ce_admin_role`)

### Role detection

- After login, check `GET /wp-json/wp/v2/users/me?context=edit` — if response includes `capabilities.manage_options: true` → **Admin**
- Otherwise → **Editor**
- Admin sees all pages including Settings
- Editor sees Dashboard, Enquiries, Properties, Testimonials — Settings hidden and route-protected

### Session validation

- `middleware.ts` runs on every request to `/admin/*` (except `/admin/login`)
- Checks `ce_admin_token` cookie exists
- If missing → redirect to `/admin/login`
- Full credential re-validation happens on each server component render via `lib/auth.ts`

### Logout

- Clears `ce_admin_token` and `ce_admin_role` cookies
- Redirects to `/admin/login`

---

## Pages

### Login (`/admin/login`)

- Crowns Estates logo (Playfair Display wordmark) centred
- Username and Application Password fields
- Submit button — calls server action → validates → sets cookies → redirects to `/admin`
- Error message on invalid credentials
- Helper text explaining where to find application passwords in WP

### Dashboard (`/admin`)

- 4 stat cards: Total Properties, Active Listings, Total Enquiries (with "X new" badge), Total Property Value
- Line chart — enquiries over last 30 days (using Chart.js or Recharts)
- Source breakdown table (Register Interest, Contact Form, Brochure Gate)
- Quick action links: Add Property (links to WP admin), View Enquiries, Export CSV

**API calls:**
- `GET /wp-json/wp/v2/posts?post_type=ce_property&per_page=1` — total properties
- `GET /wp-json/ce/v1/enquiries?per_page=1` — total + new count from headers
- `GET /wp-json/ce/v1/enquiries?per_page=100` — for chart + source data

### Enquiries (`/admin/enquiries`)

- Status filter tabs: All, New, Read, Replied, Archived (with counts)
- Search by name or email
- Paginated table: Name, Email, Phone, Source, Property, Status, Date
- Click row to expand message + GDPR/IP details
- Inline status dropdown — updates via `POST /wp-json/ce/v1/enquiries/{id}`
- Export CSV button (admin only)

**API calls:**
- `GET /wp-json/ce/v1/enquiries` with `status`, `search`, `page`, `per_page` params

### Properties (`/admin/properties`)

- Table: Title, City, Price From, Status, Date
- Click row title → opens WP admin edit screen in new tab
- "Add New" button → links to WP admin add new property screen
- No inline editing (WP admin handles that via ACF fields)

**API calls:**
- `GET /wp-json/wp/v2/ce_property?per_page=20&page=N`

### Testimonials (`/admin/testimonials`)

- Table: Client Name, Quote (truncated), Rating, Featured, Date
- Click row → opens WP admin edit screen in new tab
- "Add New" button → links to WP admin

**API calls:**
- `GET /wp-json/wp/v2/ce_testimonial?per_page=20&page=N`

### Settings (`/admin/settings`) — Admin only

Three sections matching ACF options groups:

**Exchange Rates**
- GBP to SAR rate (number input)
- GBP to USD rate (number input)
- Rates Last Updated (date)

**Calculator**
- Registration Fee %
- VAT %
- Agency Fee %

**Email**
- Admin Notification Email
- Digest Recipient Email
- Digest Enabled (toggle)
- Digest Time (text)
- Email From Name
- Email From Address
- Email Reply-To

Save button per section — updates via `POST /wp-json/wp/v2/settings` (requires ACF REST API support) or direct ACF options endpoint.

**Note:** ACF Pro exposes options fields via `GET/POST /wp-json/acf/v3/options/options`. Use this endpoint for settings reads/writes.

---

## UI Design

### Colour Palette (matches WP admin reskin)

| Token | Value |
|---|---|
| Background | `#FAFAFA` |
| Surface | `#FFFFFF` |
| Sidebar | `#1A1A1A` |
| Gold | `#C4973A` |
| Gold hover | `#B8892E` |
| Text primary | `#0A0A0A` |
| Text muted | `#666666` |
| Border | `#E8E8E8` |
| Error | `#EF4444` |
| Success | `#22C55E` |

### Layout

- **Sidebar** (fixed, left, 220px wide) — dark `#1A1A1A`, Crowns Estates wordmark at top, nav links with gold active state
- **Top bar** (fixed, top) — white, page title left, user name + logout right
- **Content area** — `#FAFAFA` background, white cards/panels

### Typography

- Inter (300, 400, 500, 600) — body, labels, nav
- Playfair Display (600) — page headings, logo

---

## What This Does NOT Include

- Rich text / WYSIWYG editing (use WP admin for post content)
- Media library (use WP admin)
- User management (use WP admin)
- Plugin/theme management (use WP admin)

The panel is for **content operations and monitoring only** — enquiries, listings overview, settings. Deep editing stays in WP admin.

---

## Deployment Notes

- In development: `cd admin-panel && npm run dev` → `localhost:3000/admin`
- In production: build with `npm run build && npm start` — serve behind Nginx/Apache with proxy pass from `/admin` to the Next.js port
- WordPress site URL and REST base URL stored in `.env.local` as `NEXT_PUBLIC_WP_URL`

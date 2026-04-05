# Crowns Estates Standalone Admin Panel Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a standalone Next.js 14 admin panel at `/admin` that connects to the WordPress REST API for managing enquiries, properties, testimonials, and site settings.

**Architecture:** Next.js 14 App Router app in `admin-panel/` directory. Auth via WordPress Application Passwords stored in httpOnly cookies. All data fetched server-side from the existing CE REST API. Tailwind CSS with Crowns Estates white/gold branding.

**Tech Stack:** Next.js 14, TypeScript, Tailwind CSS, WordPress REST API, httpOnly cookies

---

## File Map

| File | Action | Purpose |
|---|---|---|
| `admin-panel/package.json` | Create | Dependencies |
| `admin-panel/next.config.js` | Create | basePath: '/admin' |
| `admin-panel/tailwind.config.js` | Create | CE colour tokens |
| `admin-panel/tsconfig.json` | Create | TypeScript config |
| `admin-panel/.env.local` | Create | WP_URL env var |
| `admin-panel/app/globals.css` | Create | Tailwind base + font import |
| `admin-panel/app/layout.tsx` | Create | Root layout with fonts |
| `admin-panel/app/page.tsx` | Create | Root redirect to /dashboard |
| `admin-panel/lib/auth.ts` | Create | login(), logout(), getSession(), isAdmin() |
| `admin-panel/lib/api.ts` | Create | fetchWP() authenticated fetch wrapper |
| `admin-panel/middleware.ts` | Create | Cookie-based route protection |
| `admin-panel/app/login/page.tsx` | Create | Login form with server action |
| `admin-panel/components/Sidebar.tsx` | Create | Dark sidebar with gold active states |
| `admin-panel/components/TopBar.tsx` | Create | White top bar + logout |
| `admin-panel/components/ProtectedLayout.tsx` | Create | Wraps all authenticated pages |
| `admin-panel/components/StatCard.tsx` | Create | Dashboard stat card |
| `admin-panel/app/dashboard/page.tsx` | Create | Stats + chart + quick actions |
| `admin-panel/components/EnquiriesTable.tsx` | Create | Client component — filters + status update |
| `admin-panel/app/enquiries/page.tsx` | Create | Enquiries page wrapper |
| `admin-panel/app/properties/page.tsx` | Create | Properties list |
| `admin-panel/app/testimonials/page.tsx` | Create | Testimonials list |
| `admin-panel/app/settings/page.tsx` | Create | Settings form (admin only) |

---

### Task 1: Scaffold the Next.js project

**Files:**
- Create: `admin-panel/package.json`
- Create: `admin-panel/next.config.js`
- Create: `admin-panel/tailwind.config.js`
- Create: `admin-panel/tsconfig.json`
- Create: `admin-panel/.env.local`
- Create: `admin-panel/postcss.config.js`

- [ ] **Step 1: Create `admin-panel/package.json`**

```json
{
  "name": "ce-admin-panel",
  "version": "1.0.0",
  "private": true,
  "scripts": {
    "dev": "next dev",
    "build": "next build",
    "start": "next start"
  },
  "dependencies": {
    "next": "14.2.3",
    "react": "^18",
    "react-dom": "^18",
    "recharts": "^2.12.2"
  },
  "devDependencies": {
    "typescript": "^5",
    "@types/node": "^20",
    "@types/react": "^18",
    "@types/react-dom": "^18",
    "tailwindcss": "^3.4.1",
    "autoprefixer": "^10.4.19",
    "postcss": "^8.4.38"
  }
}
```

- [ ] **Step 2: Create `admin-panel/next.config.js`**

```js
/** @type {import('next').NextConfig} */
const nextConfig = {
  basePath: '/admin',
};
module.exports = nextConfig;
```

- [ ] **Step 3: Create `admin-panel/tailwind.config.js`**

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/**/*.{ts,tsx}', './components/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        gold:    '#C4973A',
        'gold-hover': '#B8892E',
        sidebar: '#1A1A1A',
        ce:      '#0A0A0A',
        muted:   '#666666',
        border:  '#E8E8E8',
        surface: '#FFFFFF',
        bg:      '#FAFAFA',
      },
      fontFamily: {
        sans:    ['Inter', 'sans-serif'],
        display: ['"Playfair Display"', 'serif'],
      },
    },
  },
  plugins: [],
};
```

- [ ] **Step 4: Create `admin-panel/tsconfig.json`**

```json
{
  "compilerOptions": {
    "target": "ES2017",
    "lib": ["dom", "dom.iterable", "esnext"],
    "allowJs": true,
    "skipLibCheck": true,
    "strict": true,
    "noEmit": true,
    "esModuleInterop": true,
    "module": "esnext",
    "moduleResolution": "bundler",
    "resolveJsonModule": true,
    "isolatedModules": true,
    "jsx": "preserve",
    "incremental": true,
    "plugins": [{ "name": "next" }],
    "paths": { "@/*": ["./*"] }
  },
  "include": ["next-env.d.ts", "**/*.ts", "**/*.tsx", ".next/types/**/*.ts"],
  "exclude": ["node_modules"]
}
```

- [ ] **Step 5: Create `admin-panel/postcss.config.js`**

```js
module.exports = {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};
```

- [ ] **Step 6: Create `admin-panel/.env.local`**

```
NEXT_PUBLIC_WP_URL=http://crowns-estates.local
```

- [ ] **Step 7: Install dependencies**

```bash
cd /Users/farhanraiss/crown-estates-website/admin-panel && npm install
```

Expected: `node_modules/` created, no errors.

- [ ] **Step 8: Create `admin-panel/app/globals.css`**

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600&display=swap');

* { box-sizing: border-box; }
body { background: #FAFAFA; font-family: 'Inter', sans-serif; }
```

- [ ] **Step 9: Create `admin-panel/app/layout.tsx`**

```tsx
import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
  title: 'Crowns Estates Admin',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
```

- [ ] **Step 10: Create `admin-panel/app/page.tsx`**

```tsx
import { redirect } from 'next/navigation';

export default function RootPage() {
  redirect('/dashboard');
}
```

- [ ] **Step 11: Verify scaffold runs**

```bash
cd /Users/farhanraiss/crown-estates-website/admin-panel && npm run dev
```

Expected: Next.js starts on `http://localhost:3000`. Visit `http://localhost:3000/admin` — you should see a redirect attempt (will fail without middleware yet, that's fine).

- [ ] **Step 12: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/
git commit -m "feat: scaffold Next.js 14 admin panel with Tailwind and CE config"
```

---

### Task 2: Auth library

**Files:**
- Create: `admin-panel/lib/auth.ts`
- Create: `admin-panel/lib/api.ts`

- [ ] **Step 1: Create `admin-panel/lib/auth.ts`**

```ts
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL!;

export interface Session {
  token: string;
  username: string;
  role: 'admin' | 'editor';
}

export async function getSession(): Promise<Session | null> {
  const store = cookies();
  const token    = store.get('ce_admin_token')?.value;
  const username = store.get('ce_admin_username')?.value;
  const role     = store.get('ce_admin_role')?.value as 'admin' | 'editor' | undefined;
  if (!token || !username || !role) return null;
  return { token, username, role };
}

export async function requireSession(): Promise<Session> {
  const session = await getSession();
  if (!session) redirect('/login');
  return session;
}

export async function requireAdmin(): Promise<Session> {
  const session = await requireSession();
  if (session.role !== 'admin') redirect('/dashboard');
  return session;
}

export async function login(username: string, appPassword: string): Promise<{ error?: string }> {
  const token = Buffer.from(`${username}:${appPassword}`).toString('base64');

  const res = await fetch(`${WP_URL}/wp-json/wp/v2/users/me?context=edit`, {
    headers: { Authorization: `Basic ${token}` },
    cache: 'no-store',
  });

  if (!res.ok) {
    return { error: 'Invalid username or application password.' };
  }

  const user = await res.json();
  const role: 'admin' | 'editor' = user.capabilities?.manage_options ? 'admin' : 'editor';

  const cookieOpts = {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax' as const,
    maxAge: 86400,
    path: '/',
  };

  const store = cookies();
  store.set('ce_admin_token',    token,       cookieOpts);
  store.set('ce_admin_username', user.name,   cookieOpts);
  store.set('ce_admin_role',     role,        cookieOpts);

  return {};
}

export async function logout(): Promise<void> {
  const store = cookies();
  store.delete('ce_admin_token');
  store.delete('ce_admin_username');
  store.delete('ce_admin_role');
}

export function isAdmin(session: Session): boolean {
  return session.role === 'admin';
}
```

- [ ] **Step 2: Create `admin-panel/lib/api.ts`**

```ts
import { getSession } from './auth';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL!;

export async function fetchWP(path: string, options: RequestInit = {}): Promise<Response> {
  const session = await getSession();
  if (!session) throw new Error('Not authenticated');

  return fetch(`${WP_URL}/wp-json${path}`, {
    ...options,
    headers: {
      Authorization: `Basic ${session.token}`,
      'Content-Type': 'application/json',
      ...(options.headers as Record<string, string> ?? {}),
    },
    cache: 'no-store',
  });
}
```

- [ ] **Step 3: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/lib/
git commit -m "feat: admin panel — auth library and fetchWP wrapper"
```

---

### Task 3: Middleware

**Files:**
- Create: `admin-panel/middleware.ts`

- [ ] **Step 1: Create `admin-panel/middleware.ts`**

```ts
import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(request: NextRequest) {
  const token = request.cookies.get('ce_admin_token')?.value;
  const { pathname } = request.nextUrl;

  const isLoginPage = pathname === '/login' || pathname === '/login/';

  if (!token && !isLoginPage) {
    const loginUrl = new URL('/login', request.url);
    return NextResponse.redirect(loginUrl);
  }

  if (token && isLoginPage) {
    const dashUrl = new URL('/dashboard', request.url);
    return NextResponse.redirect(dashUrl);
  }

  return NextResponse.next();
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|favicon.ico|api).*)'],
};
```

- [ ] **Step 2: Verify middleware redirects**

With the dev server running (`npm run dev` in `admin-panel/`), visit `http://localhost:3000/admin/dashboard`.

Expected: Redirected to `http://localhost:3000/admin/login` (even though the login page doesn't exist yet — you'll see a 404, which proves the middleware is running).

- [ ] **Step 3: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/middleware.ts
git commit -m "feat: admin panel — middleware route protection"
```

---

### Task 4: Login page

**Files:**
- Create: `admin-panel/app/login/page.tsx`

- [ ] **Step 1: Create `admin-panel/app/login/page.tsx`**

```tsx
'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';

export default function LoginPage() {
  const router = useRouter();
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setError('');
    setLoading(true);

    const form = new FormData(e.currentTarget);
    const username    = form.get('username') as string;
    const appPassword = form.get('appPassword') as string;

    const res = await fetch('/admin/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, appPassword }),
    });

    const data = await res.json();
    setLoading(false);

    if (data.error) {
      setError(data.error);
    } else {
      router.push('/admin/dashboard');
      router.refresh();
    }
  }

  return (
    <div className="min-h-screen bg-ce flex items-center justify-center" style={{ background: '#0A0A0A' }}>
      <div className="w-full max-w-sm">
        {/* Logo */}
        <div className="text-center mb-8">
          <h1 className="font-display text-3xl font-semibold text-gold tracking-wide">
            Crowns Estates
          </h1>
          <div className="mx-auto mt-2 w-10 h-0.5 bg-gold" />
          <p className="mt-3 text-sm text-gray-400">Admin Panel</p>
        </div>

        {/* Card */}
        <div className="bg-[#1a1a1a] border border-[#2a2a2a] rounded-lg p-8 shadow-2xl">
          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-xs font-medium uppercase tracking-widest text-gray-400 mb-1.5">
                Username
              </label>
              <input
                name="username"
                type="text"
                required
                autoComplete="username"
                className="w-full bg-[#0f0f0f] border border-[#333] rounded text-white text-sm px-3 py-2.5 focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold/20"
              />
            </div>

            <div>
              <label className="block text-xs font-medium uppercase tracking-widest text-gray-400 mb-1.5">
                Application Password
              </label>
              <input
                name="appPassword"
                type="password"
                required
                autoComplete="current-password"
                className="w-full bg-[#0f0f0f] border border-[#333] rounded text-white text-sm px-3 py-2.5 focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold/20"
              />
            </div>

            {error && (
              <p className="text-red-400 text-sm border-l-2 border-red-400 pl-3">{error}</p>
            )}

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-gold hover:bg-gold-hover text-white text-sm font-medium uppercase tracking-wider py-2.5 rounded transition-colors disabled:opacity-60"
            >
              {loading ? 'Signing in…' : 'Sign In'}
            </button>
          </form>

          <p className="mt-6 text-xs text-gray-500 text-center">
            Use your WP username and an Application Password.<br />
            Create one in <strong className="text-gray-400">WP Admin → Users → Profile → Application Passwords</strong>.
          </p>
        </div>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Create `admin-panel/app/api/login/route.ts`**

This API route handles login so it can set cookies properly.

```ts
import { NextRequest, NextResponse } from 'next/server';
import { login } from '@/lib/auth';

export async function POST(req: NextRequest) {
  const { username, appPassword } = await req.json();

  if (!username || !appPassword) {
    return NextResponse.json({ error: 'Username and password are required.' }, { status: 400 });
  }

  const result = await login(username, appPassword);

  if (result.error) {
    return NextResponse.json({ error: result.error }, { status: 401 });
  }

  return NextResponse.json({ success: true });
}
```

- [ ] **Step 3: Create `admin-panel/app/api/logout/route.ts`**

```ts
import { NextResponse } from 'next/server';
import { logout } from '@/lib/auth';

export async function POST() {
  await logout();
  return NextResponse.json({ success: true });
}
```

- [ ] **Step 4: Verify login page**

With dev server running, visit `http://localhost:3000/admin/login`.

Expected: Dark login page with Crowns Estates gold wordmark, username and application password fields.

- [ ] **Step 5: Test login flow**

In WP Admin, go to **Users → Profile → Application Passwords**. Create one named "Admin Panel". Copy the generated password.

Enter your WP username and the application password on the login page. Click Sign In.

Expected: Redirects to `http://localhost:3000/admin/dashboard` (404 is fine — the page doesn't exist yet).

- [ ] **Step 6: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/app/login/ admin-panel/app/api/
git commit -m "feat: admin panel — login page and auth API routes"
```

---

### Task 5: Layout components

**Files:**
- Create: `admin-panel/components/Sidebar.tsx`
- Create: `admin-panel/components/TopBar.tsx`
- Create: `admin-panel/components/ProtectedLayout.tsx`

- [ ] **Step 1: Create `admin-panel/components/Sidebar.tsx`**

```tsx
'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';

const NAV = [
  { href: '/dashboard',    label: 'Dashboard',    icon: '⊞' },
  { href: '/enquiries',    label: 'Enquiries',    icon: '✉' },
  { href: '/properties',   label: 'Properties',   icon: '⌂' },
  { href: '/testimonials', label: 'Testimonials', icon: '★' },
];

interface SidebarProps {
  role: 'admin' | 'editor';
}

export default function Sidebar({ role }: SidebarProps) {
  const pathname = usePathname();

  return (
    <aside className="fixed left-0 top-0 h-screen w-[220px] bg-sidebar flex flex-col z-20">
      {/* Wordmark */}
      <div className="px-6 py-6 border-b border-[#2a2a2a]">
        <span className="font-display text-lg font-semibold text-gold tracking-wide">
          Crowns Estates
        </span>
        <div className="mt-1 w-8 h-px bg-gold opacity-60" />
      </div>

      {/* Nav */}
      <nav className="flex-1 py-4 px-3 space-y-0.5">
        {NAV.map(({ href, label, icon }) => {
          const active = pathname === href || pathname.startsWith(href + '/');
          return (
            <Link
              key={href}
              href={href}
              className={`flex items-center gap-3 px-3 py-2.5 rounded text-sm transition-colors ${
                active
                  ? 'bg-gold text-white font-medium'
                  : 'text-[#ccc] hover:bg-gold/15 hover:text-white'
              }`}
            >
              <span className="text-base leading-none">{icon}</span>
              {label}
            </Link>
          );
        })}

        {role === 'admin' && (
          <Link
            href="/settings"
            className={`flex items-center gap-3 px-3 py-2.5 rounded text-sm transition-colors ${
              pathname === '/settings'
                ? 'bg-gold text-white font-medium'
                : 'text-[#ccc] hover:bg-gold/15 hover:text-white'
            }`}
          >
            <span className="text-base leading-none">⚙</span>
            Settings
          </Link>
        )}
      </nav>

      {/* Version */}
      <div className="px-6 py-4 border-t border-[#2a2a2a]">
        <span className="text-[11px] text-[#555]">Crowns Estates v1.0</span>
      </div>
    </aside>
  );
}
```

- [ ] **Step 2: Create `admin-panel/components/TopBar.tsx`**

```tsx
'use client';

import { useRouter } from 'next/navigation';

interface TopBarProps {
  title: string;
  username: string;
}

export default function TopBar({ title, username }: TopBarProps) {
  const router = useRouter();

  async function handleLogout() {
    await fetch('/admin/api/logout', { method: 'POST' });
    router.push('/admin/login');
    router.refresh();
  }

  return (
    <header className="fixed top-0 left-[220px] right-0 h-14 bg-surface border-b border-border flex items-center justify-between px-6 z-10">
      <h1 className="font-display text-lg font-semibold text-ce">{title}</h1>
      <div className="flex items-center gap-4">
        <span className="text-sm text-muted">{username}</span>
        <button
          onClick={handleLogout}
          className="text-sm text-muted hover:text-gold transition-colors"
        >
          Sign out
        </button>
      </div>
    </header>
  );
}
```

- [ ] **Step 3: Create `admin-panel/components/ProtectedLayout.tsx`**

```tsx
import { requireSession } from '@/lib/auth';
import Sidebar from './Sidebar';
import TopBar from './TopBar';

interface ProtectedLayoutProps {
  children: React.ReactNode;
  title: string;
}

export default async function ProtectedLayout({ children, title }: ProtectedLayoutProps) {
  const session = await requireSession();

  return (
    <div className="min-h-screen bg-bg">
      <Sidebar role={session.role} />
      <TopBar title={title} username={session.username} />
      <main className="ml-[220px] pt-14 min-h-screen">
        <div className="p-6">{children}</div>
      </main>
    </div>
  );
}
```

- [ ] **Step 4: Create `admin-panel/components/StatCard.tsx`**

```tsx
interface StatCardProps {
  label: string;
  value: string | number;
  accent?: string;
  badge?: string;
}

export default function StatCard({ label, value, accent = '#C4973A', badge }: StatCardProps) {
  return (
    <div
      className="bg-surface border border-border rounded-lg p-5 shadow-sm"
      style={{ borderTop: `3px solid ${accent}` }}
    >
      <div className="text-2xl font-bold text-ce flex items-center gap-2">
        {value}
        {badge && (
          <span className="text-xs font-medium px-2 py-0.5 rounded bg-red-50 text-red-700">
            {badge}
          </span>
        )}
      </div>
      <div className="text-xs text-muted mt-1">{label}</div>
    </div>
  );
}
```

- [ ] **Step 5: Verify components compile**

```bash
cd /Users/farhanraiss/crown-estates-website/admin-panel && npm run build 2>&1 | tail -20
```

Expected: Build completes (some pages may show "no default export" errors — ignore for now, they'll be filled in later tasks).

- [ ] **Step 6: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/components/
git commit -m "feat: admin panel — Sidebar, TopBar, ProtectedLayout, StatCard components"
```

---

### Task 6: Dashboard page

**Files:**
- Create: `admin-panel/app/dashboard/page.tsx`
- Create: `admin-panel/components/EnquiriesChart.tsx`

- [ ] **Step 1: Create `admin-panel/components/EnquiriesChart.tsx`**

```tsx
'use client';

import { LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer } from 'recharts';

interface ChartDataPoint {
  day: string;
  count: number;
}

export default function EnquiriesChart({ data }: { data: ChartDataPoint[] }) {
  return (
    <ResponsiveContainer width="100%" height={80}>
      <LineChart data={data}>
        <XAxis dataKey="day" hide />
        <YAxis hide />
        <Tooltip
          contentStyle={{ background: '#fff', border: '1px solid #E8E8E8', borderRadius: 4, fontSize: 12 }}
        />
        <Line
          type="monotone"
          dataKey="count"
          stroke="#C4973A"
          strokeWidth={2}
          dot={false}
          activeDot={{ r: 4, fill: '#C4973A' }}
        />
      </LineChart>
    </ResponsiveContainer>
  );
}
```

- [ ] **Step 2: Create `admin-panel/app/dashboard/page.tsx`**

```tsx
import ProtectedLayout from '@/components/ProtectedLayout';
import StatCard from '@/components/StatCard';
import EnquiriesChart from '@/components/EnquiriesChart';
import { fetchWP } from '@/lib/api';
import { getSession } from '@/lib/auth';

interface Enquiry {
  id: number;
  source: string;
  status: string;
  created_at: string;
}

async function getDashboardData() {
  const [propsRes, enquiriesRes, allEnquiriesRes] = await Promise.all([
    fetchWP('/wp/v2/ce_property?per_page=1&status=publish'),
    fetchWP('/ce/v1/enquiries?per_page=1'),
    fetchWP('/ce/v1/enquiries?per_page=100'),
  ]);

  const totalProperties = parseInt(propsRes.headers.get('X-WP-Total') ?? '0');
  const totalEnquiries  = parseInt(enquiriesRes.headers.get('X-WP-Total') ?? '0');
  const allEnquiries: Enquiry[] = await allEnquiriesRes.json();

  const newCount = allEnquiries.filter(e => e.status === 'new').length;

  // Source breakdown
  const sources: Record<string, number> = {};
  allEnquiries.forEach(e => { sources[e.source] = (sources[e.source] ?? 0) + 1; });

  // 30-day sparkline
  const dayCounts: Record<string, number> = {};
  const today = new Date();
  for (let i = 29; i >= 0; i--) {
    const d = new Date(today);
    d.setDate(d.getDate() - i);
    dayCounts[d.toISOString().split('T')[0]] = 0;
  }
  allEnquiries.forEach(e => {
    const day = e.created_at.split(' ')[0];
    if (day in dayCounts) dayCounts[day]++;
  });
  const chartData = Object.entries(dayCounts).map(([day, count]) => ({ day, count }));

  return { totalProperties, totalEnquiries, newCount, sources, chartData };
}

export default async function DashboardPage() {
  const session = await import('@/lib/auth').then(m => m.getSession());
  const { totalProperties, totalEnquiries, newCount, sources, chartData } = await getDashboardData();

  const WP_URL = process.env.NEXT_PUBLIC_WP_URL ?? '';

  return (
    <ProtectedLayout title="Dashboard">
      {/* Stat cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <StatCard label="Total Properties"  value={totalProperties} accent="#C4973A" />
        <StatCard label="Active Listings"   value={totalProperties} accent="#22C55E" />
        <StatCard
          label="Total Enquiries"
          value={totalEnquiries}
          accent="#3B82F6"
          badge={newCount > 0 ? `${newCount} new` : undefined}
        />
        <StatCard label="Enquiries (New)"   value={newCount} accent="#F59E0B" />
      </div>

      {/* Chart + source + actions */}
      <div className="grid grid-cols-1 xl:grid-cols-3 gap-4">
        {/* Sparkline */}
        <div className="xl:col-span-1 bg-surface border border-border rounded-lg p-5 shadow-sm">
          <p className="text-xs font-semibold uppercase tracking-wider text-muted mb-3">
            Enquiries — Last 30 Days
          </p>
          <EnquiriesChart data={chartData} />
        </div>

        {/* Source breakdown */}
        <div className="bg-surface border border-border rounded-lg p-5 shadow-sm">
          <p className="text-xs font-semibold uppercase tracking-wider text-muted mb-3">By Source</p>
          {[
            ['register_interest', 'Register Interest'],
            ['contact_form',      'Contact Form'],
            ['brochure_download', 'Brochure Gate'],
          ].map(([key, label]) => (
            <div key={key} className="flex justify-between py-1.5 text-sm border-b border-border last:border-0">
              <span className="text-ce">{label}</span>
              <strong>{sources[key] ?? 0}</strong>
            </div>
          ))}
        </div>

        {/* Quick actions */}
        <div className="bg-surface border border-border rounded-lg p-5 shadow-sm">
          <p className="text-xs font-semibold uppercase tracking-wider text-muted mb-3">Quick Actions</p>
          <div className="space-y-2">
            <a
              href={`${WP_URL}/wp-admin/post-new.php?post_type=ce_property`}
              target="_blank"
              rel="noreferrer"
              className="block text-sm text-gold hover:underline"
            >
              + Add Property
            </a>
            <a href="/admin/enquiries" className="block text-sm text-gold hover:underline">
              View Enquiries {newCount > 0 && `(${newCount} new)`}
            </a>
            <a
              href={`${WP_URL}/wp-json/ce/v1/enquiries/export`}
              target="_blank"
              rel="noreferrer"
              className="block text-sm text-gold hover:underline"
            >
              ↓ Export CSV
            </a>
          </div>
        </div>
      </div>
    </ProtectedLayout>
  );
}
```

- [ ] **Step 3: Verify dashboard loads**

With the dev server running and logged in, visit `http://localhost:3000/admin/dashboard`.

Expected: Dashboard with 4 stat cards, sparkline chart, source breakdown, and quick actions.

- [ ] **Step 4: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/app/dashboard/ admin-panel/components/EnquiriesChart.tsx
git commit -m "feat: admin panel — dashboard page with stats, chart, quick actions"
```

---

### Task 7: Enquiries page

**Files:**
- Create: `admin-panel/components/EnquiriesTable.tsx`
- Create: `admin-panel/app/enquiries/page.tsx`

- [ ] **Step 1: Create `admin-panel/components/EnquiriesTable.tsx`**

```tsx
'use client';

import { useState } from 'react';

interface Enquiry {
  id: number;
  name: string;
  email: string;
  phone: string;
  message: string;
  source: string;
  property_id: number;
  status: 'new' | 'read' | 'replied' | 'archived';
  gdpr_consent: boolean;
  ip_address: string;
  created_at: string;
}

const STATUS_COLOURS: Record<string, string> = {
  new:      'bg-red-50 text-red-700',
  read:     'bg-gray-100 text-gray-600',
  replied:  'bg-green-50 text-green-700',
  archived: 'bg-gray-100 text-gray-500',
};

const TABS = ['all', 'new', 'read', 'replied', 'archived'] as const;

interface Props {
  initial: Enquiry[];
  total: number;
  wpNonce: string;
  wpUrl: string;
  isAdmin: boolean;
}

export default function EnquiriesTable({ initial, total, wpNonce, wpUrl, isAdmin }: Props) {
  const [enquiries, setEnquiries]   = useState(initial);
  const [activeTab, setActiveTab]   = useState<string>('all');
  const [search, setSearch]         = useState('');
  const [expanded, setExpanded]     = useState<number | null>(null);
  const [loading, setLoading]       = useState(false);

  async function fetchEnquiries(status: string, q: string) {
    setLoading(true);
    const params = new URLSearchParams({ per_page: '50' });
    if (status !== 'all') params.set('status', status);
    if (q) params.set('search', q);

    const res = await fetch(`/admin/api/enquiries?${params}`);
    const data = await res.json();
    setEnquiries(data);
    setLoading(false);
  }

  function handleTabChange(tab: string) {
    setActiveTab(tab);
    fetchEnquiries(tab, search);
  }

  function handleSearch(e: React.FormEvent) {
    e.preventDefault();
    fetchEnquiries(activeTab, search);
  }

  async function updateStatus(id: number, status: string) {
    await fetch(`${wpUrl}/wp-json/ce/v1/enquiries/${id}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': wpNonce },
      body: JSON.stringify({ status }),
    });
    setEnquiries(prev => prev.map(e => e.id === id ? { ...e, status: status as Enquiry['status'] } : e));
  }

  return (
    <div>
      {/* Tabs */}
      <div className="flex gap-1 mb-4 border-b border-border">
        {TABS.map(tab => (
          <button
            key={tab}
            onClick={() => handleTabChange(tab)}
            className={`px-4 py-2 text-sm capitalize transition-colors border-b-2 -mb-px ${
              activeTab === tab
                ? 'border-gold text-gold font-semibold'
                : 'border-transparent text-muted hover:text-ce'
            }`}
          >
            {tab}
          </button>
        ))}
        {isAdmin && (
          <a
            href={`${wpUrl}/wp-json/ce/v1/enquiries/export`}
            target="_blank"
            rel="noreferrer"
            className="ml-auto px-4 py-2 text-sm bg-gold text-white rounded hover:bg-gold-hover transition-colors"
          >
            ↓ Export CSV
          </a>
        )}
      </div>

      {/* Search */}
      <form onSubmit={handleSearch} className="flex gap-2 mb-4">
        <input
          type="search"
          value={search}
          onChange={e => setSearch(e.target.value)}
          placeholder="Search by name or email"
          className="border border-border rounded px-3 py-1.5 text-sm w-64 focus:outline-none focus:border-gold"
        />
        <button type="submit" className="px-4 py-1.5 text-sm bg-ce text-white rounded hover:opacity-80">
          Search
        </button>
        {search && (
          <button
            type="button"
            onClick={() => { setSearch(''); fetchEnquiries(activeTab, ''); }}
            className="px-3 py-1.5 text-sm border border-border rounded hover:border-gold"
          >
            Clear
          </button>
        )}
      </form>

      {/* Table */}
      <div className="bg-surface border border-border rounded-lg overflow-hidden shadow-sm">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-bg border-b-2 border-gold">
              {['Name', 'Email', 'Phone', 'Source', 'Status', 'Date'].map(h => (
                <th key={h} className="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-muted">
                  {h}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {loading && (
              <tr><td colSpan={6} className="text-center py-8 text-muted">Loading…</td></tr>
            )}
            {!loading && enquiries.length === 0 && (
              <tr><td colSpan={6} className="text-center py-8 text-muted">No enquiries found.</td></tr>
            )}
            {!loading && enquiries.map(e => (
              <>
                <tr
                  key={e.id}
                  onClick={() => setExpanded(expanded === e.id ? null : e.id)}
                  className="border-b border-border hover:bg-gold/5 cursor-pointer transition-colors"
                >
                  <td className="px-4 py-3 font-medium text-ce">{e.name}</td>
                  <td className="px-4 py-3 text-muted">{e.email}</td>
                  <td className="px-4 py-3 text-muted">{e.phone || '—'}</td>
                  <td className="px-4 py-3 text-muted">{e.source}</td>
                  <td className="px-4 py-3">
                    <span className={`inline-block px-2 py-0.5 rounded text-xs font-medium capitalize ${STATUS_COLOURS[e.status]}`}>
                      {e.status}
                    </span>
                    <select
                      value={e.status}
                      onChange={ev => { ev.stopPropagation(); updateStatus(e.id, ev.target.value); }}
                      onClick={ev => ev.stopPropagation()}
                      className="ml-2 text-xs border border-border rounded px-1 py-0.5 focus:outline-none focus:border-gold"
                    >
                      {['new', 'read', 'replied', 'archived'].map(s => (
                        <option key={s} value={s}>{s}</option>
                      ))}
                    </select>
                  </td>
                  <td className="px-4 py-3 text-muted text-xs">{e.created_at.split(' ')[0]}</td>
                </tr>
                {expanded === e.id && (
                  <tr key={`${e.id}-detail`} className="bg-amber-50">
                    <td colSpan={6} className="px-4 py-4 text-sm text-ce">
                      <p><strong>Message:</strong> {e.message || '—'}</p>
                      <p className="mt-1 text-xs text-muted">
                        GDPR: {e.gdpr_consent ? '✓ Consented' : '✗ No consent'} ·
                        IP: {e.ip_address || '—'} ·
                        Submitted: {e.created_at}
                      </p>
                    </td>
                  </tr>
                )}
              </>
            ))}
          </tbody>
        </table>
      </div>

      <p className="mt-3 text-xs text-muted">{total} total enquiries</p>
    </div>
  );
}
```

- [ ] **Step 2: Create `admin-panel/app/api/enquiries/route.ts`**

```ts
import { NextRequest, NextResponse } from 'next/server';
import { fetchWP } from '@/lib/api';

export async function GET(req: NextRequest) {
  const { searchParams } = new URL(req.url);
  const params = new URLSearchParams();

  ['status', 'search', 'page', 'per_page'].forEach(k => {
    const v = searchParams.get(k);
    if (v) params.set(k, v);
  });

  const res = await fetchWP(`/ce/v1/enquiries?${params}`);
  const data = await res.json();
  return NextResponse.json(data);
}
```

- [ ] **Step 3: Create `admin-panel/app/enquiries/page.tsx`**

```tsx
import ProtectedLayout from '@/components/ProtectedLayout';
import EnquiriesTable from '@/components/EnquiriesTable';
import { fetchWP } from '@/lib/api';
import { getSession, isAdmin } from '@/lib/auth';

export default async function EnquiriesPage() {
  const session = await getSession();
  const res = await fetchWP('/ce/v1/enquiries?per_page=50');
  const enquiries = await res.json();
  const total = parseInt(res.headers.get('X-WP-Total') ?? '0');

  const WP_URL = process.env.NEXT_PUBLIC_WP_URL ?? '';

  return (
    <ProtectedLayout title="Enquiries">
      <EnquiriesTable
        initial={enquiries}
        total={total}
        wpNonce=""
        wpUrl={WP_URL}
        isAdmin={session ? isAdmin(session) : false}
      />
    </ProtectedLayout>
  );
}
```

- [ ] **Step 4: Verify enquiries page**

Visit `http://localhost:3000/admin/enquiries`.

Expected: Enquiries table with status tabs, search bar, and table rows. Changing the status dropdown on a row should update it live.

- [ ] **Step 5: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/components/EnquiriesTable.tsx admin-panel/app/enquiries/ admin-panel/app/api/enquiries/
git commit -m "feat: admin panel — enquiries page with filters and inline status update"
```

---

### Task 8: Properties and Testimonials pages

**Files:**
- Create: `admin-panel/app/properties/page.tsx`
- Create: `admin-panel/app/testimonials/page.tsx`

- [ ] **Step 1: Create `admin-panel/app/properties/page.tsx`**

```tsx
import ProtectedLayout from '@/components/ProtectedLayout';
import { fetchWP } from '@/lib/api';

interface Property {
  id: number;
  title: { rendered: string };
  status: string;
  date: string;
  link: string;
}

export default async function PropertiesPage() {
  const res = await fetchWP('/wp/v2/ce_property?per_page=20&status=any');
  const properties: Property[] = await res.json();
  const total = parseInt(res.headers.get('X-WP-Total') ?? '0');

  const WP_URL = process.env.NEXT_PUBLIC_WP_URL ?? '';

  return (
    <ProtectedLayout title="Properties">
      <div className="flex justify-between items-center mb-4">
        <p className="text-sm text-muted">{total} properties</p>
        <a
          href={`${WP_URL}/wp-admin/post-new.php?post_type=ce_property`}
          target="_blank"
          rel="noreferrer"
          className="px-4 py-2 bg-gold text-white text-sm rounded hover:bg-gold-hover transition-colors"
        >
          + Add Property
        </a>
      </div>

      <div className="bg-surface border border-border rounded-lg overflow-hidden shadow-sm">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-bg border-b-2 border-gold">
              {['Title', 'Status', 'Date', ''].map(h => (
                <th key={h} className="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-muted">
                  {h}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {properties.length === 0 && (
              <tr><td colSpan={4} className="text-center py-8 text-muted">No properties found.</td></tr>
            )}
            {properties.map(p => (
              <tr key={p.id} className="border-b border-border hover:bg-gold/5 transition-colors">
                <td className="px-4 py-3 font-medium text-ce">
                  {p.title.rendered || '(no title)'}
                </td>
                <td className="px-4 py-3">
                  <span className={`inline-block px-2 py-0.5 rounded text-xs font-medium capitalize ${
                    p.status === 'publish' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500'
                  }`}>
                    {p.status}
                  </span>
                </td>
                <td className="px-4 py-3 text-muted text-xs">{p.date.split('T')[0]}</td>
                <td className="px-4 py-3">
                  <a
                    href={`${WP_URL}/wp-admin/post.php?post=${p.id}&action=edit`}
                    target="_blank"
                    rel="noreferrer"
                    className="text-xs text-gold hover:underline"
                  >
                    Edit →
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </ProtectedLayout>
  );
}
```

- [ ] **Step 2: Create `admin-panel/app/testimonials/page.tsx`**

```tsx
import ProtectedLayout from '@/components/ProtectedLayout';
import { fetchWP } from '@/lib/api';

interface Testimonial {
  id: number;
  title: { rendered: string };
  date: string;
}

export default async function TestimonialsPage() {
  const res = await fetchWP('/wp/v2/ce_testimonial?per_page=20&status=any');
  const testimonials: Testimonial[] = await res.json();
  const total = parseInt(res.headers.get('X-WP-Total') ?? '0');

  const WP_URL = process.env.NEXT_PUBLIC_WP_URL ?? '';

  return (
    <ProtectedLayout title="Testimonials">
      <div className="flex justify-between items-center mb-4">
        <p className="text-sm text-muted">{total} testimonials</p>
        <a
          href={`${WP_URL}/wp-admin/post-new.php?post_type=ce_testimonial`}
          target="_blank"
          rel="noreferrer"
          className="px-4 py-2 bg-gold text-white text-sm rounded hover:bg-gold-hover transition-colors"
        >
          + Add Testimonial
        </a>
      </div>

      <div className="bg-surface border border-border rounded-lg overflow-hidden shadow-sm">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-bg border-b-2 border-gold">
              {['Client Name', 'Date', ''].map(h => (
                <th key={h} className="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-muted">
                  {h}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {testimonials.length === 0 && (
              <tr><td colSpan={3} className="text-center py-8 text-muted">No testimonials found.</td></tr>
            )}
            {testimonials.map(t => (
              <tr key={t.id} className="border-b border-border hover:bg-gold/5 transition-colors">
                <td className="px-4 py-3 font-medium text-ce">{t.title.rendered || '(no title)'}</td>
                <td className="px-4 py-3 text-muted text-xs">{t.date.split('T')[0]}</td>
                <td className="px-4 py-3">
                  <a
                    href={`${WP_URL}/wp-admin/post.php?post=${t.id}&action=edit`}
                    target="_blank"
                    rel="noreferrer"
                    className="text-xs text-gold hover:underline"
                  >
                    Edit →
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </ProtectedLayout>
  );
}
```

- [ ] **Step 3: Verify both pages load**

Visit `http://localhost:3000/admin/properties` and `http://localhost:3000/admin/testimonials`.

Expected: Clean tables with Add New buttons. Edit links open WP admin in a new tab.

- [ ] **Step 4: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/app/properties/ admin-panel/app/testimonials/
git commit -m "feat: admin panel — properties and testimonials pages"
```

---

### Task 9: Settings page

**Files:**
- Create: `admin-panel/app/settings/page.tsx`
- Create: `admin-panel/app/api/settings/route.ts`

- [ ] **Step 1: Create `admin-panel/app/api/settings/route.ts`**

```ts
import { NextRequest, NextResponse } from 'next/server';
import { fetchWP } from '@/lib/api';

export async function GET() {
  const res = await fetchWP('/acf/v3/options/options');
  const data = await res.json();
  return NextResponse.json(data?.acf ?? {});
}

export async function POST(req: NextRequest) {
  const body = await req.json();
  const res = await fetchWP('/acf/v3/options/options', {
    method: 'POST',
    body: JSON.stringify({ fields: body }),
  });
  const data = await res.json();
  return NextResponse.json(data);
}
```

- [ ] **Step 2: Create `admin-panel/app/settings/page.tsx`**

```tsx
import ProtectedLayout from '@/components/ProtectedLayout';
import { requireAdmin } from '@/lib/auth';
import { fetchWP } from '@/lib/api';
import SettingsForm from './SettingsForm';

export default async function SettingsPage() {
  await requireAdmin();

  const res = await fetchWP('/acf/v3/options/options');
  const data = await res.json();
  const fields = data?.acf ?? {};

  return (
    <ProtectedLayout title="Settings">
      <SettingsForm fields={fields} />
    </ProtectedLayout>
  );
}
```

- [ ] **Step 3: Create `admin-panel/app/settings/SettingsForm.tsx`**

```tsx
'use client';

import { useState } from 'react';

interface Fields {
  ce_rate_gbp_to_sar?: string;
  ce_rate_gbp_to_usd?: string;
  ce_calc_registration_fee?: string;
  ce_calc_vat?: string;
  ce_calc_agency_fee?: string;
  ce_admin_notification_email?: string;
  ce_digest_recipient_email?: string;
  ce_digest_enabled?: boolean;
  ce_digest_time?: string;
  ce_email_from_name?: string;
  ce_email_from_address?: string;
  ce_email_reply_to?: string;
}

export default function SettingsForm({ fields }: { fields: Fields }) {
  const [values, setValues]   = useState<Fields>(fields);
  const [saving, setSaving]   = useState<string | null>(null);
  const [saved, setSaved]     = useState<string | null>(null);

  function set(key: keyof Fields, value: string | boolean) {
    setValues(prev => ({ ...prev, [key]: value }));
  }

  async function save(section: string, keys: (keyof Fields)[]) {
    setSaving(section);
    const body: Partial<Fields> = {};
    keys.forEach(k => { body[k] = values[k] as any; });

    await fetch('/admin/api/settings', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });

    setSaving(null);
    setSaved(section);
    setTimeout(() => setSaved(null), 2000);
  }

  function Field({ label, name, type = 'text' }: { label: string; name: keyof Fields; type?: string }) {
    return (
      <div>
        <label className="block text-xs font-medium text-muted mb-1">{label}</label>
        {type === 'checkbox' ? (
          <input
            type="checkbox"
            checked={!!values[name]}
            onChange={e => set(name, e.target.checked)}
            className="accent-gold"
          />
        ) : (
          <input
            type={type}
            value={(values[name] as string) ?? ''}
            onChange={e => set(name, e.target.value)}
            className="w-full border border-border rounded px-3 py-2 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold/20"
          />
        )}
      </div>
    );
  }

  function Section({ title, sectionKey, keys, children }: {
    title: string;
    sectionKey: string;
    keys: (keyof Fields)[];
    children: React.ReactNode;
  }) {
    return (
      <div className="bg-surface border border-border rounded-lg shadow-sm mb-4" style={{ borderLeft: '3px solid #C4973A' }}>
        <div className="bg-bg px-5 py-3 border-b border-border">
          <h2 className="text-sm font-semibold text-ce">{title}</h2>
        </div>
        <div className="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          {children}
        </div>
        <div className="px-5 pb-4 flex items-center gap-3">
          <button
            onClick={() => save(sectionKey, keys)}
            disabled={saving === sectionKey}
            className="px-5 py-2 bg-gold text-white text-sm rounded hover:bg-gold-hover transition-colors disabled:opacity-60"
          >
            {saving === sectionKey ? 'Saving…' : 'Save'}
          </button>
          {saved === sectionKey && (
            <span className="text-xs text-green-600 font-medium">✓ Saved</span>
          )}
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-2xl">
      <Section
        title="Exchange Rates"
        sectionKey="rates"
        keys={['ce_rate_gbp_to_sar', 'ce_rate_gbp_to_usd']}
      >
        <Field label="GBP to SAR" name="ce_rate_gbp_to_sar" type="number" />
        <Field label="GBP to USD" name="ce_rate_gbp_to_usd" type="number" />
      </Section>

      <Section
        title="Calculator"
        sectionKey="calculator"
        keys={['ce_calc_registration_fee', 'ce_calc_vat', 'ce_calc_agency_fee']}
      >
        <Field label="Registration Fee (%)" name="ce_calc_registration_fee" type="number" />
        <Field label="VAT (%)"              name="ce_calc_vat"              type="number" />
        <Field label="Agency Fee (%)"       name="ce_calc_agency_fee"       type="number" />
      </Section>

      <Section
        title="Email"
        sectionKey="email"
        keys={[
          'ce_admin_notification_email', 'ce_digest_recipient_email',
          'ce_digest_enabled', 'ce_digest_time',
          'ce_email_from_name', 'ce_email_from_address', 'ce_email_reply_to',
        ]}
      >
        <Field label="Admin Notification Email" name="ce_admin_notification_email" type="email" />
        <Field label="Digest Recipient Email"   name="ce_digest_recipient_email"   type="email" />
        <div className="flex items-center gap-2">
          <Field label="Digest Enabled" name="ce_digest_enabled" type="checkbox" />
          <span className="text-xs text-muted mt-4">Enable daily digest</span>
        </div>
        <Field label="Digest Time (UTC, e.g. 08:00)" name="ce_digest_time" />
        <Field label="Email From Name"    name="ce_email_from_name" />
        <Field label="Email From Address" name="ce_email_from_address" type="email" />
        <Field label="Email Reply-To"     name="ce_email_reply_to"    type="email" />
      </Section>
    </div>
  );
}
```

- [ ] **Step 4: Verify settings page**

Visit `http://localhost:3000/admin/settings` (must be logged in as an admin).

Expected: Three sections — Exchange Rates, Calculator, Email — with pre-filled values from ACF. Save button shows "✓ Saved" confirmation.

If logged in as an editor role, visiting `/settings` should redirect to `/dashboard`.

- [ ] **Step 5: Commit**

```bash
cd /Users/farhanraiss/crown-estates-website
git add admin-panel/app/settings/ admin-panel/app/api/settings/
git commit -m "feat: admin panel — settings page with ACF options read/write"
```

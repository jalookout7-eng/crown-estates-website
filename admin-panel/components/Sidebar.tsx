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

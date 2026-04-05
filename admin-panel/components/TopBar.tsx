'use client';

import { useRouter } from 'next/navigation';

interface TopBarProps {
  title: string;
  username: string;
}

export default function TopBar({ title, username }: TopBarProps) {
  const router = useRouter();

  async function handleLogout() {
    try {
      await fetch('/admin/api/logout', { method: 'POST' });
    } catch {
      // logout API unreachable — clear cookies server-side failed, but still navigate away
    } finally {
      router.push('/admin/login');
      router.refresh();
    }
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

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

    try {
      const res = await fetch('/admin/api/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, appPassword }),
      });

      const data = await res.json();

      if (data.error) {
        setError(data.error);
      } else {
        router.push('/dashboard');
        router.refresh();
      }
    } catch {
      setError('Could not connect to the server. Please try again.');
    } finally {
      setLoading(false);
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

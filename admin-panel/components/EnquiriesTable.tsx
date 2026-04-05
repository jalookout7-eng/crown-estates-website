'use client';

import React, { useState } from 'react';

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
  wpUrl: string;
  isAdmin: boolean;
}

export default function EnquiriesTable({ initial, total, wpUrl, isAdmin }: Props) {
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

    try {
      const res = await fetch(`/admin/api/enquiries?${params}`);
      if (res.ok) {
        const data = await res.json();
        setEnquiries(data);
      }
    } catch {
      // fetch failed — keep current data
    } finally {
      setLoading(false);
    }
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
    try {
      const res = await fetch(`/admin/api/enquiries/${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status }),
      });
      if (res.ok) {
        setEnquiries(prev => prev.map(e => e.id === id ? { ...e, status: status as Enquiry['status'] } : e));
      }
    } catch {
      // update failed — status not changed in UI
    }
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
              <React.Fragment key={e.id}>
                <tr
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
              </React.Fragment>
            ))}
          </tbody>
        </table>
      </div>

      <p className="mt-3 text-xs text-muted">{total} total enquiries</p>
    </div>
  );
}

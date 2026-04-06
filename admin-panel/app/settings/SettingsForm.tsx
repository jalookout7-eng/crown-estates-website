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
  const [saveError, setSaveError] = useState<string | null>(null);

  function set(key: keyof Fields, value: string | boolean) {
    setValues(prev => ({ ...prev, [key]: value }));
  }

  async function save(section: string, keys: (keyof Fields)[]) {
    setSaving(section);
    setSaveError(null);
    const body: Partial<Fields> = {};
    keys.forEach(k => { (body as Record<string, unknown>)[k] = values[k]; });

    try {
      const res = await fetch('/admin/api/settings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      });
      if (!res.ok) {
        setSaveError('Failed to save. Please try again.');
      } else {
        setSaved(section);
        setSaveError(null);
        setTimeout(() => setSaved(null), 2000);
      }
    } catch {
      setSaveError('Could not reach the server.');
    } finally {
      setSaving(null);
    }
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
          {saveError && saving !== sectionKey && (
            <span className="text-xs text-red-500">{saveError}</span>
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

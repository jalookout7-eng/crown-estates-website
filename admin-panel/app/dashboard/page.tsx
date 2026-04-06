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
  try {
    const [propsRes, enquiriesRes, allEnquiriesRes] = await Promise.all([
      fetchWP('/wp/v2/ce_property?per_page=1&status=publish'),
      fetchWP('/ce/v1/enquiries?per_page=1'),
      fetchWP('/ce/v1/enquiries?per_page=100'),
    ]);

    const totalProperties = parseInt(propsRes.headers.get('X-WP-Total') ?? '0');
    const totalEnquiries  = parseInt(enquiriesRes.headers.get('X-WP-Total') ?? '0');

    let allEnquiries: Enquiry[] = [];
    if (allEnquiriesRes.ok) {
      try {
        allEnquiries = await allEnquiriesRes.json();
      } catch {
        // non-JSON response — treat as empty
      }
    }

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
  } catch {
    return {
      totalProperties: 0,
      totalEnquiries: 0,
      newCount: 0,
      sources: {} as Record<string, number>,
      chartData: [] as { day: string; count: number }[],
    };
  }
}

export default async function DashboardPage() {
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

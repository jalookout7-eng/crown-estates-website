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
  let properties: Property[] = [];
  let total = 0;

  try {
    const res = await fetchWP('/wp/v2/ce_property?per_page=20&status=any');
    if (res.ok) {
      properties = await res.json();
      total = parseInt(res.headers.get('X-WP-Total') ?? '0');
    }
  } catch {
    // WP unreachable — render empty table
  }

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

import ProtectedLayout from '@/components/ProtectedLayout';
import { fetchWP } from '@/lib/api';

interface Testimonial {
  id: number;
  title: { rendered: string };
  date: string;
}

export default async function TestimonialsPage() {
  let testimonials: Testimonial[] = [];
  let total = 0;

  try {
    const res = await fetchWP('/wp/v2/ce_testimonial?per_page=20&status=any');
    if (res.ok) {
      testimonials = await res.json();
      total = parseInt(res.headers.get('X-WP-Total') ?? '0');
    }
  } catch {
    // WP unreachable — render empty table
  }

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

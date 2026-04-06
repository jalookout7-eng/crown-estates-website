import ProtectedLayout from '@/components/ProtectedLayout';
import EnquiriesTable from '@/components/EnquiriesTable';
import { fetchWP } from '@/lib/api';
import { getSession, isAdmin } from '@/lib/auth';

export default async function EnquiriesPage() {
  const session = await getSession();

  let enquiries: unknown[] = [];
  let total = 0;

  try {
    const res = await fetchWP('/ce/v1/enquiries?per_page=50');
    if (res.ok) {
      enquiries = await res.json();
      total = parseInt(res.headers.get('X-WP-Total') ?? '0');
    }
  } catch {
    // WP unreachable — render empty table
  }

  const WP_URL = process.env.NEXT_PUBLIC_WP_URL ?? '';

  return (
    <ProtectedLayout title="Enquiries">
      <EnquiriesTable
        initial={enquiries as Parameters<typeof EnquiriesTable>[0]['initial']}
        total={total}
        wpUrl={WP_URL}
        isAdmin={session ? isAdmin(session) : false}
      />
    </ProtectedLayout>
  );
}

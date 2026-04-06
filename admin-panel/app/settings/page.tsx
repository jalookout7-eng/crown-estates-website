import ProtectedLayout from '@/components/ProtectedLayout';
import { requireAdmin } from '@/lib/auth';
import { fetchWP } from '@/lib/api';
import SettingsForm from './SettingsForm';

export default async function SettingsPage() {
  await requireAdmin();

  let fields: Record<string, unknown> = {};

  try {
    const res = await fetchWP('/acf/v3/options/options');
    if (res.ok) {
      const data = await res.json();
      fields = data?.acf ?? {};
    }
  } catch {
    // WP unreachable — render form with empty values
  }

  return (
    <ProtectedLayout title="Settings">
      <SettingsForm fields={fields} />
    </ProtectedLayout>
  );
}

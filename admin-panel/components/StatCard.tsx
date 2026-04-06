interface StatCardProps {
  label: string;
  value: string | number;
  accent?: string;
  badge?: string;
}

export default function StatCard({ label, value, accent = '#C4973A', badge }: StatCardProps) {
  return (
    <div
      className="bg-surface border border-border rounded-lg p-5 shadow-sm"
      style={{ borderTop: `3px solid ${accent}` }}
    >
      <div className="text-2xl font-bold text-ce flex items-center gap-2">
        {value}
        {badge && (
          <span className="text-xs font-medium px-2 py-0.5 rounded bg-red-50 text-red-700">
            {badge}
          </span>
        )}
      </div>
      <div className="text-xs text-muted mt-1">{label}</div>
    </div>
  );
}

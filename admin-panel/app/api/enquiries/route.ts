import { NextRequest, NextResponse } from 'next/server';
import { fetchWP } from '@/lib/api';

export async function GET(req: NextRequest) {
  const { searchParams } = new URL(req.url);
  const params = new URLSearchParams();

  ['status', 'search', 'page', 'per_page'].forEach(k => {
    const v = searchParams.get(k);
    if (v) params.set(k, v);
  });

  try {
    const res = await fetchWP(`/ce/v1/enquiries?${params}`);
    if (!res.ok) {
      return NextResponse.json({ error: 'Failed to fetch enquiries.' }, { status: res.status });
    }
    const data = await res.json();
    return NextResponse.json(data);
  } catch {
    return NextResponse.json({ error: 'Failed to fetch enquiries.' }, { status: 500 });
  }
}

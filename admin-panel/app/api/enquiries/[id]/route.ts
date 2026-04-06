import { NextRequest, NextResponse } from 'next/server';
import { fetchWP } from '@/lib/api';

export async function POST(
  req: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const body = await req.json();
    const res = await fetchWP(`/ce/v1/enquiries/${params.id}`, {
      method: 'POST',
      body: JSON.stringify(body),
    });
    if (!res.ok) {
      return NextResponse.json({ error: 'Failed to update enquiry.' }, { status: res.status });
    }
    const data = await res.json();
    return NextResponse.json(data);
  } catch {
    return NextResponse.json({ error: 'Could not reach WordPress.' }, { status: 500 });
  }
}

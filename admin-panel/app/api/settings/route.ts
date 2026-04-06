import { NextRequest, NextResponse } from 'next/server';
import { fetchWP } from '@/lib/api';

export async function GET() {
  try {
    const res = await fetchWP('/acf/v3/options/options');
    if (!res.ok) {
      return NextResponse.json({ error: 'Failed to fetch settings.' }, { status: res.status });
    }
    const data = await res.json();
    return NextResponse.json(data?.acf ?? {});
  } catch {
    return NextResponse.json({ error: 'Could not reach WordPress.' }, { status: 500 });
  }
}

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const res = await fetchWP('/acf/v3/options/options', {
      method: 'POST',
      body: JSON.stringify({ fields: body }),
    });
    if (!res.ok) {
      return NextResponse.json({ error: 'Failed to save settings.' }, { status: res.status });
    }
    const data = await res.json();
    return NextResponse.json(data);
  } catch {
    return NextResponse.json({ error: 'Could not save settings.' }, { status: 500 });
  }
}

import { NextRequest, NextResponse } from 'next/server';
import { login } from '@/lib/auth';

export async function POST(req: NextRequest) {
  let username: string;
  let appPassword: string;

  try {
    const body = await req.json();
    username = body.username;
    appPassword = body.appPassword;
  } catch {
    return NextResponse.json({ error: 'Invalid request body.' }, { status: 400 });
  }

  if (!username || !appPassword) {
    return NextResponse.json({ error: 'Username and password are required.' }, { status: 400 });
  }

  try {
    const result = await login(username, appPassword);

    if (result.error) {
      return NextResponse.json({ error: result.error }, { status: 401 });
    }

    return NextResponse.json({ success: true });
  } catch {
    return NextResponse.json({ error: 'An unexpected error occurred.' }, { status: 500 });
  }
}

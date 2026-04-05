import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL!;

export interface Session {
  token: string;
  username: string;
  role: 'admin' | 'editor';
}

export async function getSession(): Promise<Session | null> {
  const store = cookies();
  const token    = store.get('ce_admin_token')?.value;
  const username = store.get('ce_admin_username')?.value;
  const role = store.get('ce_admin_role')?.value;
  if (!token || !username || !role) return null;
  if (role !== 'admin' && role !== 'editor') return null;
  return { token, username, role: role as 'admin' | 'editor' };
}

export async function requireSession(): Promise<Session> {
  const session = await getSession();
  if (!session) redirect('/login');
  return session;
}

export async function requireAdmin(): Promise<Session> {
  const session = await requireSession();
  if (session.role !== 'admin') redirect('/dashboard');
  return session;
}

export async function login(username: string, appPassword: string): Promise<{ error?: string }> {
  const token = Buffer.from(`${username}:${appPassword}`).toString('base64');

  let res: Response;
  try {
    res = await fetch(`${WP_URL}/wp-json/wp/v2/users/me?context=edit`, {
      headers: { Authorization: `Basic ${token}` },
      cache: 'no-store',
    });
  } catch {
    return { error: 'Could not reach authentication server. Is WordPress running?' };
  }

  if (!res.ok) {
    return { error: 'Invalid username or application password.' };
  }

  const user = await res.json();
  const role: 'admin' | 'editor' = user.capabilities?.manage_options ? 'admin' : 'editor';

  const cookieOpts = {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax' as const,
    maxAge: 86400,
    path: '/',
  };

  const store = cookies();
  store.set('ce_admin_token',    token,       cookieOpts);
  store.set('ce_admin_username', user.name,   cookieOpts);
  store.set('ce_admin_role',     role,        cookieOpts);

  return {};
}

export async function logout(): Promise<void> {
  const store = cookies();
  store.delete('ce_admin_token');
  store.delete('ce_admin_username');
  store.delete('ce_admin_role');
}

export function isAdmin(session: Session): boolean {
  return session.role === 'admin';
}

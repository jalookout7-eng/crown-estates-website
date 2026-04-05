import { getSession } from './auth';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL!;

export async function fetchWP(path: string, options: RequestInit = {}): Promise<Response> {
  const session = await getSession();
  if (!session) throw new Error('Not authenticated');

  return fetch(`${WP_URL}/wp-json${path}`, {
    ...options,
    headers: {
      Authorization: `Basic ${session.token}`,
      'Content-Type': 'application/json',
      ...(options.headers as Record<string, string> ?? {}),
    },
    cache: options.cache ?? 'no-store',
  });
}

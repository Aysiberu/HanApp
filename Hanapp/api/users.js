import { supabase } from './supabaseClient.js';

// Example Vercel serverless function: GET /api/users
// - Uses server-side supabase client
// - Respects ?limit= & ?q= query params

export default async function handler(req, res) {
  if (req.method !== 'GET') {
    res.status(405).json({ error: 'Method not allowed' });
    return;
  }

  try {
    const limit = Math.min(parseInt(req.query.limit || '50', 10) || 50, 100);
    const q = (req.query.q || '').toString().trim();

    // Build query string for PostgREST via supabase-js query builder
    let builder = supabase.from('users').select('id, first_name, last_name, email, created_at').order('id', { ascending: false }).limit(limit);

    if (q) {
      // Quick search by name or email
      builder = builder.or(`first_name.ilike.%${q}%,last_name.ilike.%${q}%,email.ilike.%${q}%`);
    }

    const { data, error } = await builder;
    if (error) {
      console.error('Supabase error in /api/users:', error);
      res.status(500).json({ error: 'Error fetching users' });
      return;
    }

    res.setHeader('Cache-Control', 's-maxage=60, stale-while-revalidate=300');
    res.status(200).json({ users: data || [] });
  } catch (err) {
    console.error('Unhandled error in /api/users:', err);
    res.status(500).json({ error: 'Internal server error' });
  }
}

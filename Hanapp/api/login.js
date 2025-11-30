import { supabase } from './supabaseClient.js';
import bcrypt from 'bcryptjs';

export default async function handler(req, res) {
  if (req.method !== 'POST') {
    res.status(405).json({ error: 'Method not allowed' });
    return;
  }

  try {
    const { email, password } = req.body || {};
    if (!email || !password) {
      res.status(400).json({ error: 'Missing email or password' });
      return;
    }

    const { data: rows, error } = await supabase.from('users').select('id, password, first_name, last_name, email').eq('email', email).limit(1);
    if (error) {
      console.error('supabase select error', error);
      res.status(500).json({ error: 'Database error' });
      return;
    }

    if (!rows || rows.length === 0) {
      res.status(401).json({ error: 'Invalid credentials' });
      return;
    }

    const user = rows[0];
    const match = await bcrypt.compare(password, user.password || '');
    if (!match) {
      res.status(401).json({ error: 'Invalid credentials' });
      return;
    }

    // NOTE: We are not issuing a persistent session here (cookie / JWT).
    // You can integrate Supabase Auth or create a JWT for the frontend to use.
    res.status(200).json({ ok: true, user: { id: user.id, name: (user.first_name || '') + ' ' + (user.last_name || ''), email: user.email } });
  } catch (err) {
    console.error('unhandled login error', err);
    res.status(500).json({ error: 'Internal server error' });
  }
}

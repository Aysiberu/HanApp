import { supabase } from './supabaseClient.js';
import bcrypt from 'bcryptjs';

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const { email, password } = req.body || {};
    if (!email || !password) return res.status(400).json({ error: 'Missing email or password' });

    const { data: rows, error } = await supabase.from('users').select('id, password, first_name, last_name, email').eq('email', email).limit(1);
    if (error) { console.error('login_provider select error', error); return res.status(500).json({ error: 'Database error' }); }
    if (!rows || rows.length === 0) return res.status(401).json({ error: 'Invalid credentials' });

    const user = rows[0];
    const match = await bcrypt.compare(password, user.password || '');
    if (!match) return res.status(401).json({ error: 'Invalid credentials' });

    // ensure user also has a provider record
    const { data: prov, error: pErr } = await supabase.from('providers').select('id, user_id').eq('user_id', user.id).limit(1);
    if (pErr) { console.error('login_provider providers select err', pErr); return res.status(500).json({ error: 'Internal error' }); }
    if (!prov || prov.length === 0) return res.status(403).json({ error: 'No provider account found for this user' });

    res.status(200).json({ ok: true, provider: { id: prov[0].id, user_id: prov[0].user_id, name: (user.first_name||'') + ' ' + (user.last_name||'') } });
  } catch (err) { console.error('unhandled login_provider', err); res.status(500).json({ error: 'Internal server error' }); }
}

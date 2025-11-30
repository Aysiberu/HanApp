import { supabase } from './supabaseClient.js';
import bcrypt from 'bcryptjs';

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const { email, code, newPassword } = req.body || {};
    if (!email || !code || !newPassword) return res.status(400).json({ error: 'Missing fields' });

    // fetch user
    const { data: users, error } = await supabase.from('users').select('id, reset_code, reset_expiry').eq('email', email).limit(1);
    if (error) { console.error('reset select err', error); return res.status(500).json({ error: 'Internal error' }); }
    if (!users || users.length === 0) return res.status(404).json({ error: 'Email not found' });

    const u = users[0];
    if (!u.reset_code || u.reset_code !== code) return res.status(400).json({ error: 'Invalid code' });
    if (!u.reset_expiry || new Date(u.reset_expiry) < new Date()) return res.status(400).json({ error: 'Code expired' });

    // update password
    if (newPassword.length < 8) return res.status(400).json({ error: 'Password too short' });
    const hashed = await bcrypt.hash(newPassword, 10);
    const { error: upErr } = await supabase.from('users').update({ password: hashed, reset_code: null, reset_expiry: null }).eq('id', u.id);
    if (upErr) { console.error('reset update err', upErr); return res.status(500).json({ error: 'Could not update password' }); }

    res.status(200).json({ ok: true, message: 'Password updated' });
  } catch (err) { console.error('reset unhandled', err); res.status(500).json({ error: 'Internal server error' }); }
}

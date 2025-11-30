import { supabase } from './supabaseClient.js';
import bcrypt from 'bcryptjs';

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const { email, currentPassword, newPassword } = req.body || {};
    if (!email || !currentPassword || !newPassword) return res.status(400).json({ error: 'Missing fields' });

    const { data: rows, error } = await supabase.from('users').select('id,password').eq('email', email).limit(1);
    if (error) { console.error('update select err', error); return res.status(500).json({ error: 'Internal error' }); }
    if (!rows || rows.length === 0) return res.status(404).json({ error: 'Email not found' });

    const user = rows[0];
    const match = await bcrypt.compare(currentPassword, user.password || '');
    if (!match) return res.status(401).json({ error: 'Current password incorrect' });

    if (newPassword.length < 8) return res.status(400).json({ error: 'New password too short' });
    const hashed = await bcrypt.hash(newPassword, 10);
    const { error: upErr } = await supabase.from('users').update({ password: hashed }).eq('id', user.id);
    if (upErr) { console.error('update passwd err', upErr); return res.status(500).json({ error: 'Could not update password' }); }

    res.status(200).json({ ok: true, message: 'Password updated' });
  } catch (err) { console.error('update unhandled', err); res.status(500).json({ error: 'Internal server error' }); }
}

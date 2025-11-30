import { supabase } from './supabaseClient.js';
import bcrypt from 'bcryptjs';

function makeCode() {
  return Math.floor(100000 + Math.random() * 900000).toString();
}

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const { email } = req.body || {};
    if (!email) return res.status(400).json({ error: 'Missing email' });

    const { data: users, error } = await supabase.from('users').select('id,email').eq('email', email).limit(1);
    if (error) { console.error('forgot select err', error); return res.status(500).json({ error: 'Internal error' }); }
    if (!users || users.length === 0) return res.status(404).json({ error: 'Email not found' });

    const user = users[0];
    const code = makeCode();
    const expiry = new Date(Date.now() + 1000 * 60 * 60).toISOString(); // 1 hour

    const { error: upErr } = await supabase.from('users').update({ reset_code: code, reset_expiry: expiry }).eq('id', user.id);
    if (upErr) { console.error('forgot update err', upErr); return res.status(500).json({ error: 'Could not set reset code' }); }

    // NOTE: This demo does not send email. Ideally you would send the code by email to the user.
    // For testing we return the code in the response. Remove this in production.
    res.status(200).json({ ok: true, message: 'Reset code generated', code, expiry });
  } catch (err) {
    console.error('forgot unhandled', err); res.status(500).json({ error: 'Internal server error' });
  }
}

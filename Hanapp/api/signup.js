import { supabase } from './supabaseClient.js';
import bcrypt from 'bcryptjs';

export default async function handler(req, res) {
  if (req.method !== 'POST') {
    res.status(405).json({ error: 'Method not allowed' });
    return;
  }

  try {
    const body = req.body || {};
    const {
      firstName, middleName, lastName, extName,
      email, mobile, location, zip, password
    } = body;

    // Basic validation
    if (!firstName || !lastName || !email || !password) {
      res.status(400).json({ error: 'Missing required fields' });
      return;
    }

    // validate numeric mobile & zip
    if (mobile && !/^[0-9]+$/.test(mobile)) {
      res.status(400).json({ error: 'Mobile must contain only digits' });
      return;
    }
    if (zip && !/^[0-9]+$/.test(zip)) {
      res.status(400).json({ error: 'ZIP must contain only digits' });
      return;
    }

    // allowed locations inside Ilocos Norte (alphabetical)
    const allowed = ['Adams','Bacarra','Badoc','Bangui','Banna','Burgos','Carasi','Currimao','Dingras','Dumalneg','Laoag','Marcos','Nueva Era','Pagudpud','Piddig','Pinili','San Nicolas','Sarrat','Solsona','Vintar'];
    if (location && !allowed.includes(location)) {
      res.status(400).json({ error: 'Location must be one of the Ilocos Norte municipalities/cities' });
      return;
    }

    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      res.status(400).json({ error: 'Invalid email' });
      return;
    }

    // check if user exists
    const { data: existing, error: selErr } = await supabase
      .from('users')
      .select('id')
      .eq('email', email)
      .limit(1);

    if (selErr) {
      console.error('supabase select error', selErr);
      res.status(500).json({ error: 'Database error' });
      return;
    }

    if (existing && existing.length > 0) {
      res.status(409).json({ error: 'Email already in use' });
      return;
    }

    // hash password
    const hashed = await bcrypt.hash(password, 10);

    const payload = {
      first_name: firstName,
      middle_name: middleName || '',
      last_name: lastName,
      ext_name: extName || null,
      email,
      mobile: mobile || null,
      location: location || null,
      zip: zip || null,
      password: hashed,
    };

    const { data, error: insertErr } = await supabase.from('users').insert([payload]).select('id,email,first_name,last_name').limit(1);
    if (insertErr) {
      console.error('supabase insert error', insertErr);
      res.status(500).json({ error: 'Could not create user' });
      return;
    }

    res.status(201).json({ ok: true, user: data?.[0] ?? null });
  } catch (err) {
    console.error('unhandled signup error', err);
    res.status(500).json({ error: 'Internal server error' });
  }
}

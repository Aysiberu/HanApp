import { supabase } from './supabaseClient.js';
import bcrypt from 'bcryptjs';

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const body = req.body || {};
    const { firstName, middleName, lastName, extName, email, mobile, location, zip, password } = body;
    if (!firstName || !lastName || !email || !password) return res.status(400).json({ error: 'Missing required fields' });

    // numeric checks
    if (mobile && !/^[0-9]+$/.test(mobile)) return res.status(400).json({ error: 'Mobile must contain only digits' });
    if (zip && !/^[0-9]+$/.test(zip)) return res.status(400).json({ error: 'ZIP must contain only digits' });

    const allowed = ['Adams','Bacarra','Badoc','Bangui','Banna','Burgos','Carasi','Currimao','Dingras','Dumalneg','Laoag','Marcos','Nueva Era','Pagudpud','Piddig','Pinili','San Nicolas','Sarrat','Solsona','Vintar'];
    if (location && !allowed.includes(location)) return res.status(400).json({ error: 'Location must be a valid Ilocos Norte municipality/city' });

    // check existing
    const { data: ex, error: sErr } = await supabase.from('users').select('id').eq('email', email).limit(1);
    if (sErr) { console.error('provider_signup select err', sErr); return res.status(500).json({ error: 'Internal error' }); }
    if (ex && ex.length) return res.status(409).json({ error: 'Email already in use' });

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

    const { data: urows, error: insErr } = await supabase.from('users').insert([payload]).select('id,email,first_name,last_name').limit(1);
    if (insErr || !urows || urows.length === 0) { console.error('provider signup insert err', insErr); return res.status(500).json({ error: 'Could not create user' }); }

    const user = urows[0];
    // create empty provider record
    const provPayload = {
      user_id: user.id,
      name: `${user.first_name} ${user.last_name}`,
      photo: null,
      location: location || null,
      service_types: null,
      price_per_hour: null,
      availability: null,
      availability_from: null,
      availability_to: null,
      bio: null
    };

    const { data: prow, error: pErr } = await supabase.from('providers').insert([provPayload]).select('id').limit(1);
    if (pErr) { console.error('provider create err', pErr); /* don't fail user creation */ }

    res.status(201).json({ ok: true, user: user, providerId: prow?.[0]?.id ?? null });
  } catch (err) { console.error('provider signup unhandled', err); res.status(500).json({ error: 'Internal server error' }); }
}

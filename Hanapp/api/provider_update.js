import { supabase } from './supabaseClient.js';

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const body = req.body || {};
    const { providerId, userId, service_types, company, availability, availability_from, availability_to, certificate_url, profile_photo_url, price_per_hour, location, bio, houseNumber, street } = body;
    if (!providerId && !userId) return res.status(400).json({ error: 'Missing providerId or userId' });

    const match = providerId ? { id: providerId } : { user_id: userId };

    // validate location if provided
    const allowed = ['Adams','Bacarra','Badoc','Bangui','Banna','Burgos','Carasi','Currimao','Dingras','Dumalneg','Laoag','Marcos','Nueva Era','Pagudpud','Piddig','Pinili','San Nicolas','Sarrat','Solsona','Vintar'];
    if (location && !allowed.includes(location)) return res.status(400).json({ error: 'Location must be a municipality/city from Ilocos Norte' });

    const payload = {
      service_types: service_types || null,
      location: location || null,
      price_per_hour: price_per_hour || null,
      availability: availability || null,
      availability_from: availability_from || null,
      availability_to: availability_to || null,
      bio: (company ? `Company: ${company}\n` : '') + (bio || ''),
      house_number: houseNumber || null,
      street: street || null,
    };

    if (profile_photo_url) payload.photo = profile_photo_url;
    if (certificate_url) payload.bio = (payload.bio || '') + `\nCertificate: ${certificate_url}`;

    const { data, error } = await supabase.from('providers').update(payload).match(match).select('id,user_id');
    if (error) { console.error('provider_update err', error); return res.status(500).json({ error: 'Could not update provider' }); }

    res.status(200).json({ ok: true, provider: data?.[0] ?? null });
  } catch (err) { console.error('provider_update unhandled', err); res.status(500).json({ error: 'Internal server error' }); }
}

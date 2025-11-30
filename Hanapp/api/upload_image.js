import { supabase } from './supabaseClient.js';

// Accepts JSON { bucket, path, filename, base64, contentType }
export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const { bucket = 'public', path = '', filename = null, base64, contentType = 'image/jpeg' } = req.body || {};
    if (!base64 || !filename) return res.status(400).json({ error: 'Missing base64 or filename' });

    // decode base64
    const match = base64.match(/^data:(image\/[^;]+);base64,(.*)$/);
    let raw = base64;
    let ct = contentType;
    if (match) { ct = match[1]; raw = match[2]; }
    const buffer = Buffer.from(raw, 'base64');

    // unique path
    const destPath = (path ? `${path.replace(/^\/+|\/+$/g,'')}/` : '') + `${Date.now()}_${filename}`;

    const { data, error } = await supabase.storage.from(bucket).upload(destPath, buffer, { contentType: ct, upsert: false });
    if (error) { console.error('upload error', error); return res.status(500).json({ error: 'Upload failed', detail: error }); }

    const { publicURL } = supabase.storage.from(bucket).getPublicUrl(destPath);
    res.status(201).json({ ok: true, path: destPath, url: publicURL });
  } catch (err) { console.error('upload unhandled', err); res.status(500).json({ error: 'Internal server error' }); }
}

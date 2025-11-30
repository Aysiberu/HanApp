import { supabase } from './supabaseClient.js';

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const { sender_id, receiver_id, message } = req.body || {};
    if (!sender_id || !receiver_id || !message) return res.status(400).json({ error: 'Missing fields' });

    const payload = { sender_id: parseInt(sender_id,10), receiver_id: parseInt(receiver_id,10), message };
    const { data, error } = await supabase.from('messages').insert([payload]).select('id,sender_id,receiver_id,message,created_at');
    if (error) {
      console.error('supabase insert message error', error);
      return res.status(500).json({ error: 'Database error' });
    }

    res.status(201).json({ success: true, message: data?.[0] ?? null });
  } catch (err) {
    console.error('unhandled send_message error', err);
    res.status(500).json({ error: 'Internal server error' });
  }
}

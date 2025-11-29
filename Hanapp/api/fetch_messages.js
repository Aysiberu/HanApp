import { supabase } from './supabaseClient.js';

export default async function handler(req, res) {
  if (req.method !== 'GET') return res.status(405).json({ error: 'Method not allowed' });
  try {
    const user = parseInt(req.query.u || req.query.user || '0', 10);
    const other = parseInt(req.query.other || req.query.to || '0', 10);
    if (!user || !other) return res.status(400).json({ error: 'Missing user or other id' });

    // fetch messages between user and other ordered by created_at
    const { data, error } = await supabase
      .from('messages')
      .select('id, sender_id, receiver_id, message, created_at')
      .or(`and(sender_id.eq.${user},receiver_id.eq.${other}),and(sender_id.eq.${other},receiver_id.eq.${user})`)
      .order('created_at', { ascending: true });

    if (error) {
      console.error('supabase fetch messages error', error);
      return res.status(500).json({ error: 'Database error' });
    }

    res.status(200).json(data || []);
  } catch (err) {
    console.error('unhandled fetch_messages error', err);
    res.status(500).json({ error: 'Internal server error' });
  }
}

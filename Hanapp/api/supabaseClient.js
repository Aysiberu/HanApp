import { createClient } from '@supabase/supabase-js';

// Reads configuration from environment variables set in Vercel.
// Recommended env variables (set in Vercel Project Settings -> Environment Variables):
// - SUPABASE_URL (e.g. https://xyzcompany.supabase.co)
// - SUPABASE_SERVICE_KEY (server-side service role) OR SUPABASE_KEY/SUPABASE_ANON_KEY

// Use environment variables when available. For quick local testing you provided
// a SUPABASE URL + service key so we can fall back to those values if env vars are
// not set. NOTE: it's strongly recommended you DO NOT commit secrets to source
// control and instead set the variables in your Vercel project settings.
const SUPABASE_URL = process.env.SUPABASE_URL || 'https://yajldhxreagwebnaueds.supabase.co';
// Prefer a server-side service_role key for server functions. If not present fall back to SUPABASE_KEY/ANON.
const SUPABASE_KEY = process.env.SUPABASE_SERVICE_KEY || process.env.SUPABASE_KEY || process.env.SUPABASE_ANON_KEY || 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InlhamxkaHhyZWFnd2VibmF1ZWRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjQ0MTk4NzUsImV4cCI6MjA3OTk5NTg3NX0.aR8JBrCkmDVxDnM9Zx0OYtyax5UvT_QbeUiwIuiwRfg';

// If you want to ensure deployments fail when env vars are missing (recommended),
// replace the above fallbacks and set the variables in Vercel Project Settings.

// Create a Supabase client for server-side usage. Keep persistSession false for serverless functions.
export const supabase = createClient(SUPABASE_URL, SUPABASE_KEY, {
  auth: { persistSession: false },
});

export default supabase;

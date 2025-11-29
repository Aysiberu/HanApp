Vercel serverless & Supabase setup

Overview
- This folder contains example Vercel-friendly serverless API functions that use Supabase (Node.js).
- These functions must be deployed to Vercel (or another serverless host) where you provide environment variables via the platform UI.

Required environment variables (set these in Vercel > Project > Settings > Environment Variables):
- SUPABASE_URL — your Supabase project URL (e.g. https://abc123.supabase.co)
- SUPABASE_SERVICE_KEY — service role key for server-side operations (keep secret)

Notes & best practices
- Never commit your Supabase service_role key to source control. Use Vercel's environment variable UI for secrets.
- For frontend/anonymous access you'll use SUPABASE_ANON_KEY or SUPABASE_KEY — keep permissions limited.
- Prefer using serverless functions for operations that need service_role privileges (e.g., admin inserts) so you don't expose the admin key to clients.

Files added
- package.json — Node dependency manifest (includes @supabase/supabase-js)
- api/supabaseClient.js — server-side Supabase client that reads config from environment variables
- api/users.js — example GET /api/users endpoint that fetches from Supabase safely
- .env.example — shows the environment variables to set on Vercel

Deploying
1. Push this repo to GitHub/GitLab/Bitbucket and connect it to Vercel.
2. In the Vercel dashboard, set the environment variables shown above.
3. Vercel will install dependencies from package.json and build serverless functions automatically.
4. Test the endpoint: https://<your-vercel-deploy>/api/users

Security
- Use the SUPABASE_SERVICE_KEY in serverless functions only.
- Protect sensitive endpoints behind authentication and/or Row Level Security policies in Supabase.

Examples
- To list users in a browser (only use with limited data or when RLS is configured):
  GET /api/users?limit=20&q=beru

If you'd like I can add additional sample endpoints for creating bookings, messaging, or auth hooks.
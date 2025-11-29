Supabase integration guide — HanApp
================================

This project has been updated to support Supabase (Postgres + PostgREST) using a small PHP helper file `supabase.php`.

Files updated to use Supabase REST API
- Core user / auth flow: `login_process.php`, `signup_process.php`, `reset_password_process.php`, `verify_code.php`
- Messaging endpoints: `send_message.php`, `fetch_messages.php`, `messages.php`, `provider_messages.php`
- Booking flows: `bookservice.php`, `bookservicestep2.php`, `bookservice_step3.php`, `bookinghistory.php`
- Settings & update logic: `settings.php`, `update_settings.php`

How to configure (local / production)
------------------------------------
1. Create a Supabase project at https://app.supabase.com and note your project URL and keys.
2. For server-side usage (backend scripts) you should use the SERVICE_ROLE key (kept private). Alternatively, use the anon key but configure RLS for security.
3. Set environment variables on your server (recommended) or edit `supabase.php` temporarily during local development:

   - SUPABASE_URL=https://your-project.supabase.co
   - SUPABASE_KEY=YOUR_SUPABASE_SERVICE_ROLE_OR_ANON_KEY

4. Apply the SQL migrations in order (they are idempotent, but run in this order):
   - `hanapp.sql` (create core tables `users`, `bookings`)
   - `create_providers_table.sql` (adds `providers` and sample seed rows)
   - `create_messages_table.sql` (adds `messages` table)

Notes & next steps
------------------
- The helper `supabase.php` uses PostgREST endpoints. It must be able to reach your Supabase project and CURL must be enabled in PHP.
- For security, do not expose the service_role key to the browser — always use it from server-side code.
- The project currently uses simple REST calls; as next improvements you might add row-level security, JWT auth integration, and real-time subscriptions via Supabase Realtime WebSockets.

If you'd like, I can:
- Convert any remaining DB-dependent pages to use Supabase fully
- Add a migration script to convert an existing MySQL database into a Supabase/Postgres schema
- Implement booking persistence and full Step 3 booking form using Supabase

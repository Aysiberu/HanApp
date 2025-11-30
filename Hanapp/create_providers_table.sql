-- Run this SQL to create a providers table and add sample providers used by bookservicestep2
-- Run order: first apply `hanapp.sql` (creates `users` and `bookings`), then this file.
-- Note: This file is written for Postgres/Supabase. Do not run `USE` in Postgres.
CREATE TABLE IF NOT EXISTS providers (
  id SERIAL PRIMARY KEY,
  user_id INT DEFAULT NULL,
  name VARCHAR(255) NOT NULL,
  photo VARCHAR(255) DEFAULT NULL,
  location VARCHAR(255) DEFAULT NULL,
  service_types VARCHAR(255) DEFAULT NULL, -- comma separated list, e.g. 'Plumbing,Painting'
  price_per_hour NUMERIC(8,2) DEFAULT NULL,
  completed_tasks INT DEFAULT 0,
  availability VARCHAR(255) DEFAULT NULL, -- human-friendly (e.g. 'Every Weekends 9AM-5PM')
  availability_from TIME DEFAULT NULL,
  availability_to TIME DEFAULT NULL,
  rating NUMERIC(3,2) DEFAULT 0.00,
  bio TEXT DEFAULT NULL,
  CONSTRAINT fk_providers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- index for faster lookups by user
CREATE INDEX IF NOT EXISTS idx_providers_user_id ON providers(user_id);

-- seed data (INSERT if not exists). Adjust photo paths as needed for your app.
INSERT INTO providers (name, photo, location, service_types, price_per_hour, completed_tasks, availability, availability_from, availability_to, rating, bio)
VALUES
('Mama Coco', 'assets/provider1.png', 'Laoag, Ilocos Norte', 'Plumbing,Outdoor Repair', 100.00, 92, 'Every Weekends 9AM-5PM', '09:00:00', '17:00:00', 4.75, 'Experienced plumber — I can help with installations, repairs and maintenance.'),
('Pocoloco Tacotaco', 'assets/provider2.png', 'Bacarra, Ilocos Norte', 'Plumbing,Moving', 120.00, 218, 'Every Weekends 9AM-5PM', '09:00:00', '17:00:00', 4.40, 'Reliable and fast — I offer plumbing and moving help.'),
('Kita Moto Tacotaco', 'assets/provider3.png', 'Vintar, Ilocos Norte', 'Plumbing,Painting', 110.00, 183, 'Every Weekends 9AM-5PM', '09:00:00', '17:00:00', 4.60, 'Professional — plumbing and painting services available.')
ON CONFLICT DO NOTHING;



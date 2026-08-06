CREATE INDEX IF NOT EXISTS movie_sessions_date_of_start_idx ON movie_sessions(date_of_start);
CREATE INDEX IF NOT EXISTS booked_tickets_date_of_booked_idx ON booked_tickets(date_of_booked);
CREATE INDEX IF NOT EXISTS movies_premier_date_idx ON movies(premier_date);
CREATE INDEX IF NOT EXISTS movie_sessions_movie_id_idx ON movie_sessions(movie_id);
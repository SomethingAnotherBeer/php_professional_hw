SELECT movies.movie_id, movies.movie_name, SUM(ticket_price) AS movie_profit FROM movies INNER JOIN movie_sessions ON movies.movie_id = movie_sessions.movie_id
    INNER JOIN tickets ON movie_sessions.movie_session_id = tickets.movie_session_id WHERE movie_sessions.date_of_end < CURRENT_DATE AND tickets.is_booked IS FALSE GROUP BY movies.movie_id, movies.movie_name
    ORDER BY movie_profit DESC LIMIT 1;


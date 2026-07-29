SELECT movies.movie_id, movies.movie_name, SUM(ticket_price) AS movie_profit FROM movies INNER JOIN movie_sessions ON movies.movie_id = movie_sessions.movie_id
    INNER JOIN tickets ON movie_sessions.movie_session_id = tickets.movie_session_id WHERE movie_sessions.date_of_start >= '2026-06-01' AND movie_sessions.date_of_end <= '2026-07-25' AND tickets.is_booked IS FALSE GROUP BY movies.movie_id, movies.movie_name
    ORDER BY movie_profit DESC LIMIT 1;


WITH movies_profits AS (
    SELECT m.movie_id, m.movie_name, SUM(t.ticket_price) AS movie_profit, RANK() OVER(ORDER BY SUM(t.ticket_price) DESC) AS rank FROM movies AS m INNER JOIN movie_sessions AS ms ON m.movie_id = ms.movie_id
    INNER JOIN tickets AS t ON ms.movie_session_id = t.movie_session_id
    INNER JOIN booked_tickets AS bt ON t.ticket_id = bt.ticket_id

    GROUP BY m.movie_id, m.movie_name
    ORDER BY movie_profit DESC
)
SELECT movie_id, movie_name, movie_profit FROM movies_profits WHERE rank = 1;

SELECT m.movie_id, m.movie_name, SUM(t.ticket_price) AS movie_profit, RANK() OVER(ORDER BY SUM(t.ticket_price) DESC) AS rank FROM movies AS m INNER JOIN movie_sessions AS ms ON m.movie_id = ms.movie_id
    INNER JOIN tickets AS t ON ms.movie_session_id = t.movie_session_id
    INNER JOIN booked_tickets AS bt ON t.ticket_id = bt.ticket_id

    GROUP BY m.movie_id, m.movie_name
    ORDER BY movie_profit DESC
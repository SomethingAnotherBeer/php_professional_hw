TRUNCATE TABLE booked_tickets RESTART IDENTITY CASCADE;
TRUNCATE TABLE tickets RESTART IDENTITY CASCADE;
TRUNCATE TABLE places RESTART IDENTITY CASCADE;
TRUNCATE TABLE movie_sessions RESTART IDENTITY CASCADE;
TRUNCATE TABLE hall_rows RESTART IDENTITY CASCADE;
TRUNCATE TABLE halls RESTART IDENTITY CASCADE;
TRUNCATE TABLE movies RESTART IDENTITY CASCADE;
TRUNCATE TABLE customers RESTART IDENTITY CASCADE;
TRUNCATE TABLE users RESTART IDENTITY CASCADE;



INSERT INTO halls(hall_id, hall_name) VALUES
(1, 'Большой зал'),
(2, 'Средний зал'),
(3, 'Маленький зал');

SELECT setval('halls_hall_id_seq', (SELECT MAX(halls.hall_id) FROM halls));



INSERT INTO hall_rows(hall_id, hall_row_number)
SELECT
1, i
FROM generate_series(1, 12) AS i;

INSERT INTO hall_rows(hall_id, hall_row_number)
SELECT
2, i
FROM generate_series(1, 8) AS i;


INSERT INTO hall_rows(hall_id, hall_row_number)
SELECT
3, i
FROM generate_series(1, 5) AS i;


WITH first_hall_rows AS (
    SELECT hall_row_id, ROW_NUMBER() OVER(PARTITION BY hall_row_id ORDER BY hall_row_id) AS place_number FROM
    hall_rows
    CROSS JOIN generate_series(1, 10)
    WHERE hall_id = 1
)
INSERT INTO places(hall_row_id, place_number)
SELECT
first_hall_rows.hall_row_id, first_hall_rows.place_number
FROM first_hall_rows;


WITH second_hall_rows AS (
    SELECT hall_row_id, ROW_NUMBER() OVER(PARTITION BY hall_row_id ORDER BY hall_row_id) AS place_number FROM
    hall_rows
    CROSS JOIN generate_series(1, 10)
    WHERE hall_id = 2
)
INSERT INTO places(hall_row_id, place_number)
SELECT
second_hall_rows.hall_row_id, second_hall_rows.place_number
FROM second_hall_rows;

WITH third_hall_rows AS (
    SELECT hall_row_id, ROW_NUMBER() OVER(PARTITION BY hall_row_id ORDER BY hall_row_id) AS place_number FROM
    hall_rows
    CROSS JOIN generate_series(1, 10)
    WHERE hall_id = 3
)
INSERT INTO places(hall_row_id, place_number)
SELECT
third_hall_rows.hall_row_id, third_hall_rows.place_number
FROM third_hall_rows;


INSERT INTO movies(movie_name, premier_date, genre, duration, age_rating)
SELECT
'movie_name_' || i,
((NOW() - INTERVAL '1 month') + (24 * (i - 1) || ' hours')::interval)::date,
(ARRAY['фэнтэзи', 'фантастика', 'хоррор', 'драма', 'комедия', 'мелодрама', 'документальный'])[floor(random() * 7) + 1],
'2 hours'::INTERVAL,
(ARRAY['PG', 'PG-13', 'R'])[floor(random() * 3) + 1]
FROM generate_series(1, 5000) AS i;


INSERT INTO users(user_id, user_name, user_email, user_login, user_password, user_role)
SELECT
i, 'user_name__' || i, 'user_name_' || i || '@example.com', 'user_' || i || '_login',
crypt((ARRAY['111', '222', '333', '444', '555'])[floor(random() * 5) + 1], gen_salt('bf', 10)),
'ROLE_CUSTOMER'
FROM generate_series(1, 100) AS i;

SELECT setval('users_user_id_seq', (SELECT MAX(users.user_id) FROM users));


INSERT INTO customers(customer_id, user_id, customer_email, customer_phone)
SELECT 
i,i, 'user_name' || i || '@example.com', '+7' || '(' || (floor(random() * (999 - 111)) + 111) || ')' || floor(random() * (9999999 - 1111111) + 1111111)
FROM generate_series(1, 100) AS i;

SELECT setval('customers_customer_id_seq', (SELECT MAX(customers.customer_id) FROM customers));




WITH current_movies AS (
    SELECT movie_id, premier_date, duration, i AS current_repeat,
    (ARRAY[9, 12, 14, 16, 18, 20, 22])[i] AS showtime
    FROM movies CROSS JOIN generate_series(1, 7) AS i
)

INSERT INTO movie_sessions(movie_id, hall_id, date_of_start, date_of_end)
SELECT 
cm.movie_id, 
1,
cm.premier_date::timestamp + ((cm.current_repeat - 1) * 24 + cm.showtime || ' hours')::interval,
cm.premier_date::timestamp + ((cm.current_repeat - 1) * 24 + cm.showtime || ' hours')::interval + duration
FROM current_movies AS cm;




WITH current_movies AS (
    SELECT movie_id, premier_date, duration, i AS current_repeat,
    (ARRAY[9, 12, 14, 16, 18, 20, 22])[i] AS showtime
    FROM movies CROSS JOIN generate_series(1, 7) AS i
)

INSERT INTO movie_sessions(movie_id, hall_id, date_of_start, date_of_end)
SELECT 
cm.movie_id, 
2,
cm.premier_date::timestamp + ((cm.current_repeat - 1) * 24 + cm.showtime || ' hours')::interval,
cm.premier_date::timestamp + ((cm.current_repeat - 1) * 24 + cm.showtime || ' hours')::interval + duration
FROM current_movies AS cm;




WITH current_movies AS (
    SELECT movie_id, premier_date, duration, i AS current_repeat,
    (ARRAY[9, 12, 14, 16, 18, 20, 22])[i] AS showtime
    FROM movies CROSS JOIN generate_series(1, 7) AS i
)

INSERT INTO movie_sessions(movie_id, hall_id, date_of_start, date_of_end)
SELECT 
cm.movie_id, 
3,
cm.premier_date::timestamp + ((cm.current_repeat - 1) * 24 + cm.showtime || ' hours')::interval,
cm.premier_date::timestamp + ((cm.current_repeat - 1) * 24 + cm.showtime || ' hours')::interval + duration
FROM current_movies AS cm;



WITH fh_movie_sessions AS (
    SELECT movie_sessions.movie_session_id, places.place_id, movie_sessions.date_of_start FROM movie_sessions
    INNER JOIN halls ON movie_sessions.hall_id = halls.hall_id
    INNER JOIN hall_rows ON halls.hall_id = hall_rows.hall_id
    INNER JOIN places ON hall_rows.hall_row_id = places.hall_row_id
    WHERE movie_sessions.hall_id = 1
    ORDER BY movie_sessions.movie_session_id
)
INSERT INTO tickets(movie_session_id, place_id, ticket_price)
SELECT fh_ms.movie_session_id, fh_ms.place_id, (ARRAY[500, 800, 850, 900, 1000])[floor(random() * 5) + 1]

FROM fh_movie_sessions AS fh_ms;


WITH fh_movie_sessions AS (
    SELECT movie_sessions.movie_session_id, places.place_id, movie_sessions.date_of_start FROM movie_sessions
    INNER JOIN hall_rows ON movie_sessions.hall_id = hall_rows.hall_id
    INNER JOIN places ON hall_rows.hall_row_id = places.hall_row_id
     WHERE movie_sessions.hall_id = 2
     ORDER BY movie_sessions.movie_session_id
)
INSERT INTO tickets(movie_session_id, place_id, ticket_price)
SELECT fh_ms.movie_session_id, fh_ms.place_id, (ARRAY[500, 800, 850, 900, 1000])[floor(random() * 5) + 1]

FROM fh_movie_sessions AS fh_ms;



WITH fh_movie_sessions AS (
    SELECT movie_sessions.movie_session_id, places.place_id, movie_sessions.date_of_start FROM movie_sessions
    INNER JOIN hall_rows ON movie_sessions.hall_id = hall_rows.hall_id
    INNER JOIN places ON hall_rows.hall_row_id = places.hall_row_id
     WHERE movie_sessions.hall_id = 3
     ORDER BY movie_sessions.movie_session_id
)
INSERT INTO tickets(movie_session_id, place_id, ticket_price)
SELECT fh_ms.movie_session_id, fh_ms.place_id, (ARRAY[500, 800, 850, 900, 1000])[floor(random() * 5) + 1]

FROM fh_movie_sessions AS fh_ms;




WITH current_tickets AS (
    SELECT tickets.ticket_id, ms.date_of_start - (floor(random() * 4) + 1 || ' hours')::interval AS date_of_booked FROM tickets 
    INNER JOIN movie_sessions AS ms ON tickets.movie_session_id = ms.movie_session_id
     WHERE tickets.ticket_id % 2 = 0
)
INSERT INTO booked_tickets(ticket_id, date_of_booked) SELECT ticket_id, date_of_booked  FROM current_tickets;

WITH current_customers AS (
    SELECT customer_id FROM customers
)
UPDATE booked_tickets SET customer_id = current_customers.customer_id FROM current_customers WHERE booked_tickets.ticket_id % 3 = 0;
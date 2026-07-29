DROP TABLE IF EXISTS booked_tickets;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS movie_sessions;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS places;
DROP TABLE IF EXISTS hall_rows;
DROP TABLE IF EXISTS halls;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS users;

CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TABLE halls(
    hall_id serial PRIMARY KEY,
    hall_name VARCHAR(255) NOT NULL
);

CREATE TABLE hall_rows(
    hall_row_id serial PRIMARY KEY,
    hall_id INTEGER NOT NULL,
    hall_row_number SMALLINT NOT NULL,
    FOREIGN KEY(hall_id) REFERENCES halls(hall_id) ON DELETE CASCADE,
    CONSTRAINT hall_row_number_unique UNIQUE(hall_row_number, hall_id) 
);

CREATE TABLE places(
    place_id serial PRIMARY KEY,
    hall_row_id INTEGER NOT NULL,
    place_number SMALLINT NOT NULL,
    FOREIGN KEY(hall_row_id) REFERENCES hall_rows(hall_row_id) ON DELETE CASCADE,
    CONSTRAINT hall_row_place_number_unique UNIQUE(place_number, hall_row_id)
);

CREATE TABLE movies(
    movie_id SERIAL PRIMARY KEY,
    movie_name VARCHAR(255),
    premier_date DATE,
    genre VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    age_rating VARCHAR(255),
    movie_description TEXT DEFAULT ''
);

CREATE TABLE movie_sessions(
    movie_session_id SERIAL PRIMARY KEY,
    movie_id INTEGER NOT NULL,
    hall_id INTEGER NOT NULL,
    date_of_start TIMESTAMP NOT NULL,
    date_of_end TIMESTAMP NOT NULL,
    ticket_price DECIMAL(8, 2) NOT NULL,
    FOREIGN KEY(movie_id) REFERENCES movies(movie_id),
    FOREIGN KEY(hall_id) REFERENCES halls(hall_id),
    CONSTRAINT valid_dates CHECK(date_of_start < date_of_end)
);

CREATE TABLE tickets(
    ticket_id SERIAL PRIMARY KEY,
    movie_session_id INTEGER NOT NULL,
    place_id INTEGER NOT NULL,
    ticket_price DECIMAL(8,2) NOT NULL,
    FOREIGN KEY(movie_session_id) REFERENCES movie_sessions(movie_session_id) ON DELETE CASCADE,
    FOREIGN KEY(place_id) REFERENCES places(place_id) ON DELETE CASCADE,
    CONSTRAINT per_one_place UNIQUE(movie_session_id, place_id)
);

CREATE TABLE users(
    user_id SERIAL PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL DEFAULT 'anonimous user',
    user_email VARCHAR(255) UNIQUE NOT NUll,
    user_login VARCHAR(255) UNIQUE NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    user_role VARCHAR(100) NOT NULL DEFAULT 'ROLE_MANAGER'
);

CREATE TABLE customers(
    customer_id SERIAL PRIMARY KEY,
    user_id INTEGER UNIQUE,
    customer_email VARCHAR(255) UNIQUE NOT NULL,
    customer_phone VARCHAR(255),
    FOREIGN KEY(user_id) REFERENCES users(user_id)

);


CREATE TABLE booked_tickets(
    booked_ticket_id SERIAL PRIMARY KEY,
    ticket_id INTEGER NOT NULL,
    customer_id INTEGER,
    date_of_booked TIMESTAMP NOT NULL,
    FOREIGN KEY(ticket_id) REFERENCES tickets(ticket_id) ON DELETE CASCADE,
    FOREIGN KEY(customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    CONSTRAINT one_customer_per_ticket UNIQUE(ticket_id, customer_id)
);



CREATE INDEX idx_movie_session_start_end ON movie_sessions(date_of_start, date_of_end);

INSERT INTO halls(hall_id, hall_name) VALUES
(1, 'Большой зал'),
(2, 'Средний зал'),
(3, 'Маленький зал');

SELECT setval('halls_hall_id_seq', (SELECT MAX(halls.hall_id) FROM halls));



INSERT INTO hall_rows(hall_id, hall_row_number)
SELECT
1, i
FROM generate_series(1, 24) AS i;

INSERT INTO hall_rows(hall_id, hall_row_number)
SELECT
2, i
FROM generate_series(1, 12) AS i;


INSERT INTO hall_rows(hall_id, hall_row_number)
SELECT
3, i
FROM generate_series(1, 6) AS i;






WITH first_hall_rows AS (
    SELECT hall_row_id, ROW_NUMBER() OVER(ORDER BY hall_row_id) AS place_number FROM
    hall_rows
    CROSS JOIN generate_series(1, 24)
    WHERE hall_id = 1
)
INSERT INTO places(hall_row_id, place_number)
SELECT
first_hall_rows.hall_row_id, first_hall_rows.place_number
FROM first_hall_rows;


WITH second_hall_rows AS (
    SELECT hall_row_id, ROW_NUMBER() OVER(ORDER BY hall_row_id) AS place_number FROM
    hall_rows
    CROSS JOIN generate_series(1, 12)
    WHERE hall_id = 2
)
INSERT INTO places(hall_row_id, place_number)
SELECT
second_hall_rows.hall_row_id, second_hall_rows.place_number
FROM second_hall_rows;

WITH third_hall_rows AS (
    SELECT hall_row_id, ROW_NUMBER() OVER(ORDER BY hall_row_id) AS place_number FROM
    hall_rows
    CROSS JOIN generate_series(1, 6)
    WHERE hall_id = 3
)
INSERT INTO places(hall_row_id, place_number)
SELECT
third_hall_rows.hall_row_id, third_hall_rows.place_number
FROM third_hall_rows;





INSERT INTO movies(movie_name, premier_date, genre, duration, age_rating)
SELECT
'movie_name_' || i,
(NOW() + (24 * i || ' hours')::interval)::date,
(ARRAY['фэнтэзи', 'фантастика', 'хоррор', 'драма', 'комедия', 'мелодрама', 'документальный'])[floor(random() * 7) + 1],
(ARRAY[7200, 8400, 5400, 10800])[floor(random() * 4) + 1],
(ARRAY['PG', 'PG-13', 'R'])[floor(random() * 3) + 1]
FROM generate_series(1, 20) AS i;


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
    CASE
        WHEN i IN (1, 2) THEN 12
        WHEN i IN (3, 4) THEN 17
        WHEN i IN (5,6,7) THEN 20
    END AS showtime
    FROM movies CROSS JOIN generate_series(1, 7) AS i
)

INSERT INTO movie_sessions(movie_id, hall_id, date_of_start, date_of_end, ticket_price)
SELECT 
cm.movie_id, 
1,
cm.premier_date::timestamp + (cm.current_repeat * 24 + cm.showtime || ' hours')::interval,
cm.premier_date::timestamp + (cm.current_repeat * 24 + cm.showtime + (duration::float / 3600)::integer || ' hours')::interval,
(ARRAY[500, 800, 850, 900, 1000])[floor(random() * 5) + 1]
FROM current_movies AS cm;




WITH current_movies AS (
    SELECT movie_id, premier_date, duration, i AS current_repeat,
    CASE
        WHEN i IN (1, 2) THEN 12
        WHEN i IN (3, 4) THEN 17
        WHEN i IN (5,6,7) THEN 20
    END AS showtime
    FROM movies CROSS JOIN generate_series(1, 7) AS i
)

INSERT INTO movie_sessions(movie_id, hall_id, date_of_start, date_of_end, ticket_price)
SELECT 
cm.movie_id, 
2,
cm.premier_date::timestamp + (cm.current_repeat * 24 + cm.showtime || ' hours')::interval,
cm.premier_date::timestamp + (cm.current_repeat * 24 + cm.showtime + (duration::float / 3600)::integer || ' hours')::interval,
(ARRAY[500, 800, 850, 900, 1000])[floor(random() * 5) + 1]
FROM current_movies AS cm;




WITH current_movies AS (
    SELECT movie_id, premier_date, duration, i AS current_repeat,
    CASE
        WHEN i IN (1, 2) THEN 12
        WHEN i IN (3, 4) THEN 17
        WHEN i IN (5,6,7) THEN 20
    END AS showtime
    FROM movies CROSS JOIN generate_series(1, 7) AS i
)

INSERT INTO movie_sessions(movie_id, hall_id, date_of_start, date_of_end, ticket_price)
SELECT 
cm.movie_id, 
3,
cm.premier_date::timestamp + (cm.current_repeat * 24 + cm.showtime || ' hours')::interval,
cm.premier_date::timestamp + (cm.current_repeat * 24 + cm.showtime + (duration::float / 3600)::integer || ' hours')::interval,
(ARRAY[500, 800, 850, 900, 1000])[floor(random() * 5) + 1]
FROM current_movies AS cm;



WITH fh_movie_sessions AS (
    SELECT movie_sessions.movie_session_id, places.place_id, movie_sessions.date_of_start FROM movie_sessions
    INNER JOIN halls ON movie_sessions.hall_id = halls.hall_id
    INNER JOIN hall_rows ON halls.hall_id = hall_rows.hall_id
    INNER JOIN places ON hall_rows.hall_row_id = places.hall_row_id
    WHERE movie_sessions.hall_id = 1
)
INSERT INTO tickets(movie_session_id, place_id, ticket_price)
SELECT fh_ms.movie_session_id, fh_ms.place_id, (ARRAY[500, 800, 850, 900, 1000])[floor(random() * 5) + 1]

FROM fh_movie_sessions AS fh_ms;


WITH fh_movie_sessions AS (
    SELECT movie_sessions.movie_session_id, places.place_id, movie_sessions.date_of_start FROM movie_sessions
    INNER JOIN hall_rows ON movie_sessions.hall_id = hall_rows.hall_id
    INNER JOIN places ON hall_rows.hall_row_id = places.hall_row_id
     WHERE movie_sessions.hall_id = 2
)
INSERT INTO tickets(movie_session_id, place_id, ticket_price)
SELECT fh_ms.movie_session_id, fh_ms.place_id, (ARRAY[500, 800, 850, 900, 1000])[floor(random() * 5) + 1]

FROM fh_movie_sessions AS fh_ms;



WITH fh_movie_sessions AS (
    SELECT movie_sessions.movie_session_id, places.place_id, movie_sessions.date_of_start FROM movie_sessions
    INNER JOIN hall_rows ON movie_sessions.hall_id = hall_rows.hall_id
    INNER JOIN places ON hall_rows.hall_row_id = places.hall_row_id
     WHERE movie_sessions.hall_id = 3
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
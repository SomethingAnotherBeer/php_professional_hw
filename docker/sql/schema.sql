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


INSERT INTO hall_rows (hall_row_id, hall_id, hall_row_number)
SELECT
i, 1, i
FROM generate_series(1, 24) AS i;


INSERT INTO hall_rows(hall_row_id, hall_id, hall_row_number)
SELECT
i + 24, 2, i
FROM generate_series(1, 12) AS i;

INSERT INTO hall_rows(hall_row_id, hall_id, hall_row_number)
SELECT
i + 36, 3, i
FROM generate_series(1, 6) AS i;

SELECT setval('hall_rows_hall_row_id_seq', (SELECT MAX(hall_rows.hall_row_id) FROM hall_rows));

INSERT INTO places (place_id, hall_row_id, place_number)
SELECT
i, i, i
FROM generate_series(1, 24) AS i;

INSERT INTO places (place_id, hall_row_id, place_number)
SELECT
i + 24, i + 24, i
FROM generate_series(1, 12) AS i;

INSERT INTO places (place_id, hall_row_id, place_number)
SELECT
i + 36, i + 36, i
FROM generate_series(1, 6) AS i;

SELECT setval('places_place_id_seq', (SELECT MAX(places.place_id) FROM places));


INSERT INTO movies(movie_id, movie_name, premier_date, genre, duration, age_rating)
SELECT
i, 'movie_name_' || i,
to_timestamp(floor(random() * (1749589200 - 1672520400)) + 1672520400),
(ARRAY['фэнтэзи', 'фантастика', 'хоррор', 'драма', 'комедия', 'мелодрама', 'документальный'])[floor(random() * 7) + 1],
(ARRAY[7200, 8400, 5400, 10800])[floor(random() * 4) + 1],
(ARRAY['PG', 'PG-13', 'R'])[floor(random() * 3) + 1]
FROM generate_series(1, 1000) AS i;

SELECT setval('movies_movie_id_seq', (SELECT MAX(movies.movie_id) FROM movies));


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
    SELECT movie_id, premier_date, duration FROM movies
)
INSERT INTO movie_sessions(movie_id, hall_id, date_of_start, date_of_end, ticket_price)
SELECT cm.movie_id, (ARRAY[1,2,3])[floor(random() * 3) + 1],
cm.premier_date::timestamp + (i * 24 + (ARRAY[10, 15, 21])[(i % 3) + 1] || ' hours')::interval + ((ARRAY[20, 25, 30])[(i % 3) + 1]  || ' minutes')::interval,
cm.premier_date::timestamp + (i * 24 + (ARRAY[10, 15, 21])[(i % 3) + 1] || ' hours')::interval + ((ARRAY[20, 25, 30])[(i % 3) + 1]  || ' minutes')::interval + (duration || ' seconds')::interval,
(ARRAY[200, 250, 300, 400, 500, 700, 200, 580, 900, 1000])[floor(random() * 10) + 1]
FROM current_movies AS cm CROSS JOIN generate_series(1, 10) AS i;



WITH tickets_movie_sessions AS (
    SELECT movie_session_id, hall_id, ticket_price FROM movie_sessions
)

INSERT INTO tickets (movie_session_id, place_id, ticket_price)
SELECT tickets_movie_sessions.movie_session_id, places.place_id, tickets_movie_sessions.ticket_price
FROM tickets_movie_sessions
INNER JOIN hall_rows ON tickets_movie_sessions.hall_id = hall_rows.hall_id
INNER JOIN places ON hall_rows.hall_row_id = places.hall_row_id;

WITH current_tickets AS (
    SELECT ticket_id, movie_session_id FROM tickets
)
INSERT INTO booked_tickets (ticket_id, customer_id, date_of_booked)
SELECT ct.ticket_id, (ARRAY[null, null, null, null, floor(random() * 100) + 1])[floor(random() * 5) + 1], ms.date_of_start - (floor(random() * 5) + 1 || ' hours')::interval
FROM current_tickets AS ct
INNER JOIN movie_sessions AS ms ON ct.movie_session_id = ms.movie_session_id;


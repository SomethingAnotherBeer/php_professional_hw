DROP TABLE IF EXISTS halls;
DROP TABLE IF EXISTS hall_rows;
DROP TABLE IF EXISTS places;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS movie_sessions;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS customers;
CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TABLE halls(
    hall_id serial PRIMARY KEY,
    hall_name VARCHAR(255) NOT NULL
);

CREATE TABLE hall_rows(
    hall_row_id serial PRIMARY KEY,
    hall_id INTEGER NOT NULL,
    hall_row_number SMALLINT NOT NULL,
    FOREIGN KEY(hall_id) REFERENCES halls(hall_id),
    CONSTRAINT hall_row_number_unique UNIQUE(hall_row_number, hall_id) 
);

CREATE TABLE places(
    place_id serial PRIMARY KEY,
    hall_row_id INTEGER NOT NULL,
    place_number SMALLINT NOT NULL,
    FOREIGN KEY(hall_row_id) REFERENCES hall_rows(hall_row_id),
    CONSTRAINT hall_row_place_number_unique UNIQUE(place_number, hall_row_id)
);

CREATE TABLE movies(
    movie_id SERIAL PRIMARY KEY,
    movie_name VARCHAR(255),
    premier_date DATE,
    genre VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    age_rating VARCHAR(255),
    description TEXT DEFAULT ''
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

CREATE TABLE tickets(
    ticket_id SERIAL PRIMARY KEY,
    movie_session_id INTEGER NOT NULL,
    hall_id INTEGER NOT NULL,
    place_id INTEGER NOT NULL,
    customer_id INTEGER,
    is_booked BOOLEAN NOT NULL DEFAULT false,
    FOREIGN KEY(movie_session_id) REFERENCES movie_sessions(movie_session_id),
    FOREIGN KEY(hall_id) REFERENCES halls(hall_id),
    FOREIGN KEY(place_id) REFERENCES places(place_id),
    FOREIGN KEY(customer_id) REFERENCES customers(customer_id)
);

INSERT INTO halls(hall_id, hall_name) VALUES
(1, 'Большой зал'),
(2, 'Средний зал'),
(3, 'Маленький зал');

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

INSERT INTO movies (movie_id, movie_name, premier_date, genre, duration, age_rating)
SELECT
i, 'movie_name_' || i, 
(ARRAY['2026-06-01'::date, '2026-06-02'::date, '2026-06-03'::date, '2026-06-04'::date, '2026-06-05'::date, '2026-06-06'::date, '2026-06-07'::date, '2026-06-08'::date, '2026-06-09'::date, '2026-06-10'::date, '2026-06-11'::date, '2026-06-12'::date])[i],
(ARRAY['фэнтэзи', 'хоррор', 'фантастика', 'фэнтэзи', 'драма', 'комедия', 'детектив', 'боевик', 'фантастика', 'документальный', 'фэнтэзи', 'фэнтэзи'])[i],
(ARRAY[7200, 8400, 5400, 5400, 7200, 10800, 7200, 7200, 5400, 5400, 8400, 8400])[i],
(ARRAY['PG', 'R', 'PG-13', 'PG-13', 'PG', 'PG', 'R', 'PG-13', 'PG-13', 'PG', 'PG-13', 'PG-13'])[i]
FROM generate_series(1, 12) AS i;


INSERT INTO users(user_id, user_name, user_email, user_login, user_password, user_role)
SELECT
i, 'user_name_' || i, 'user_name_' || i || '@example.com', 'user_' || i || 'login',
crypt((ARRAY['111', '222', '333', '444', '555', '666', '777', '888', '999', '1000'])[i], gen_salt('bf', 10)),
'ROLE_CUSTOMER'
FROM generate_series(1, 10) AS i;

INSERT INTO customers(customer_id, user_id, customer_email, customer_phone)
SELECT
i,
(ARRAY[null, null, null, 1, 2, 3, 4, 8, 9, 10])[i],
(ARRAY['customer_one@example.com', 'customer_two@example.com', 'customer_three@example.com', 'user_name_1@example.com', 'user_name_2@example.com', 'user_name_3@example.com', 'user_name_4@example.com', 'user_name_5@example.com', 'user_name_6@example.com', 'user_name_7@example.com', 'user_name_8@example.com', 'user_name_9@example.com', 'user_name_10@example.com'])[i],
(ARRAY['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'])[i]
FROM generate_series(1, 10) AS i;


DO $$
DECLARE
    movie_show_id_duration_list INT[][][] := ARRAY[[[1, 7200], [2, 8400], [3, 5400]], [[4, 5400], [5, 7200], [6, 10800]], [[7, 7200], [8, 7200], [9, 5400]], [[10, 5400], [11, 8400], [12, 8400]]];
    hall_id_list INT[] := ARRAY[1, 2, 3];
    current_movie_date TIMESTAMP := '2026-06-21'::TIMESTAMP;
    current_start_time TIMESTAMP := current_date + '10:20'::INTERVAL;
    current_end_time TIMESTAMP := current_start_time;
    current_ticket_price DECIMAL := 0.0;
    current_customer_id INT := NULL;
    current_customer_definder INT := 0;
    current_movie_session_id INT := 1;
    current_ticket_id INT := 1;

    
    movie_partition INT[][];
    day_number INT;
    current_hall_id INT;
    movie_params INT[];
    current_row RECORD;

BEGIN
    FOREACH movie_partition SLICE 2 IN ARRAY movie_show_id_duration_list
        LOOP
            FOR day_number IN 1..7
                LOOP
                    FOREACH current_hall_id IN ARRAY hall_id_list
                        LOOP
                            FOREACH movie_params SLICE 1 IN ARRAY movie_partition
                                LOOP
                                    current_end_time := current_start_time + (movie_params[2] || ' sec')::interval;
                                    current_ticket_price := floor(random() * (1000 - 200 + 1) + 200)::decimal;
                                    INSERT INTO movie_sessions(movie_session_id, movie_id, hall_id, date_of_start, date_of_end, ticket_price)
                                     VALUES (current_movie_session_id, movie_params[1], current_hall_id, current_start_time, current_end_time, current_ticket_price);
                                    
                                     current_start_time := current_end_time + INTERVAL '10 minutes';
                                     current_movie_session_id := current_movie_session_id + 1;

                                END LOOP;
                        END LOOP;
                        current_movie_date := current_movie_date + INTERVAL '24 hours';
                        current_start_time := current_movie_date + '10:20'::INTERVAL;
                END LOOP;
        END LOOP;

    FOR current_row IN SELECT ms.hall_id AS current_hall_id, ms.movie_session_id AS current_movie_session_id, p.place_id AS current_place_id FROM movie_sessions AS ms INNER JOIN hall_rows AS hr ON ms.hall_id = hr.hall_id INNER JOIN places AS p ON hr.hall_row_id = p.hall_row_id
        LOOP
            current_customer_definder := floor(random() * (21 - 1) + 1)::int;
            current_customer_id := Null;
            IF current_customer_definder > 17 THEN
                current_customer_id:= (ARRAY[1, 2, 3, 4, 5, 6, 7, 8, 9, 10])[floor(random() * (11 - 1) + 1)::int];

            END IF;

            INSERT INTO tickets(ticket_id, movie_session_id, hall_id, place_id, customer_id, is_booked) VALUES (current_ticket_id, current_row.current_movie_session_id, current_row.current_hall_id, current_row.current_place_id, current_customer_id, (current_customer_id IS NOT NULL));
            current_ticket_id := current_ticket_id + 1;
        END LOOP;

END $$





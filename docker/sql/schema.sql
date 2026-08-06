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
    duration INTERVAL NOT NULL,
    age_rating VARCHAR(255),
    movie_description TEXT DEFAULT ''
);

CREATE TABLE movie_sessions(
    movie_session_id SERIAL PRIMARY KEY,
    movie_id INTEGER NOT NULL,
    hall_id INTEGER NOT NULL,
    date_of_start TIMESTAMP NOT NULL,
    date_of_end TIMESTAMP NOT NULL,
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


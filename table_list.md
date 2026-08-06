## Таблицы

 Наименование таблицы | Размер таблицы | Размер таблицы с индексами 
----------------------+----------------+----------------------------
 tickets              | 436 MB         | 815 MB
 booked_tickets       | 257 MB         | 759 MB
 movie_sessions       | 6216 kB        | 11 MB
 movies               | 520 kB         | 776 kB
 users                | 56 kB          | 104 kB
 places               | 48 kB          | 80 kB
 customers            | 16 kB          | 64 kB
 hall_rows            | 8192 bytes     | 40 kB



## Индексы

 Наименование таблицы |       Наименование индекса        | Размер индекса 
----------------------+-----------------------------------+----------------
 booked_tickets       | one_customer_per_ticket           | 263 MB
 tickets              | per_one_place                     | 192 MB
 tickets              | tickets_pkey                      | 187 MB
 booked_tickets       | booked_tickets_pkey               | 187 MB
 booked_tickets       | booked_tickets_date_of_booked_idx | 51 MB
 movie_sessions       | movie_sessions_pkey               | 2312 kB
 movie_sessions       | movie_sessions_date_of_start_idx  | 1768 kB
 movie_sessions       | movie_sessions_movie_id_idx       | 816 kB
 movies               | movies_pkey                       | 128 kB
 movies               | movies_premier_date_idx           | 128 kB
 halls                | halls_pkey                        | 16 kB
 hall_rows            | hall_row_number_unique            | 16 kB
 users                | users_user_email_key              | 16 kB
 users                | users_user_login_key              | 16 kB
 customers            | customers_pkey                    | 16 kB
 customers            | customers_user_id_key             | 16 kB
 customers            | customers_customer_email_key      | 16 kB
 users                | users_pkey                        | 16 kB
 places               | places_pkey                       | 16 kB
 places               | hall_row_place_number_unique      | 16 kB
 hall_rows            | hall_rows_pkey                    | 16 kB

# php_professional_hw
For otus php professional course homeworks

# Логическая схема данных

```mermaid
erDiagram
    
    halls ||--|{ rows: "Зал, в котором содержатся ряды"
    rows || --|{ places: "Место, содержащаяся в ряду"
    halls {
        int hall_id
        string hall_name
        string DCI
    }
    rows {
        int row_id
        int hall_id
        tinyint row_number
    }
    places {
        int place_id
        int row_id
        tinyint place_number
    }
    movies{
        int movie_id
        string movie_name
        date premiere_date
        string genre
        string age_rating
        text movie_description    
    }
    movie_sessions{
        int movie_session_id
        int movie_id
        int hall_id
        datetime date_of_start
        datetime date_of_end
        decimal ticket_price
    }
    tickets{
        int ticket_id
        int movie_session_id
        int place_id
        decimal ticket_price
    }
    booked_tickets{
        int booked_ticket_id
        int ticket_id
        int customer_id
    }

    movies ||-- |{ movie_sessions: "Фильм, показываемый во время киносеанса"
    halls || -- |{ movie_sessions: "Кинозал, в котором показывается фильм"

    customers{
        int customer_id
        int user_id
        string customer_email
        string customer_phone
    }

    users {
        int user_id
        string user_name
        string user_login
        string user_email
        string user_password
        string user_role
    }
    movie_sessions ||--|{tickets: "билет на киносеанс"
    places ||--o{tickets: "Место в зале, за которым закреплен билет"
    tickets ||..||booked_tickets: "Билеты, которые являются купленными"
    customers}o..o{booked_tickets: "Покупка клиентом билета"
    users || .. o| customers: "Пользователь, который является покупателем билетов"
```
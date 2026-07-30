CREATE TABLE attribute_types(
    attribute_type_id SERIAL PRIMARY KEY,
    attribute_type_name VARCHAR(255) NOT NULL,
    attribute_type_type VARCHAR(255) NOT NULL
);

CREATE TABLE attributes(
    attribute_id SERIAL PRIMARY KEY,
    attribute_type_id INTEGER NOT NULL,
    attribute_name VARCHAR(255) NOT NULL,
    FOREIGN KEY(attribute_type_id) REFERENCES attribute_types(attribute_type_id)
);

CREATE TABLE attribute_values(
    attribute_value_id SERIAL PRIMARY KEY,
    movie_id INTEGER NOT NULL,
    attribute_id INTEGER NOT NULL,
    t_value TEXT,
    b_value BOOLEAN,
    d_value DATE,
    i_value INT,
    f_value FLOAT,
    FOREIGN KEY(movie_id) REFERENCES movies(movie_id),
    FOREIGN KEY(attribute_id) REFERENCES attributes(attribute_id)
);

CREATE EXTENSION IF NOT EXISTS tablefunc;

CREATE INDEX idx_attribute_values_movie_id ON attribute_values(movie_id);
CREATE INDEX idx_attribute_values_attribute_id ON attribute_values(attribute_id);


INSERT INTO attribute_types(attribute_type_name, attribute_type_type) VALUES
('Рецензии', 'string'),
('Премия', 'boolean'),
('Важные даты', 'date'),
('Служебные даты', 'date');

INSERT INTO attributes(attribute_type_id, attribute_name) VALUES
(1, 'Рецензии критиков'),
(1, 'Отзывы американского института киноискусства'),
(1, 'Отзывы Британской академии кино и телевизионных искусств');


INSERT INTO attributes(attribute_type_id, attribute_name) VALUES
(2, 'Оскар'),
(2, 'Ника'),
(2, 'Золотой глобус'),
(2, 'Золотая пальмовая ветвь');

INSERT INTO attributes(attribute_type_id, attribute_name) VALUES
(3, 'Мировая премьера'),
(3, 'Премьера в РФ'),
(3, 'Цифровой релиз');

INSERT INTO attributes(attribute_type_id, attribute_name) VALUES
(4, 'Дата начала печати билетов'),
(4, 'Дата начала продажи билетов'),
(4, 'Дата начала показа рекламы на ТВ'),
(4, 'Дата вывески баннеров');


WITH current_movies AS (
    SELECT movies.movie_id, attributes.attribute_id, attribute_types.attribute_type_id, attribute_types.attribute_type_type FROM movies CROSS JOIN attributes INNER JOIN attribute_types ON attributes.attribute_type_id = attribute_types.attribute_type_id
)
INSERT INTO attribute_values(movie_id, attribute_id, t_value, b_value, d_value, i_value, f_value)
SELECT 
movie_id, 
attribute_id,
CASE
    WHEN attribute_type_type = 'string' THEN ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vel justo vitae enim placerat luctus ut eu metus. Integer ac diam vel ante fermentum condimentum. Integer eget dignissim lectus. Donec quis consequat massa. Aenean et diam ac lorem egestas venenatis a vel nunc. Sed mollis elementum magna, eu pharetra nulla congue vitae. Donec rhoncus, erat vel suscipit hendrerit, massa lacus mattis tellus, vel tempus ligula nisi at erat. Proin varius venenatis bibendum. '
    ELSE null
END AS t_value,
CASE
    WHEN attribute_type_type = 'boolean' THEN (ARRAY[false, true])[floor(random() * 2) + 1]
    ELSE NULL
END AS b_value,
CASE
    WHEN attribute_type_type = 'date' THEN (NOW() + ((24 * (ARRAY[1,2,3,4,5,6,7,10,15,20,30])[floor(random() * 11) + 1]) || ' hours')::interval)::date
    ELSE NULL
END AS d_value,
CASE
    WHEN attribute_type_type = 'integer' THEN (ARRAY[1,2,3,4,5,6,7,8,9,10])[floor(random() * 10) + 1]
    ELSE NULL
END AS i_value,
CASE
    WHEN attribute_type_type = 'float' THEN (ARRAY[1.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0])[floor(random() * 10) + 1]
    ELSE NULL
END AS f_value

FROM current_movies;



CREATE VIEW service_data AS SELECT * FROM crosstab('SELECT 
m.movie_name,
CASE 
    WHEN av.d_value::timestamp BETWEEN NOW() AND NOW() + (24 || '' hours'')::interval THEN ''Задачи на сегодня''
    WHEN av.d_value::timestamp >= NOW() + (20 || '' days'')::interval THEN ''Задачи через 20 дней''
END AS task_description,
a.attribute_name
FROM movies AS m
INNER JOIN attribute_values AS av ON m.movie_id = av.movie_id
INNER JOIN attributes AS a ON av.attribute_id = a.attribute_id
WHERE a.attribute_type_id = 4 AND (av.d_value::timestamp BETWEEN NOW() AND NOW() + (24 || '' hours'')::interval OR av.d_value::timestamp >= NOW() + (20 || '' days'')::interval)
ORDER BY 1,2')
AS ct(movie_name VARCHAR(255), "Задачи на сегодня" VARCHAR(255), "Задачи через 20 дней" VARCHAR(255));


CREATE VIEW marketing_data AS SELECT 
m.movie_name,
attr_t.attribute_type_name,
attr.attribute_name,
CASE
    WHEN attr_t.attribute_type_type = 'string' THEN attr_v.t_value
    WHEN attr_t.attribute_type_type = 'boolean' THEN attr_v.b_value::text
    WHEN attr_t.attribute_type_type = 'date' THEN attr_v.d_value::text
    WHEN attr_t.attribute_type_type = 'integer' THEN attr_v.i_value::text
    WHEN attr_t.attribute_type_type = 'float' THEN attr_v.f_value::text
END AS attribute_value
FROM movies AS m
INNER JOIN attribute_values AS attr_v ON m.movie_id = attr_v.movie_id
INNER JOIN attributes AS attr ON attr_v.attribute_id = attr.attribute_id
INNER JOIN attribute_types AS attr_t ON attr.attribute_type_id = attr_t.attribute_type_id;

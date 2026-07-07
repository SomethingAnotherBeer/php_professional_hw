SET NAMES 'utf8mb4';

DROP TABLE if EXISTS users;

CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    user_login VARCHAR(255) UNIQUE NOT NULL,
    user_password VARCHAR(255) NOT NULL

);

INSERT INTO users (id, user_name, user_login, user_password) VALUES 
    (1, 'Осаму Дазай', 'dazai', '$2y$10$AnVtDCjjUZirAQ1Nfuf6MerMG/PqqgS7HocjqjSA1kWxzp1Zvnrqi'),
    (2, 'Ранпо Эдогава', 'ranpo', '$2y$10$GluwCOYoiNcDevRH.jHjGee7jvT01QW924r5zJtH9CVCaq1VCzPK2'),
    (3, 'Акико Йосано', 'yosana', '$2y$10$ndrf2pIDJFw7VInhsSd25.HwnkcbxwWaDHplrEkxRMxwSSGAPnfRG');

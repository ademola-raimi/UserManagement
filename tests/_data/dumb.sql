CREATE TABLE users (id INTEGER PRIMARY KEY AUTO_INCREMENT, first_name varchar(100) NOT NULL, last_name varchar(100) NOT NULL, email varchar(100) NOT NULL, created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL, UNIQUE (email));
INSERT INTO users (first_name, last_name, email) VALUES ('Ademola', 'Raimi', 'ademola@example.com');
INSERT INTO users (first_name, last_name, email) VALUES ('Gorbach', 'Pavel', 'pavel@example.com');
INSERT INTO users (first_name, last_name, email) VALUES ('Stefan', 'Knittel', 'stefan@example.com');
INSERT INTO users (first_name, last_name, email) VALUES ('John', 'Doe', 'john@example.com');
INSERT INTO users (first_name, last_name, email) VALUES ('Gotye', 'Mirrors', 'gotye@example.com');

CREATE TABLE files (
  id SERIAL NOT NULL,
  name CHARACTER VARYING(255) NOT NULL,
  original_name CHARACTER VARYING(255) NOT NULL,
  path CHARACTER VARYING(255) NOT NULL,
  thumbnail_path CHARACTER VARYING(255),
  description CHARACTER VARYING(120),
  size INTEGER NOT NULL,
  mime_type CHARACTER VARYING(255),
  date TIMESTAMP DEFAULT current_timestamp,
  downloads INTEGER DEFAULT 0,
  user_id INTEGER,
  user_token CHARACTER VARYING(64),
  media_info text,
  PRIMARY KEY (id),
  UNIQUE (name)
);

COMMENT ON COLUMN files.user_token IS 'token for anonymous upload';

CREATE TABLE users (
  id SERIAL NOT NULL,
  name CHARACTER VARYING(30),
  avatar CHARACTER VARYING(255),
  email CHARACTER VARYING(255),
  password CHARACTER VARYING(255),
  auth_token CHARACTER VARYING(64),
  PRIMARY KEY (id),
  UNIQUE (name, email, auth_token)
);

ALTER TABLE files
  ADD FOREIGN KEY (user_id) REFERENCES users(id);

CREATE TABLE comments (
  id SERIAL NOT NULL,
  file_id INTEGER NOT NULL,
  parent_id INTEGER,
  user_id INTEGER,
  date TIMESTAMP DEFAULT current_timestamp,
  text CHARACTER VARYING(250),
  matpath CHARACTER VARYING(255),
  PRIMARY KEY (id)
);

COMMENT ON COLUMN comments.matpath IS 'Materialized path';

ALTER TABLE comments
  ADD FOREIGN KEY (file_id) REFERENCES files(id);

ALTER TABLE comments
  ADD FOREIGN KEY (parent_id) REFERENCES comments(id);

ALTER TABLE comments
  ADD FOREIGN KEY (user_id) REFERENCES users(id);

INSERT INTO users (name, email, password, auth_token)
VALUES ('testuser', 'testuser@example.com', '12345678', '013ef89f6d17841a2ac8c35b20q62b1c');

INSERT INTO files (name, original_name, path, thumbnail_path, size, mime_type, user_id, media_info)
VALUES ('testfile', 'test.jpg', '/var/www/uppu/tests/_data/testfile', '', '2054548', 'image/jpeg', 1, '{"dataformat":"jpg","resolution":"800x600","bits_per_sample":24}');

INSERT INTO comments (file_id, parent_id, user_id, text, matpath)
VALUES (1, NULL, 1, 'первый', '001');

INSERT INTO comments (file_id, parent_id, user_id, text, matpath)
VALUES (1, NULL, NULL , 'второй', '002');

INSERT INTO comments (file_id, parent_id, user_id, text, matpath)
VALUES (1, NULL, NULL , 'третий', '003');

INSERT INTO comments (file_id, parent_id, user_id, text, matpath)
VALUES (1, NULL, NULL , 'первый.первый', '001.001');

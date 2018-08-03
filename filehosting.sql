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

COMMENT ON COLUMN files.user_token IS 'For anonymous upload';

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
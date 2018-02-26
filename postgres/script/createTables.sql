CREATE TYPE diff AS ENUM ('1', '2', '3');
CREATE TABLE IF NOT EXISTS recipes(
  id BIGSERIAL PRIMARY KEY,
  rname VARCHAR(50) NOT NULL,
  prep_time INT NOT NULL,
  difficulty diff DEFAULT '1',
  veg bool,
  status bool DEFAULT TRUE,
  created_at DATE NULL,
  updated_at DATE NULL,
  CONSTRAINT rname_unique UNIQUE (rname)
);

CREATE TABLE IF NOT EXISTS ratings(
  id BIGSERIAL PRIMARY KEY,
  rate INT NOT NULL,
  recipe_id INT NOT NULL
);

CREATE TABLE IF NOT EXISTS users(
  id BIGSERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  token VARCHAR(50) NULL,
  secret VARCHAR(255) NULL,
  valid_upto INT NULL
);

INSERT INTO recipes (rname, prep_time, difficulty, veg, status, created_at) VALUES ('Biriyani', 3, '2', false, true, '2018-02-20'), ('Pizza', 2, '2', true, true, '2018-02-20'), ('Rosogolla', 2, '3', true, true, '2018-02-20');

INSERT INTO users (username, password) VALUES ('recipe', '$2y$10$yVtxdmKCVB.mX.MXspw1BumQRq8Oeoza7G9HmV5Qfj3YOqOfEUtwK');
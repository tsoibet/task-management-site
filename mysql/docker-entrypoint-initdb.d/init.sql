DROP SCHEMA IF EXISTS TMSITE;
CREATE SCHEMA TMSITE;
USE TMSITE;
DROP TABLE IF EXISTS TASK;
CREATE TABLE TASK
(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(50) NOT NULL,
  detail TEXT NOT NULL,
  `status` ENUM('To do', 'In progress', 'Done') NOT NULL,
  `priority` ENUM('1', '2', '3') NOT NULL,
  deadlineness BOOLEAN NOT NULL,
  deadline DATETIME NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO TASK (title, detail, `status`, `priority`, deadlineness, deadline)
VALUES
('Hello World!', 'I have a dog.', 'To do', '1', TRUE, '2020-02-01'),
('Example 2', 'My dog''s name is Nora.', 'In progress', '2', FALSE, '9999-12-31');
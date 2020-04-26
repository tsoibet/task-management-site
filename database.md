# Database

## Initialize data base
To initialize the database in docker, you can setup a sql file and mount it to docker for initialization.

1. Create a `mysql` folder.
2. Create a `docker-entrypoint-initdb.d` folder inside `mysql` folder.
3. Create an `init.sql` file inside `docker-entrypoint-initdb.d` folder.
4. Prepare the `init.sql` file, e.g.

```sql
DROP SCHEMA IF EXISTS <YOUR_SCHEMA_NAME>;
CREATE SCHEMA <YOUR_SCHEMA_NAME>;
USE <YOUR_SCHEMA_NAME>;
DROP TABLE IF EXISTS <YOUR_TABLE_NAME>;
CREATE TABLE <YOUR_TABLE_NAME>;
(
  id INT ...
  ...
);
-- Insert some data if you need
INSERT INTO <YOUR_TABLE_NAME> (id, ...) VALUES (...);
```

5. Update `docker-compose.yml`, under `mysql` part, insert:

```yml
        volumes:
            - $PWD/mysql/docker-entrypoint-initdb.d:/docker-entrypoint-initdb.d
```

6. Restart docker

```bash
$ docker-compose down
$ docker-compose up
```
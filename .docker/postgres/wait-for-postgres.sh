#!/bin/bash
# wait-for-postgres.sh

# Try to connect to PostgreSQL
until PGPASSWORD="qwerty" psql -h "postgres" -U "filehosting" -d "filehosting_testing" -c '\q'; do
  >&2 echo "Postgres is unavailable - sleeping"
  sleep 1
done

>&2 echo "Postgres is up - executing command"

# Execute given other parameters (commands)
exec "$@"
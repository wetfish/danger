# danger.wetfish.net

## How do I deploy this container stack?

See [https://github.com/wetfish/server](https://github.com/wetfish/server)
for setting up a server. Then add danger to it with

```bash
./add-project.sh --name danger --repo https://github.com/wetfish/danger.git --domain <domain>
```

It sets everything up from [setup.conf](setup.conf): danger gets its own database on the server's
shared database, and its domain for post links, so there's nothing to enter by hand.
After that it'll redeploy whenever `main` (staging) or `release` (production) is pushed.

The server creates danger's tables from schema.sql when it first creates danger's database,
and never runs it again.

### Moving posts from an old server

On a server still running the old `danger-db` container, dump just the posts, without the old
table's definition:

```bash
docker exec danger-db sh -c 'exec mariadb-dump --user=root --password="$MARIADB_ROOT_PASSWORD" --no-create-info --insert-ignore --default-character-set=utf8mb4 "$MARIADB_DATABASE"' > danger.sql
```

then copy `danger.sql` to the new server and, after danger's first deploy has created its table
from schema.sql, load the posts into it from wetfish/server's directory:

```bash
./database.sh import danger < danger.sql
```

Posts already in the new database are skipped, so to pick up posts made on the old server since,
dump and import again.

## Development

To run just this stack, with its own database, do
```bash
# setup a /etc/hosts entry to match SITE_URL
# 127.0.0.1   danger.wetfish.net.local

cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change passwords to match

docker compose up -d --build

docker compose logs -f
```

The service will be available at [http://danger.wetfish.net.local:2402](http://danger.wetfish.net.local:2402)

The database and port only exist in docker-compose.override.yml, which servers leave out.

If you want to test it like it's on a real server, set up a
[local copy](https://github.com/wetfish/server/blob/main/docs/how-it-works.md#a-local-copy-for-testing)
of wetfish/server and add your clone by its path, with a domain like `danger.localhost`. \
Heads up, it only deploys committed changes!

## When do I need to rebuild the container?

Whenever you make an edit in public_html, package.json, php.ini, or nginx.conf. \
schema.sql only runs in a new, empty database, so changes to it never reach an existing one.
To start over locally with a new database, run `docker compose down --volumes`.

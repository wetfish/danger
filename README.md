# danger.wetfish.net

## How do I deploy this container stack?

See [https://github.com/wetfish/server](https://github.com/wetfish/server)
for setting up a server. Then add danger to it with

```bash
./add-project.sh --name danger --repo https://github.com/wetfish/danger.git --domain <domain>
```

The first deploy fails until danger has its database settings. Create them in
the server's clone of danger, then deploy again:

```bash
cd /srv/wetfish-server/projects/danger
cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change passwords to match, and set SITE_URL to the domain

cd /srv/wetfish-server
./deploy.sh danger
```

After that it'll redeploy whenever `main` (staging) or `release` (production) is pushed.

### Moving posts from an old server

Posts live in the `danger_db` Docker volume. To bring them over from a server
running the old `danger-db` container, dump them there:

```bash
docker exec danger-db sh -c 'exec mariadb-dump --user=root --password="$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' > danger.sql
```

then copy `danger.sql` to the new server, after danger's first successful deploy, and load it:

```bash
docker exec -i danger-db-1 sh -c 'exec mariadb --user=root --password="$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' < danger.sql
```

## Development

To run just this stack, do
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

If you want to test it like it's on a real server, set up a
[local copy](https://github.com/wetfish/server/blob/main/docs/how-it-works.md#a-local-copy-for-testing)
of wetfish/server and add your clone by its path, with a domain like `danger.localhost`. \
Heads up, it only deploys committed changes!

## When do I need to rebuild the container?

Whenever you make an edit in wwwroot, config, nginx.conf, or schema.sql. \
schema.sql only runs when the database volume is empty; to start over locally, run `docker compose down --volumes`.

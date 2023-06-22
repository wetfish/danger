# danger.wetfish.net

## How do I deploy this container stack?

See [https://github.com/wetfish/production-manifests](https://github.com/wetfish/production-manifests)
for production deployment and full stack dev env info.

For development, to run just this stack, do 
```bash
# setup a /etc/hosts entry to match SITE_URL
# 127.0.0.1   danger.wetfish.net.local

cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change passwords to match

docker compose \
  -f docker-compose.dev.yml \
  up -d \
  --build \
  --force-recreate

docker compose -f docker-compose.dev.yml logs -f
```

The service will be available at [http://danger.wetfish.net.local:2402](http://danger.wetfish.net.local:2402)

## When do I need to rebuild the container?

Only if you touch Dockerfile. \
Application code is not in the php-fpm container image.

# danger.wetfish.net

## How do I deploy this container stack?

See [https://github.com/wetfish/production-manifests](https://github.com/wetfish/production-manifests)
for production deployment and full stack dev env info.

For local development of this repo only, edit docker-compose.yml,
uncommenting the port exposure stanza,
and commenting out everything `traefik-backend` related.

Either way, you'll want to copy `php.env.example` and `mariadb.env.example` to `php.env` and `mariadb.env` and set some real passwords.

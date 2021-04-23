# The Channel Video Archive

## Getting started

```sh
cp .env.example .env
```

**Docker**

Install the correct version of Docker for your operating system.

```sh
cat .env.example.docker >> .env
```
Check that the correct php image is selected in .env (choice depends on host operating system).

```sh
make up
```

```sh
# When running php-based tools and Docker, prefix commands with:
docker-compose exec php <command>

# e.g.
docker-compose exec php composer install

# and
docker-compose exec php php artisan key:generate
```

Note the double `php` in the second command above. The first `php` refers to the name of the Docker service, the second refers to the command to invoke `php` on the command line.

### Install frontend dependencies.
npm install

### Build frontend and watch for changes.
npm run hot

You should now be able to access the site at [http://hv.docker.localhost:8001/](http://hv.docker.localhost:8001/).

### Front-end notes

Most of the time during development you'll likely just want to run `npm run hot`, but a full list of commands is located in the `scripts` section of the `package.json`.

Please don't bypass or disable the linting rules or webpack configuration we have setup yourself. They are there for everyone's benefit (including the user), but if they do become particularly annoying please feel free to suggest changes.

Stylelint is currently enforcing a rule aimed at reducing nesting and encourages BEM style classes. [If you aren't familiar in writing BEM, you should read the supporting documentation](./docs/BEM.md).

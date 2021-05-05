# Getting started

The project uses [Docker4PHP](https://wodby.com/docs/1.0/stacks/php/local/#local-environment-with-docker4php) for creating a local development environment and uses traefik for container routing. By default, we use port 8000 to avoid potential conflicts but if port 80 is free on your host machine just replace traefik's ports definition in the compose file.

By default the BASE_URL is set to channel.docker.localhost, you can change it in the .env file.

Add 127.0.0.1 channel.docker.localhost to your /etc/hosts file (some browsers like Chrome may work without it).

## Requirements

- For local development you will need:

  - [Docker](https://docs.docker.com/get-docker)
  - Node (best installed via nvm)

- You should first setup the [Channel Backend](https://github.com/HammerMuseum/channel-backend) project as it provides a back end data source for this application.

## Setting up a local environment

1. [Install Docker](https://docs.docker.com/get-docker) for your operating system.

2. Copy the example env file to create a local env file.

```sh
cp .env.example .env
```

3.Populate the env file with some additional variables

```sh
cat .env.example.docker >> .env
```

4. Ensure that the correct php image is selected in the .env file for your host operating system.

5. Launch the containers

```sh
make up
```

6. Install dependencies

```sh
docker-compose exec php composer install
```

7. Generate Laravel application key

```sh
docker-compose exec php php artisan key:generate
```

8. Install front end dependencies

```sh
npm install
```

9. Build the front end and watch for changes

```sh
npm run hot
```

You should now be able to access the application at [http://channel.docker.localhost:8001/](http://channel.docker.localhost:8001/).

### Front-end notes

Most of the time during development you'll likely just want to run `npm run hot`, but a full list of commands is located in the `scripts` section of the `package.json`.

Please don't bypass or disable the linting rules or webpack configuration we have setup yourself. They are there for everyone's benefit (including the user), but if they do become particularly annoying please feel free to suggest changes.

Stylelint is currently enforcing a rule aimed at reducing nesting and encourages BEM style classes. [If you aren't familiar in writing BEM, you should read the supporting documentation](./docs/BEM.md).

### Running php-based commands inside the container

```sh
# When running php-based tools and Docker, prefix commands with:
docker-compose exec php artisan config:cache
```

Note the double `php` in the second command above. The first `php` refers to the name of the Docker service, the second refers to the command to invoke `php` on the command line.

## Developer Hub

Search developers on github by username!

#### Introduction:

This is a simple approach to build a decoupled application using DDD, Hexagonal Architecture, and CQRS.

This application should be used only as example to apply some best practices. For the moment is an incomplete example, only is used as proof of concept.

By the way ... this application only use next concepts (for time being):

- query bus
- Value object
- Read model

### Requirements

- Docker Compose ^ 3.4

### Setup

1. clone this project:

```
https://github.com/juancama/devhub.git
```

2. default setup:

```
make setup
```

Port is not available? you can change.

```
# .env

DEVELOPER_HUB_WEB_PORT=<yourPort>
```

3. Setup your personal token (https://github.com/settings/tokens)

```
# ./applications/developer-hub/.env

GITHUB_USER=<username>
GITHUB_TOKEN=<token
```

4. Run app!

```
make serve
```

### UI

- Web search:

go to http://localhost:3200


- CLI search:

```
make search-developer username="juancama"
```


### Show Other Recipes

```
$ make help

setup First time application setup
serve Up application
tests Run all tests
unit-tests Run unit tests
integration-tests Run integration tests
composer Execute composer command. Example: make composer cmd="require ramsey/uuid"
```

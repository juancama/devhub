-include .env
-include makefiles/parameters.makefile

.DEFAULT_GOAL := help

help:
	@echo "$(GREEN)serve$(END) Up application"
	@echo "$(GREEN)tests$(END) Run all tests"
	@echo "$(GREEN)unit-tests$(END) Run unit tests"
	@echo "$(GREEN)integration-tests$(END) Run integration tests"
	@echo "$(GREEN)composer$(END) Execute composer command. Example: $(BLUE)make composer cmd=\"require ramsey/uuid\"$(END)"
.PHONY: help

composer:
	docker run --rm --interactive --volume $(PWD):/app --workdir=/app composer $(cmd) --ignore-platform-reqs
.PHONY: composer

docker-compose:
	docker-compose -p $(DOCKER_COMPOSER_PN) $(cmd)
.PHONY: docker-compose

serve:
	$(MAKE) docker-compose cmd="down -v --remove-orphans" \
	&& $(MAKE) docker-compose cmd=stop \
	&& $(MAKE) docker-compose cmd="up -d developer-hub phpunit"
	@echo "$(YELLOW)developer-hub: $(END) '$(BLUE)$(APP_BASE_URL):$(DEVELOPER_HUB_WEB_PORT)$(END)'";
.PHONY: serve

unit-tests:
	docker exec -i devhub_phpunit_1 ./vendor/bin/phpunit --colors=always --exclude-group integration $(extra)
.PHONY: unit-tests

integration-tests:
	docker exec -i --workdir=/var/www/html/devhub $(DEVELOPER_HUB_CONTAINER) ./vendor/bin/phpunit \
	--configuration /var/www/html/devhub/applications/developer-hub/phpunit.xml \
	--colors=always --group integration --exclude-group external $(extra)
.PHONY: integration-tests

tests:
	- $(MAKE) unit-tests --no-print-directory
	- $(MAKE) integration-tests --no-print-directory
.PHONY: tests

search-developer:
	$(MAKE) cli cmd="colvin:search-developer $(username)"
.PHONY: search-developer

cli:
	docker exec -i --workdir=/var/www/html/devhub $(DEVELOPER_HUB_CONTAINER) ./applications/developer-hub/bin/console $(cmd)
.PHONY: cli

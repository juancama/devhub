-include .env
-include makefiles/parameters.makefile

.DEFAULT_GOAL := help

help:
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

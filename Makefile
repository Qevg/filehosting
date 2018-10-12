configure-docker-production:
	@cp .docker/configure/production/config/* config

configure-docker-testing:
	@cp .docker/configure/testing/config/* config
	@cp .docker/configure/testing/sphinx/* tests/_data/sphinxsearch/data
	@cp .docker/configure/testing/codecept/codeception.yml codeception.yml
	@cp .docker/configure/testing/codecept/tests/* tests
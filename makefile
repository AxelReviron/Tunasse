tunasse-docker:
	docker exec -it tunasse_app bash

laraclean:
	./vendor/bin/pint && ./vendor/bin/phpstan analyse --memory-limit 1G

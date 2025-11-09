tunasse-docker:
	docker exec -it tunasse bash

laraclean:
	./vendor/bin/pint && ./vendor/bin/phpstan analyse --memory-limit 1G

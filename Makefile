build:
	docker build -t incubator-cache .

exec:
	docker run --rm -it \
		-v $(CURDIR):/app \
		--network dev \
		incubator-cache bash

dbs:
	docker run --rm -it \
		-v $(CURDIR):/app \
		--network dev \
		-e MYSQL_ROOT_PASSWORD=phalcon \
		mariadb

	docker run --rm -it \
		-v $(CURDIR):/app \
		--network dev \
		aerospike

test:
	docker run --rm -it \
		-v $(CURDIR):/app \
		--network dev \
		incubator-cache php -d display_errors=On -d error_reporting=E_ALL vendor/bin/codecept run

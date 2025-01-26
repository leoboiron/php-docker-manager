build:
	docker build -f tests/Dockerfile -t php-docker-manager:test .

test:
	docker run --rm -v /var/run/docker.sock:/var/run/docker.sock -v $(PWD):/app php-docker-manager:test php tests/app.php

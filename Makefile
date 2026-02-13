.PHONY: build run init migrate wait-db assets

UNAME_S := $(shell uname -s)
WIN_CURDIR := $(shell pwd -W 2>/dev/null || pwd)
WSL_CURDIR := $(shell wsl -d Ubuntu -- wslpath -a "$(WIN_CURDIR)" 2>/dev/null | tr -d '\r')

build:
	docker compose -f docker-compose.yaml build

run:
	docker compose -f docker-compose.yaml up -d

wait-db:
	docker compose -f docker-compose.yaml exec -T database sh -c 'until mysqladmin ping -h 127.0.0.1 -p"$$MYSQL_ROOT_PASSWORD" --silent; do sleep 1; done'

migrate: wait-db
	docker compose -f docker-compose.yaml exec -T fv-replowed php artisan migrate --seed

init: build run migrate

assets:
ifeq ($(findstring MINGW,$(UNAME_S)),MINGW)
	test -f scripts/fetch-assets.sh || (echo "scripts/fetch-assets.sh is local-only; create it before running assets." && exit 1)
	wsl -d Ubuntu -- bash -lc "cd \"$(WSL_CURDIR)\" && ./scripts/fetch-assets.sh"
else
	@if [ ! -f ./scripts/fetch-assets.sh ]; then \
		echo "scripts/fetch-assets.sh is local-only; create it before running assets."; \
		exit 1; \
	fi
	./scripts/fetch-assets.sh
endif

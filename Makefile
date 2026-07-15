.PHONY: build no-vnc run init migrate assets items db-wait

UNAME_S := $(shell uname -s)
WIN_CURDIR := $(shell pwd -W 2>/dev/null || pwd)
WSL_CURDIR := $(shell wsl -d Ubuntu -- wslpath -a "$(WIN_CURDIR)" 2>/dev/null | tr -d '\r')

build:
	docker compose -f docker-compose.yaml build

no-vnc:
	docker compose -f docker-compose.yaml build fv-replowed
	docker compose -f docker-compose.yaml up -d database fv-replowed

run:
	docker compose -f docker-compose.yaml up -d

db-wait:
	@echo "Waiting for database to be ready..."
	@i=0; \
	until docker compose -f docker-compose.yaml exec -T database sh -lc 'MYSQL_PWD="$$MARIADB_ROOT_PASSWORD" mariadb -uroot -e "SELECT 1" >/dev/null 2>&1'; do \
		i=$$((i+1)); \
		if [ $$i -ge 30 ]; then \
			echo "Database is not ready after $${i} attempts."; \
			echo "Ensure the database container is running: make run"; \
			exit 1; \
		fi; \
		sleep 2; \
	done

migrate: db-wait
	docker compose exec fv-replowed php artisan migrate --seed

init: build run migrate

assets:
ifeq ($(findstring MINGW,$(UNAME_S)),MINGW)
	wsl -d Ubuntu -- bash -lc "cd \"$(WSL_CURDIR)\" && ./scripts/fetch-assets.sh"
else
	./scripts/fetch-assets.sh
endif

items:
	@if [ ! -f .cache/fv-assets/farmvilledb_trimmed.sql ]; then \
		echo "Missing .cache/fv-assets/farmvilledb_trimmed.sql. Run: make assets"; \
		exit 1; \
	fi
	docker compose -f docker-compose.yaml exec -T database sh -lc 'cat > /tmp/farmvilledb_trimmed.sql' < .cache/fv-assets/farmvilledb_trimmed.sql
	docker compose -f docker-compose.yaml exec -T database sh -lc 'mariadb -uroot -p"$$MARIADB_ROOT_PASSWORD" farmvilledb < /tmp/farmvilledb_trimmed.sql'
	@echo "Imported items SQL into farmvilledb."

.PHONY: up down bash logs test

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f app

test:
	docker compose exec app php artisan test

bash:
	@echo "Selecione o usuario para acessar o container:"
	@echo "1 - root"
	@echo "2 - appuser"
	@printf "Opcao (1 ou 2): "; \
	read choice; \
	if [ "$$choice" = "1" ]; then \
		docker compose exec -u root app bash; \
	elif [ "$$choice" = "2" ]; then \
		docker compose exec -u $$(id -u):$$(id -g) app bash; \
	else \
		echo "Opcao invalida. Use 1 ou 2."; \
	fi

.PHONY: format lint test fresh serve clear

fmt:
	./vendor/bin/pint

lint:
	./vendor/bin/pint --test

test:
	php artisan test

fresh:
	php artisan migrate:fresh --seed

serve:
	php artisan serve

clear:
	php artisan optimize:clear

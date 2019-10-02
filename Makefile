composer-install:
	cd ../../../ && docker-compose exec -w /var/www/html/packages/basic_table_package concrete5 composer install

composer-install-prod:
	cd ../../../ && docker-compose exec -w /var/www/html/packages/basic_table_package concrete5 composer install --no-dev

composer-update:
	cd ../../../ && docker-compose exec -w /var/www/html/packages/basic_table_package concrete5 composer update

#to install the package, installation without dev is needed.
#that doctrine is not getting in the way of installing the package
composer-update-pord:
	cd ../../../ && docker-compose exec -w /var/www/html/packages/basic_table_package concrete5 composer update --no-dev
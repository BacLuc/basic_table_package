CWD=cd ../../../
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

sync:
	${CWD} && docker-compose exec rsync rsync --delete -rt /mnt/html/packages/basic_table_package/ /var/www/html/packages/basic_table_package/

#for windows
run-tests: sync
	${CWD} && docker-compose exec concrete5 php concrete/vendor/phpunit/phpunit/phpunit --no-configuration /var/www/html/packages/basic_table_package/tests

update-schema: sync
	${CWD} && docker-compose exec concrete5 su -s /bin/bash  www-data -c "concrete/bin/concrete5 c5:entities:refresh"
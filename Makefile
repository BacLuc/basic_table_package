CWD=cd ../../../
composer-install:
	cd ../../../ && docker-compose exec -w /var/www/html/packages/bacluc_c5_crud concrete5 composerpkg install

composer-install-prod:
	cd ../../../ && docker-compose exec -w /var/www/html/packages/bacluc_c5_crud concrete5 composerpkg install --no-dev

composer-update:
	cd ../../../ && docker-compose exec -w /var/www/html/packages/bacluc_c5_crud concrete5 composerpkg update

#to install the package, installation without dev is needed.
#that doctrine is not getting in the way of installing the package
composer-update-pord:
	cd ../../../ && docker-compose exec -w /var/www/html/packages/bacluc_c5_crud concrete5 composerpkg update --no-dev

sync:
	${CWD} && docker-compose exec rsync rsync --delete -rt /mnt/html/packages/bacluc_c5_crud/ /var/www/html/packages/bacluc_c5_crud/

#for windows
run-tests: sync
	${CWD} && docker-compose exec concrete5 php concrete/vendor/phpunit/phpunit/phpunit --no-configuration /var/www/html/packages/bacluc_c5_crud/tests

update-schema: sync
	${CWD} && docker-compose exec concrete5 su -s /bin/bash  www-data -c "concrete/bin/concrete5 c5:entities:refresh"
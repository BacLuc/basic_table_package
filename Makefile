CWD=cd ../../../
composer-install:
	${CWD} && docker-compose exec \
						-w /var/www/html/packages/bacluc_c5_crud \
						--user www-data \
						concrete5 \
						composer install

composer-install-prod:
	${CWD} && docker-compose exec \
						-w /var/www/html/packages/bacluc_c5_crud \
						--user www-data \
						concrete5 \
						bash -c "composerpkg install --no-dev && rm composer-patched*.json || true"

composer-update:
	${CWD} && docker-compose exec \
						-w /var/www/html/packages/bacluc_c5_crud \
						--user www-data \
						concrete5 \
						composer update --lock

sync:
	${CWD} && docker-compose exec rsync rsync --delete -rt /mnt/html/packages/bacluc_c5_crud/ /var/www/html/packages/bacluc_c5_crud/

#for windows
run-tests: sync
	${CWD} && docker-compose exec \
							-w /var/www/html/packages/bacluc_c5_crud \
							--user www-data  \
							concrete5 \
							php vendor/phpunit/phpunit/phpunit \
							--configuration tests/phpunit.xml \
							--coverage-html coverage \
							/var/www/html/packages/bacluc_c5_crud/tests \


update-schema: sync
	${CWD} && docker-compose exec concrete5 su -s /bin/bash  www-data -c "concrete/bin/concrete5 c5:entities:refresh"
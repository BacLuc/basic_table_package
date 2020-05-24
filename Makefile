CWD=cd ../../../
#we need to remove the concrete5/core dependency for development
#but not for composerpkg and composer based installation
replace-composer-json-dev:
	mv composer.json composer.json.backup
	sed '/concrete5\/core/d' composer.json.backup > composer.json

do-composer-install:
	${CWD} && docker-compose exec \
    						-w /var/www/html/packages/bacluc_c5_crud \
    						--user concrete5 \
    						concrete5 \
    						composer install || true

restore-composer-json:
	mv composer.json.backup composer.json
	chmod 775 composer.json

composer-install-prod:
	${CWD} && docker-compose exec \
						-w /var/www/html/packages/bacluc_c5_crud \
						--user concrete5 \
						concrete5 \
						bash -c "composerpkg install --no-dev && rm composer-patched*.json || true"

do-composer-update:
	${CWD} && docker-compose exec \
						-w /var/www/html/packages/bacluc_c5_crud \
						--user concrete5 \
						concrete5 \
						composer update --lock

sync:
	${CWD} && docker-compose exec rsync rsync --delete -rt /mnt/html/packages/bacluc_c5_crud/ /var/www/html/packages/bacluc_c5_crud/

composer-install: replace-composer-json-dev sync do-composer-install restore-composer-json sync

composer-update: replace-composer-json-dev sync do-composer-update restore-composer-json sync

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
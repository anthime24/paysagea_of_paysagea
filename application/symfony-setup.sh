#!/bin/bash
echo 'alias sf="php bin/console"' >> ~/.bashrc
echo 'alias dsuf="php bin/console doctrine:schema:update --force"' >> ~/.bashrc
echo 'alias dsv="php bin/console doctrine:schema:validate"' >> ~/.bashrc
echo 'alias transfr="php bin/console translation:update --force fr"' >> ~/.bashrc
echo 'alias transen="php bin/console translation:update --force en"' >> ~/.bashrc

cd ${APACHE_ROOT}
COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --prefer-dist --no-interaction --no-cache
php bin/console assets:install --symlink public

chmod 777 -R ${LOG_PATH} ${CACHE_PATH}

if  [[ $1 = "--cp-vendor" ]]; then
  cp -r ${VENDOR_PATH} ${APACHE_ROOT}
fi

yarn install
yarn dev
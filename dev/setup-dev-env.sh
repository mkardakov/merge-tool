#!/bin/bash

set -o nounset
set -o errexit

echo -e "==== Setting up Dev environment ====\n"

yes | docker-compose -f docker/docker-compose.yml up -d \
     && docker-compose -f docker/docker-compose.yml run --rm api-skeleton-php bash -c "rm -rf var/cache" \
     && docker-compose -f docker/docker-compose.yml run --rm api-skeleton-php bash -c "composer install --no-interaction" \

readonly last_exit_code=$?
if [[ $last_exit_code -ne 0 ]]; then exit $last_exit_code; fi

echo -e "\n==== DONE ====\nAccess links:"
echo "API Skeleton App: http://#DOCKER_MACHINE_IP#:8080/"
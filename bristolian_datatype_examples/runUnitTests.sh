#!/usr/bin/env bash

set -e

cd "$(dirname "$0")"

php ../vendor/bin/phpunit -c ./phpunit.xml "$@"

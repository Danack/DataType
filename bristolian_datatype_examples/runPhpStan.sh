#!/usr/bin/env bash

set -e

cd "$(dirname "$0")"

php phpstan.phar analyze -c ./phpstan.neon "$@"

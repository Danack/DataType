#!/usr/bin/env bash

rm infection.log

php infection.phar


infection_exit_code=$?

# set -e
# set -x

if [ ! -f infection.log ]; then
    echo "infection.log log not generated."
    exit -1;
fi

cat infection.log

if [ "$infection_exit_code" -ne "0" ]; then echo "Infection failed"; exit "$infection_exit_code"; fi

# This is here as the output of mutation tests can be confused
# with the output of the unit tests. This cost me 3 hours when I learnt that.
echo "*********************************"
echo "** End of mutation tests       **"
echo "*********************************"
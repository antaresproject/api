#!/bin/bash

if [ -z "$1" ] ; then
    echo "No project build name supplied."
    exit 1
fi

BUILDAPP=$(pwd)/../$1
PHPUNITXML=$BUILDAPP/phpunit.xml
TESTSDIR=$(pwd)/tests

echo 'Running component tests...'
echo 'Test directory: ' $TESTSDIR
echo 'PHP UNIT Configuration: ' $PHPUNITXML

cp -r $(pwd) $BUILDAPP/src/components
phpunit --configuration $PHPUNITXML $TESTSDIR

#!/bin/bash

BRANCH=$1
declare -A subtreePush;

subtreePush['packages/php-rdk']='git@github.com:vtsykun/test-phprdk.git';
subtreePush['applications/symfony-se']='git@github.com:vtsykun/test-symfony-se-aplicatiion.git';
subtreePush['applications/symfony-ee']='git@github.com:vtsykun/test-symfony-ee-aplicatiion.git';
subtreePush['packages/distribution-bundle']='git@github.com:vtsykun/test-distribution-bundle.git';
subtreePush['packages/database-isolation']='git@github.com:vtsykun/database-isolation.git';

for prefix in "${!subtreePush[@]}"
do
    if [ -f "$PWD/travis.key" ]; then
        echo "Push into ${subtreePush[$prefix]} using key"
        ssh-agent bash -c "ssh-add $PWD/travis.key; git subtree push --prefix=$prefix ${subtreePush[$prefix]} $BRANCH"
    else
        echo "Push into ${subtreePush[$prefix]} without key"
        git subtree push --prefix=${prefix} ${subtreePush[$prefix]} ${BRANCH}
    fi
done

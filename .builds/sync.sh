#!/bin/bash

declare -A subtreePush;

subtreePush['packages/php-rdk']='git@github.com:vtsykun/test-phprdk.git';
subtreePush['applications/symfony-se']='git@github.com:vtsykun/test-symfony-se-aplicatiion.git';
subtreePush['applications/symfony-ee']='git@github.com:vtsykun/test-symfony-ee-aplicatiion.git';
subtreePush['packages/distribution-bundle']='git@github.com:vtsykun/test-distribution-bundle.git';

for prefix in "${!subtreePush[@]}"
do
    git subtree push --prefix=${prefix} ${subtreePush[$prefix]} master
done

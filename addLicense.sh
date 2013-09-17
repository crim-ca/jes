#!/bin/sh

# PHP FILE

FILES=`find . ! -path "./libraries/*" -name '*.php'`

for i in $FILES # or whatever other pattern...
do
    if ! grep -q Copyright $i
    then
    cat copyright.txt $i >$i.new && mv $i.new $i
    fi
done


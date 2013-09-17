#!/bin/sh


if [ "$#" -ne 1 ]; then
    echo "Usage : $0 <joomla-site-path>"
    exit 1
fi

JOOMLA_BASE=$1
#JES_PATH=$(cd "$(dirname "$0")/.."; pwd)
JES_PATH=$PWD

# Check if JOOMLA_BASE IS GOOD
if [ ! -d "$JOOMLA_BASE/libraries/joomla" ]; then
    echo "ERROR: $JOOMLA_BASE seems not being a joomla website"
    echo "Please check path"
    exit 1
fi

#Check if script is executed since JES root path
if [ ! -f "$JES_PATH/remove_symbolic.sh" ]; then
    echo "ERROR: remove_symbolic.sh not found"
    echo "Please execute this script in the same repertory of this file"
    exit 1
fi


# restore com_elasticsearch
REP=$JOOMLA_BASE/components/com_elasticsearch
if [ -h "$REP" ]; then # check if symbolic link exists
    rm  $REP
    mv $REP-save $REP
    echo "Restore : $REP"
fi

REP=$JOOMLA_BASE/administrator/components/com_elasticsearch
if [ -h "$REP" ]; then # check if symbolic link exists
    rm  $REP
    mv $REP-save $REP
    echo "Restore : $REP"
fi

#plugins



REP=$JOOMLA_BASE/plugins/elasticsearch
if [ -d "$REP-save" ] && [ -d "$REP" ]; then
    rm -r $REP
    mv $REP-save $REP
    echo "Restore : elasticsearch plugins repertory"
fi
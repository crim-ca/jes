#!/bin/sh

if [ "$#" -ne 1 ]; then
    echo "Usage : $0 <joomla-site-path>"
    exit 1
fi

JOOMLA_BASE=$1
#JES_PATH=$(cd "$(dirname "$0")/.."; pwd)
JES_PATH=$PWD

# Check if JOOMLA_BASE IS GOOD
if [ ! -f "$JOOMLA_BASE/joomla.xml" ]; then
    echo "ERROR: joomla.xml not found"
    echo "Please check path"
    exit 1
fi

# Check if elasticsearch extension is installed
if [ ! -d "$JOOMLA_BASE/components/com_elasticsearch" ]; then
    echo "ERROR : com_elasticsearch not found"
    echo "Please install elasticsearch package"
    exit 1
fi

#Check if script is executed since JES root path
if [ ! -f "$JES_PATH/create_symbolic.sh" ]; then
    echo "ERROR: ln_script.sh not found"
    echo "Please execute this script in the same repertory of this file"
    exit 1
fi



### com_elasticsearch
REP=$JOOMLA_BASE/components/com_elasticsearch
if [ ! -h "$REP" ]; then # check if symbolic link exists
    mv $REP $REP-save
    ln -s $JES_PATH/components/com_elasticsearch $REP
    echo "Create symbolic link : $REP"
fi

REP=$JOOMLA_BASE/administrator/components/com_elasticsearch
if [ ! -h "$REP" ]; then # check if symbolic link exists
    mv $REP $REP-save
    ln -s $JES_PATH/components/com_elasticsearch/admin $REP
    echo "Create symbolic link : $REP"
fi


### plugin
REP=$JOOMLA_BASE/plugins/elasticsearch
if [ -d "$REP" ]; then # check if elasticsearch repertory exists

    if [ ! -d "$REP-save" ]; then # if backup does not exists
	mv $REP $REP-save
	cp -r $REP-save $REP
    fi

    FILES="article contact weblinks"
    for PLUG in $FILES # foearch plugin
    do
	if [ ! -h "$REP/$PLUG" ]; then # check if symbolic link exists
	    mv $REP/$PLUG $REP/$PLUG-save
	    ln -s $JES_PATH/plugins/elasticsearch/$PLUG $REP/$PLUG
	    echo "Create symbolic link : $REP/$TYPE"
	fi 
    done
fi

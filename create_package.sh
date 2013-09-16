#!/bin/sh

ROOT_PATH=$PWD

#Tmp repertory to save zip archive
TMP_PATH=$PWD/tmp

# If directory tmp does not exists
if [ ! -d "$TMP_PATH" ]; then
    mkdir $TMP_PATH
    mkdir $TMP_PATH/packages
else
    rm $TMP_PATH -r
    mkdir $TMP_PATH
    mkdir $TMP_PATH/packages
fi


# Zip Component elasticsearch
cd $ROOT_PATH/components
zip -r $TMP_PATH/packages/com_elasticsearch.zip  ./com_elasticsearch

#content_elastic plugin
cd $ROOT_PATH/plugins/content
zip -r $TMP_PATH/packages/plg_content_elastic.zip ./elastic

#system_elasticLib plugin
cd $ROOT_PATH/plugins/system
zip -r $TMP_PATH/packages/plg_system_elasticaLib.zip ./elasticaLib
echo $PWD

# Elastica Librairy
cd $ROOT_PATH/libraries
zip -r $TMP_PATH/packages/lib_Elastica.zip ./Elastica


################### CONTENT TYPE ################

cd $ROOT_PATH/plugins/elasticsearch

TYPES="article contact weblinks"

for TYPE_NAME in $TYPES
do
	zip -r $TMP_PATH/packages/plg_elasticsearch_$TYPE_NAME.zip ./$TYPE_NAME
done

# Create zip package
if [ -d "$ROOT_PATH/release" ]; then
# Remove old package if exists
    if [ -f "$ROOT_PATH/release/pkg_elasticsearch.zip" ]; then
	rm $ROOT_PATH/release/pkg_elasticsearch.zip
    fi
else
    mkdir $ROOT_PATH/release
fi

cd $TMP_PATH
cp $ROOT_PATH/pkg_elasticsearch.xml $TMP_PATH
zip -r $ROOT_PATH/release/pkg_elasticsearch.zip .

echo 
echo "#########################################################"
echo "## ZIP Package has been created in release repertory ! ##"
echo "#########################################################"
echo 


#rm -r $TMP_PATH
JES - Extension ElasticSearch for Joomla!
===
16/09/2013  
Jean-Baptite Cayrou  
Adrien Gareau  

An ElasticSearch extension for Joomla


##Build package


It is easier to have separated repertories to develop but a little long to create package install...  
We have created a shell script named "create_package.sh" which compress and prepare a zip install package for Joomla!  
Just execute `./create_package.sh` and the script will create a release repertory containing install package !



##Installation

In Joomla administration, upload package to install it.
You also need to activate these plugins:

Content - ElasticSearch  
System - ElasticaLib   
ElasticSearch - Article   
ElasticSearch - Contact   
ElasticSearch - Weblinks   

## Setup envirnoement to develop

###Automatic

** ElasticSearch package musts be installed **

`./create_symbolic.sh` creates all symbolic links you need. Execute it with the path of your Joomla website :  
`./create-symbolic.sh /var/www/joomla`

The script will create symbolic links for com_elasticsearch component (site and admin) and for different
plugins installed.


###Manual:

Just install from admin panel :
plg_content_elastic.zip
plg_system_elasticaLib.zip
lib_elastica.zip


We call $SVN path where is saved this repository and $SITE, root path of your Joomla! install

* Component

`ln -s $SVN/components/com_elasticsearch $SITE/components/`
`ln -s $SVN/components/com_elasticsearch/admin $SITE/administrator/components/com_elasticsearch`

* Plugins

`ln -s $SVN/plugins/elasticsearch $SITE/plugins/`


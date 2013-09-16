##############################################
# Extension ElasticSearch for Joomla! 2.5    
# 23/08/2013		      	      	     
# Jean-Baptite Cayrou			     
# Adrien Gareau				     
##############################################

# Build package
-------------------

It is easier to have separated repertories to develop but a little long to create package install...
We have created a bash script named "create_package.sh" which compress and prepare a zip install package for Joomla!
Just execute ./create_package.sh and the script will create a release repertory containing install package !

# Setup environment to develop

Automatic:

"create_symbolic.sh" creates all symbolic links you need. Execute it with the path of your Joomla website : ./create-symbolic.sh /var/www/joomla


Manual:

To develop this extension we advise to create symbolic links to component and plugins.
Some plugins are not likely to change, so creating symbolic links for them is useless.

Just install from admin panel :
plg_content_elastic.zip
plg_system_elasticaLib.zip
lib_elastica.zip


We call $SVN path where is saved this repository and $SITE, root path of your Joomla! install

# Component
ln -s $SVN/components/com_elasticsearch $SITE/components/
ln -s $SVN/components/com_elasticsearch/admin $SITE/administrator/components/com_elasticsearch

# Plugins
ln -s $SVN/plugins/elasticsearch $SITE/plugins/

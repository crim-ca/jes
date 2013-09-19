JES - Extension ElasticSearch for Joomla!
===

An ElasticSearch extension for Joomla  
**You absolutely need to have an ElasticSearch server if you want to use this extension.**  
If you need further information about ElasticSearch you can go on their website:  
http://www.elasticsearch.org/

This extension for Joomla! is a powerful search engine which uses ElasticSearch as indexer.

Here some features it provides:

* Results are ordering by pertinence.
* Boost on some fields. If a search word is in title it is more pertinent than if it is just in the content.
* Smart search. Our engine does not search the exact words which have been typed. The search is made by prefix so if you write part of a word it could return the whole word.
* Internationalization. Search results are in the same language that current user.
* Stopword. Common words (the, it, is etc.) are eliminated from the search query.
* Search words are highlighted in the results. The way highlighting is made can be modified. Indeed you can choose to bold results are use a special html tags.
* Each type of content has its own display. Different elements can be display depends on type of the result.
* PDF file can be easily indexed. You can attach a file to content and make search in that content.


##Build package


It is easier to have separated repertories to develop but it is a little bit too long to create package install...  
We have created a shell script named "create_package.sh" which compress and prepare a zip install package for Joomla!  
Just execute `./create_package.sh` and the script will create a release repertory containing the package !



##Installation

In Joomla administration, upload the package to install it.
You also need to activate these plugins:

Content - ElasticSearch  
System - ElasticaLib   
ElasticSearch - Article   
ElasticSearch - Contact   
ElasticSearch - Weblinks   

## Setting up the working environment

To work on this extension we advise you to create symbolic links for component and plugins.
Some plugins are not likely to change, so creating symbolic links for them is useless.

###Automatic

** ElasticSearch package musts be installed **

`./create_symbolic.sh` creates all symbolic links you need. Execute it with the path of your Joomla website :  
`./create-symbolic.sh /var/www/joomla`

The script will create symbolic links for com_elasticsearch component (site and admin) and for different
plugins installed.


###Manual

We call $REP path where is saved this repository and $SITE, root path of your Joomla! installation

* Component

`ln -s $REP/components/com_elasticsearch $SITE/components/`
`ln -s $REP/components/com_elasticsearch/admin $SITE/administrator/components/com_elasticsearch`

* Plugins

`ln -s $REP/plugins/elasticsearch $SITE/plugins/`

###Documentation

Guides for administrators and developers are available here : https://crim-ca.github.com/jes


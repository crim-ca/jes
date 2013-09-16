<?php
/*
   Copyright 2013 CRIM - Computer Research Institute of Montreal

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
?>
<?php
/**
 * Curl for php must be installed.
 * sudo apt-get install php5-curl for ubuntu
 **/
 
 
// no direct access
defined('_JEXEC') or die;
	
require_once JPATH_SITE.'/components/com_content/router.php';

/**
 * ElasticSearch Content Plugin
 *
 */		
class plgContentElastic extends JPlugin
{

	public function __construct(& $subject, $config)
	{		
			parent::__construct($subject, $config);
			$this->loadLanguage();
	}
	
	/**
	 * ElasticSearch after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		The context of the content passed to the plugin (added in 1.6)
	 * @param	object		A JTableContent object
	 * @param	bool		If the content has just been created
	 * @since	2.5
	 */
	public function onContentAfterSave($context, $article, $isNew)
	{		
		$dispatcher	= JDispatcher::getInstance();
		$res = JPluginHelper::importPlugin('elasticsearch');

		// Trigger the onElasticSearchAfterSave event.
		$results = $dispatcher->trigger('onElasticSearchAfterSave', array($context, $article, $isNew));

	}
	/**
	 * ElasticSearch before save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		The context of the content passed to the plugin (added in 1.6)
	 * @param	object		A JTableContent object
	 * @param	bool		If the content is just about to be created
	 * @since   2.5
	 */
	public function onContentBeforeSave($context, $article, $isNew)
	{
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('elasticsearch');

		// Trigger the onElasticSearchBeforeSave event.
		$results = $dispatcher->trigger('onElasticSearchBeforeSave', array($context, $article, $isNew));

	}
	/**
	 * ElasticSearch after delete content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		The context of the content passed to the plugin (added in 1.6)
	 * @param	object		A JTableContent object
	 * @since   2.5
	 */
	public function onContentAfterDelete($context, $article)
	{
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('elasticsearch');

		// Trigger the onElasticSearchAfterDelete event.
		$results = $dispatcher->trigger('onElasticSearchAfterDelete', array($context, $article));

	}
	/**
	 * ElasticSearch change state content method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param   string   $context  The context for the content passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the content that has changed state.
	 * @param   integer  $value    The value of the state that the content has been changed to.
	 * @since   2.5
	 */
	public function onContentChangeState($context, $pks, $value)
	{
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('elasticsearch');

		// Trigger the onElasticSearchChangeState event.
		$results = $dispatcher->trigger('onElasticSearchChangeState', array($context, $pks, $value));
	}

	/**
	 * ElasticSearch change category state content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param   string   $extension  The extension whose category has been updated.
	 * @param   array    $pks        A list of primary key ids of the content that has changed state.
	 * @param   integer  $value      The value of the state that the content has been changed to.
	 * @since   2.5
	 */
	public function onCategoryChangeState($extension, $pks, $value)
	{
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('elasticsearch');

		// Trigger the onElasticSearchCategoryChangeState event.
		$dispatcher->trigger('onElasticSearchCategoryChangeState', array($extension, $pks, $value));

	}
			   
}


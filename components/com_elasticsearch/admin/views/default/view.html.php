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
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
// Load the ElasticSearch Conf.
require_once JPATH_ADMINISTRATOR . '/components/com_elasticsearch/helpers/config.php';

// Load the ElasticSearch Helper.
require_once JPATH_ADMINISTRATOR . '/components/com_elasticsearch/helpers/render.php';

/**
 * ElasticSearch View
 * @author Jean-Baptiste Cayrou
 * @author Adrien Gareau
 */
class ElasticSearchViewDefault extends JViewLegacy
{
	
        /**
         * ElasticSearch Default view display method
         * @return void
         */
        function display($tpl = null) 
        {		
        	// Load plug-in language files.
        	JFactory::getLanguage()->load('com_finder');

          if($this->get('isConnected')){
              $this->items = $this->get('Items');
              $this->status = $this->get('Status'); 
          }   
          $this->pluginStatus = $this->get('pluginState');



        	$this->indexName=ElasticSearchConfig::getIndexName();
				
				// Set the toolbar
                $this->addToolBar();
                
                // Display the template
                parent::display($tpl);
        }
        
		/**
         * Setting the toolbar
         */
        protected function addToolBar() 
        {
                JToolBarHelper::title(JText::_('COM_ELASTICSEARCH_MANAGER_INDEX'));
			  
        	$toolbar = JToolBar::getInstance('toolbar');
        	JToolBarHelper::preferences('com_elasticsearch');
        	$toolbar->appendButton('Popup', 'archive', 'COM_FINDER_INDEX', 'index.php?option=com_elasticsearch&view=indexer&tmpl=component', 500, 210);
        	$toolbar->appendButton('Popup', 'delete', 'COM_FINDER_INDEX_TOOLBAR_PURGE', 'index.php?option=com_elasticsearch&view=delete&tmpl=component', 500, 210);

        }
}

<?php
/**
 * @package elasticsearch
 * @subpackage com_elasticsearch
 * @author Jean-Baptiste Cayrou and Adrien Gareau
 * @copyright Copyright 2013 CRIM - Computer Research Institute of Montreal
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
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


class ElasticSearchViewDefault extends JView
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

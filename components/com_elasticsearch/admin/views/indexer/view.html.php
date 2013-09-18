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
 
/**
 * ElasticSearch View
 */
class ElasticSearchViewIndexer extends JView
{

        function display($tpl = null) 
        {
				
			$dispatcher	= JDispatcher::getInstance();
			$res = JPluginHelper::importPlugin('elasticsearch');

			$types = array();
			
			// Trigger the index event.
			$this->results = $dispatcher->trigger('onElasticSearchIndexAll', array($types));
			
		   // Display the template
			parent::display($tpl);
			
        }
        
}

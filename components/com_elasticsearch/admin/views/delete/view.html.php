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

/**
 * ElasticSearch View
 */
class ElasticSearchViewDelete extends JView
{

        function display($tpl = null) 
        {
			$elasticaClient = ElasticSearchConfig::getElasticSearchClient();
			$index = $elasticaClient->getIndex(ElasticSearchConfig::getIndexName());
			
			$index->delete();
			
		   // Display the template
			parent::display($tpl);
			
        }
        
}

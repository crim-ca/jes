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
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * ElasticSearch Component Controller
 *
 * @author Jean-Baptiste Cayrou
 * @author Adrien Gareau
 */
class ElasticSearchController extends JController
{
	
	function search()
	{
		$keyword= JRequest::getString('searchword', null, 'post'); 
		
		
	}
	
}

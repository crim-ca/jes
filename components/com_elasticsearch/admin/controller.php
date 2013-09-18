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
 * General Controller of ElasticSearch component
 */
class ElasticSearchController extends JController
{
        /**
         * display
         *
         * @return void
         */
        function display($cachable = false, $urlparams = false) 
        {
                // set default view if not set
                $input = JFactory::getApplication()->input;
                $input->set('view', $input->getCmd('view', 'default'));
                
                // call parent behavior
                parent::display($cachable);
        }
        
}

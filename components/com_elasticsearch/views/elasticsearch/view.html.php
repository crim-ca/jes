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
jimport('joomla.html.pagination');


require_once JPATH_ADMINISTRATOR.'/components/com_elasticsearch/helpers/render.php';



/**
 * HTML View class for the ElasticSearch Component
 *
 */
class ElasticSearchViewElasticSearch extends JView
{
		
        // Overwriting JView display method
        function display($tpl = null) 
        {
				JFactory::getLanguage()->load('com_search');
				
				
				$app	= JFactory::getApplication();
				$params = $app->getParams();
				$menus	= $app->getMenu();
				$menu	= $menus->getActive();
				$model = $this->getModel();
				$this->elements=array();
				// because the application sets a default page title, we need to get it
				// right from the menu item itself
				if (is_object($menu)) {
					$menu_params = new JRegistry;
					$menu_params->loadString($menu->params);
					if (!$menu_params->get('page_title')) {
						$params->set('page_title',	JText::_('COM_SEARCH_SEARCH'));
					}
				}
				else {
					$params->set('page_title',	JText::_('COM_SEARCH_SEARCH'));
				}

				$title = $params->get('page_title');
				if ($app->getCfg('sitename_pagetitles', 0) == 1) {
					$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
				}
				elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
					$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
				}
				$this->document->setTitle($title);

				if ($params->get('menu-meta_description'))
				{
					$this->document->setDescription($params->get('menu-meta_description'));
				}

				if ($params->get('menu-meta_keywords'))
				{
					$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
				}

				if ($params->get('robots'))
				{
					$this->document->setMetadata('robots', $params->get('robots'));
				}
				
				$this->assignRef('params', $params);

				


				// Get the offset and the limit for pagination
				$offset=JRequest::getInt('start',0,'get');
				$limit =JRequest::getInt('limit',10,'get');
				$this->searchword=JRequest::getString('searchword', null, 'get');
				
				// Get search areas to limit search on selected types
				$this->areas = $model->getSearchAreas();
				
				$this->totalResults=0;

				// Search only if searchword or start GET parameter exists
				if(isset($_GET['searchword'])||isset($_GET['start'])||isset($_GET['limitstart']))
				{
					//Call the model to make the search and get results and the number of it 
					$model->search();
					$this->totalResults=$model->getTotalHits();
					$this->results= $model->getResults();
				}

				//Pagination : utiliser splice pour couper l'array en plusieurs parties 
				$this->pagination = new JPagination($this->totalResults,$offset,$limit);
				
				
                // Display the view
                parent::display($tpl);          
        }
     
}

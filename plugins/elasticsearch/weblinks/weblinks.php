<?php
/**
 * @package elasticsearch
 * @subpackage plg_elasticsearch_weblinks
 * @author Jean-Baptiste Cayrou and Adrien Gareau
 * @copyright Copyright 2013 CRIM - Computer Research Institute of Montreal
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<?php

// no direct access
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

require_once JPATH_SITE.'/components/com_content/router.php';
require_once JPATH_SITE.'/components/com_weblinks/helpers/route.php';

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_elasticsearch/helpers/adapter.php';

/**
 * ElasticSearch adapter for com_content.
 *
 */
class plgElasticsearchWeblinks extends ElasticSearchIndexerAdapter
{

	/**
	 * The type ElasticSearch of content which will be indexed
	 *
	 * @var    string
	 */
	protected $type = 'weblinks';


	/**
	 * The type ElasticSearchto display
	 *
	 * @var    string
	 */
	protected $type_display = 'Web Links';

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   2.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Set boost
		$this->boost=$this->params->get('boost');
		
		// Doc here : http://www.elasticsearch.org/guide/reference/mapping/core-types/
		$mapping= 	array(
		    'id'      				 => array('type' => 'integer','include_in_all' => FALSE,'index' => 'not_analyzed'),
			'title'    			 	 => array('type' => 'string','include_in_all' => TRUE,'boost'=> 1.5),
			'url'    			 	=> array('type' => 'string', 'include_in_all' => TRUE),
			'description'    		 => array('type' => 'string', 'include_in_all' => TRUE),
			'categories' 			 => array('type' => 'string', 'include_in_all' => TRUE),
			'language' 				 => array('type' => 'string',  'include_in_all' => FALSE, 'index' => 'not_analyzed'),
			'href'   				 => array('type' => 'string', 'include_in_all' => FALSE),
			'boost' 				 => array('type' => 'float', 'include_in_all' => FALSE),
			);
		
		$this->setMapping($mapping);	
	}

	/**
	 * Method to remove an article in ElasticSearch when it is deleted
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $data    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 */
	public function onElasticSearchAfterDelete($context, $data)
	{
		// Skip plugin if we are deleting something other than article
		if ($context != 'com_weblinks.weblink') {
				return false;
		}
		$this->delete($data->id);
		
		return true;
	}

	/**
	 * Method to determine if the access level of an item changed.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row      A JTable object
	 * @param   boolean  $isNew    If the content has just been created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onElasticSearchAfterSave($context, $row, $isNew)
	{

		// Skip plugin if we are saving something other than article
		if ($context != 'com_weblinks.weblink') {
				return true;
		}

		//Delete the document in elasticsearch (if language changed)
		$this->delete($id = $row->id);
		
		if($row->state == 1){ // If this article is published
			$document = $this->rowToDocument($row);
			$this->addDocument($document);
		}
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
	public function onElasticSearchChangeState($context, $pks, $value)
	{
		
		// Skip plugin if we are saving something other than article
		if ($context != 'com_weblinks.weblink') {
				return true;
		}
		
		// Get all articles modifies
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__weblinks w');
		$query->where('w.id IN ('.implode(',',$pks).')');
		$db->setQuery((string)$query);
		$weblinks = $db->loadObjectList();
		
		foreach($weblinks as $weblink){
				$this->delete($weblink->id);
        		if($weblink->state == 1){ // If this weblink is published
	        		$document = $this->rowToDocument($weblink);
        			$this->pushDocument($document);	
	        	}
		}

		$this->flushDocuments();
	}


	/**
	 * Convert an row to an elastica document
	 */
	private function rowToDocument($row){
		$id = $row->id;


		
		//Get the names of the categories
		$category = JCategories::getInstance('Weblinks')->get($row->catid);
		$categories = array();
		while($category&&$category->id > 1){
			$categories[] = $category->title;
			$category = $category->getParent();
		}
		
		// Create a document
		$entity = array(
			'title'    		    => html_entity_decode(strip_tags($row->title),ENT_COMPAT | ENT_HTML401,'UTF-8'),
			'url'		    	=> html_entity_decode(strip_tags($row->url),ENT_COMPAT | ENT_HTML401,'UTF-8'),
			'description'		=> html_entity_decode(strip_tags($row->description),ENT_COMPAT | ENT_HTML401,'UTF-8'),
			'language'		    => $row->language,
			'categories' 	    => implode(';',$categories),
			'href'				=> $row->url,
		);

		$document = new \Elastica\Document($id,$entity);

		return $document;

	}

	/**
	 * Method called to index all contents
	 * 
	 * @param   Array    $type    A String array
	 *
	 */
	public function onElasticSearchIndexAll($types){
		
		//Get all articles
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__weblinks');
		$db->setQuery((string)$query);
		$weblinks = $db->loadObjectList();

        foreach($weblinks as $weblink){
        		if($weblink->state == 1){ // If this weblink is published
	        		$document = $this->rowToDocument($weblink);
        			$this->pushDocument($document);	
	        	}
		}

		$this->flushDocuments();

		return $this->type_display;
			
	}
	
	/**
	 * Return the type of content
	 * 
	 * @return Array
	 * */
	public function onElasticSearchType()
	{
		$infoType=array();
		$infoType['type'] = $this->type;
		$infoType['type_display'] = $this->type_display;
		
		return $infoType;
	}
	
	/**
	 * Index an article
	 * @param   JTable   $row 
	 *
	 * @return  boolean  True on success.
	 * */
	protected function index($row,$isNew=false){
		
		$id = $row->id;
		$this->delete($id);
		
		if($row->state == 1){ // If this weblink is published

			$document = $this->rowToDocument($row);
			$this->addDocument($document);
	
		}
		
		return true;
		
	}

}

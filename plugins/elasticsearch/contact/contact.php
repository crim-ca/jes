<?php
/**
 * @package elasticsearch
 * @subpackage plg_elasticsearch_contact
 * @author Jean-Baptiste Cayrou and Adrien Gareau
 * @copyright Copyright 2013 CRIM - Computer Research Institute of Montreal
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<?php

// no direct access
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

require_once JPATH_SITE.'/components/com_contact/router.php';
require_once JPATH_SITE.'/components/com_contact/helpers/route.php';

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_elasticsearch/helpers/adapter.php';

/**
 * ElasticSearch adapter for com_contacts.
 *
 */
class plgElasticsearchContact extends ElasticSearchIndexerAdapter
{
	/**
	 * The type ElasticSearch of content which will be indexed
	 *
	 * @var    string
	 */
	protected $type = 'contact';


	/**
	 * The type ElasticSearchto display
	 *
	 * @var    string
	 */
	protected $type_display = 'Contacts';


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
		    'id'      				 => array('type' => 'integer', 'include_in_all' => FALSE,'index' => 'not_analyzed'),
			'name'    			 	 => array('type' => 'string', 'include_in_all' => TRUE,'boost'=> 1.1),
			'alias'         		 => array('type' => 'string', 'include_in_all' => FALSE,'index' => 'no'),
			'language' 				 => array('type' => 'string', 
											  'include_in_all' => FALSE,
											  'index' => 'not_analyzed'),
			'misc'    			     => array('type' => 'string', 'include_in_all' => TRUE),
			'href'   				 => array('type' => 'string', 'include_in_all' => FALSE),
			'created_at'  			 => array('type' => 'date', 'include_in_all' => FALSE),
			'feature'			 	 => array('type' => 'integer', 'include_in_all' => FALSE),
			'boost' 				 => array('type' => 'float', 'include_in_all' => FALSE),
			);
		
		$this->setMapping($mapping);

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
		if ($context != 'com_contact.contact') {
				return true;
		}
		
		// Get all articles modifies
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__contact_details c');
		$query->where('c.id IN ('.implode(',',$pks).')');
		$db->setQuery((string)$query);
		$contacts = $db->loadObjectList();
		
		foreach($contacts as $contact){
				$this->delete($contact->id);
        		if($contact->published == 1){ // If this article is published
	        		$document = $this->rowToDocument($contact);
        			$this->pushDocument($document);	
	        	}
		}

		$this->flushDocuments();
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onElasticSearchAfterDelete($context, $data)
	{
		if ($context != 'com_contact.contact') {
				return true;
		}
		$this->delete($id = $data->id);
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
		
		// A MODIF 
		// Skip plugin if we are saving something other than article
		if ($context != 'com_contact.contact') {
				return true;
		}
		//Delete the document in elasticsearch (if language changed)
		$this->delete($row->id);
		if($row->published == 1){ // If published
			$document = $this->rowToDocument($row);
			$this->addDocument($document);
		}
	}


	/**
	 * Convert an row to an elastica document
	 */
	private function rowToDocument($row){
		$id = $row->id;

		//Create a date object
		$date = new DateTime($row->created);

		//Get the names of the categories
		$category = JCategories::getInstance('Contact')->get($row->catid);
		$categories = array();
		while($category&&$category->id > 1){
			$categories[] = $category->title;
			$category = $category->getParent();
		}
		
		// Create a document
		$entity = array(
			'id'      		    => $id,
			'name'    		    => html_entity_decode(strip_tags($row->name),ENT_COMPAT | ENT_HTML401,'UTF-8'),
			'alias'				=> $row->alias,
			'misc'				=> html_entity_decode(strip_tags($row->misc),ENT_COMPAT | ENT_HTML401,'UTF-8'),
			'categories' 	    => implode(';',$categories),
			'language'		    => $row->language,
			'created_at'		=> $date->format('Y-m-d\Th:i:s'),
			'href'				=> ContactHelperRoute::getContactRoute($row->id,implode(';',$categories),$row->language),
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
		$query->from('#__contact_details');
		$db->setQuery((string)$query);
		$contacts = $db->loadObjectList();

        foreach($contacts as $contact){
    			if($contact->published == 1){ // If published
	        		$document = $this->rowToDocument($contact);
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
		
}

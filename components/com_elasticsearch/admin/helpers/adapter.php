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

defined('_JEXEC') or die;


jimport('joomla.event.dispatcher');

// Load the ElasticSearch Conf.
require_once JPATH_ADMINISTRATOR . '/components/com_elasticsearch/helpers/config.php';

/**
 * Adapter class for ElasticSearch plugins. 
 * All plugins must extend this class.
 *
*/
abstract class ElasticSearchIndexerAdapter extends JPlugin
{

	/**
	 * The type of content the adapter indexes.
	 *
	 * @var    string
	 */
	protected $type;

	/**
	 * The type ElasticSearchto display
	 *
	 * @var    string
	 */
	protected $type_display;

	// Boost
	protected $boost = 1.0;


	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var    string
	 */
	protected $layout;
	
	
	// Client ElasticSearch
	protected $elasticaClient;
	
	// Type ElasticSearch
	protected $elasticaType;

	///***********************************/
	
	//The ElasticSearch index name
	private $index;
	
	private $elasticaIndex;
	
	// current type= $type_+lang
	private $current_type;
	
	// Mapping of the type
	private $mapping;

	private $sourceExcludes;

	private $documents = array();
	
	// Language mapping between Joomla and ElasticSearch	
	private $langAnalyzer = array ( 
								'ar' => 'arabic',
								'da' => 'danish',
								'de' => 'german',
								'el' => 'greek',
							    'en' => 'english',
								'es' => 'spanish',
								'fa' => 'persian',
								'fr' => 'french',
								'hu' => 'hungarian',
								'it' => 'italian',
								'nb' => 'Norwegian',
								'nl' => 'dutch',
								'pt' => 'portuguese',
								'ro' => 'Romanian',
								'ru' => 'russian',
								'sv' => 'swedish',
								'tr' => 'turkish',
								'zh' => 'chinese',
							   );
	///***********************************/
	
	public function __construct(&$subject, $config)
	{
		// Call the parent constructor.
		parent::__construct($subject, $config);
		
		// Set configuration of ES
		$this->index=ElasticSearchConfig::getIndexName();
		
		//Check if type is set
		if($this->type==null){
			throw new JException(JText::sprintf('Erreur in an ElasticSearch Plugin, $this->type is null. It must be set in the plugin.'));	
		}

		// Set ElasticSearch Client and Index
		$this->elasticaClient = ElasticSearchConfig::getElasticSearchClient();
		
		// Create Index if not exits
		$this->createIndex();
		
		//By default current_type = type
		$this->setLanguage(null);
		
		// Check for a layout override.
		if ($this->params->get('layout'))
		{
			$this->layout = $this->params->get('layout');
		}
	}



	private function createAnalyzerArray(){
			
			$analyzers = array();

			foreach($this->langAnalyzer as $key => $ESlang){

				$analyzers[$key."_Analyzer"] = array(
													'tokenizer'	=> 'standard',
													'filter'  	=> array("standard","lowercase","delimiter","edge",$key."_stop","asciifolding"),

													);
			}

			$analyzers["default"] = array(
													'tokenizer'	=> 'standard',
													'filter'  	=> array("standard","lowercase","delimiter","asciifolding"),
													);	
			return $analyzers;
	}
	
	private function createFilterArray(){
			
			$filters = array();


			$filters["delimiter"] = array(
									'type' 		=> 'word_delimiter',
									'catenate_all'  => 'true',
									'preserve_original' =>'true',
									'split_on_numerics' =>'false',
									);

			$filters["edge"] = array(
									'type' 		=> 'edgeNGram',
									'min_gram'  => '3',
									'max_gram'  => '18',
									'side'      => 'front'
									);
			
			foreach($this->langAnalyzer as $key => $ESlang){
				$filters[$key."_filter"] = array(
													'type' 		=> 'snowball',
													'language'  => $ESlang,
													);
			
				$filters[$key."_stop"] = array(
													'type' 		=> 'stop',
													'stopwords'  => '_'.$ESlang.'_',
													);
			}
			
			return $filters;
	}
	
	/**
	 * Create the ElasticSearch index if not exists
	 * */
	private function createIndex(){
		
		$this->elasticaIndex = $this->elasticaClient->getIndex($this->index);
		
		// Get all Indexes
		$status = $this->elasticaClient->getStatus();
		
		
		if(!$status->indexExists($this->index)){


			$indexMapping= array(
								'number_of_shards' => 4,
								'number_of_replicas' => 1,
								'analysis' => array(
									'analyzer' 	=> $this->createAnalyzerArray(),
									'filter'	=> $this->createFilterArray(),
												)
									
								);

			$this->elasticaIndex->create($indexMapping);		

		}

	}
	
	private function changeType(){
		$this->elasticaType = $this->elasticaIndex->getType($this->current_type);
	}
	
	/**
	 * Search with the $lang parameter, the name of this language in Joomla
	 *
	 * */
	private function getJoomlaLanguage($lang)
	{
		$Jlang =& JFactory::getLanguage();
		
		foreach ($Jlang->getKnownLanguages() as $key=>$knownLang)
		{
			//Check if $lang is a key else look into local list
			if($key==$lang||in_array($lang,explode(", ",$knownLang["locale"]))){
				$lang = explode('-',$key); // Slip to get the first part. en-GB becomes en
				return $lang[0];
			}
		}
		
		return "*";
	}
	
	/**
	 * Set the language
	 * Call this fonction before all indexing
	 * 
	 * @param   String   $data
	 * */
	 protected function setLanguage($lang)
	 {
		 //Try to Transform $lang in Joomla Standard, return "*" if it failed
		 $this->lang=$this->getJoomlaLanguage($lang);
		 
		 if($this->lang!="*"){

			 $this->current_type=$this->type.'_'.$this->lang;
		}
		else{
			$this->current_type=$this->type;
		}
		 
		 //Change current type
		 $this->changeType();
	 }
	 
	 /**
	  * Set a mapping for a type
	  */
	protected function setMapping($mapping){

		$this->mapping=$mapping;	

	}

	protected function setSourceExclude($exclude){
		$this->sourceExcludes=$exclude;
	}
	

	protected function typeExist($type){
		$mapping = $this->elasticaIndex->getMapping();
		
		foreach($mapping[ElasticSearchConfig::getIndexName()] as $key => $map){
			if($key==$type){
				return true;
			}
		}
						
		return false;
	}
	/*
	 * Method to define mapping
	 * 
	 * @param   JTable   $data
	 * */
	 protected function mapping(){
		 

		 // Set mapping only if mapping is set and if type does not exist
		 if($this->mapping&&!$this->typeExist($this->current_type))
		 {
		 	$ESmapping = new \Elastica\Type\Mapping();
		 	$ESmapping->setType($this->elasticaType);

			//If Language supported by ES
			if (array_key_exists($this->lang,$this->langAnalyzer)){
		
				$ESmapping->setParam("index_analyzer",$this->lang."_Analyzer");
				$ESmapping->setParam("search_analyzer",$this->lang."_Analyzer");
			}
			else{
				$ESmapping->setParam("index_analyzer","default");
				$ESmapping->setParam("search_analyzer","default");
			}

			if($this->boost==""){
				$this->boost=1.0;
			}
			$ESmapping->setParam('_boost', array('name' => 'boost', 'null_value' => $this->boost));

			if($this->sourceExcludes){
				$ESmapping->setSource(array('excludes' => $this->sourceExcludes));
			}

			// Set mapping
			$ESmapping->setProperties($this->mapping);

			// Send mapping to type
			$ESmapping->send();
		}
	 }

	
	/**
	 * Add a document
	 * 
	 * @param   \Elastica\Document   $document
	 * */
	protected function addDocument($document)
	{
		// Auto dectect the language of the document zith the field language
		$lang=($document->__isset('language')) ? $document->language : '*' ;

		//Add boost field if does not exist
		if (!$document->__isset('boost')){
			$document->set('boost',$this->boost);
		}

		// Set the lang
		$this->setLanguage($lang);

		// Create mapping if not exists
		$this->mapping();
		
		// Add article to type
		$this->elasticaType->addDocument($document);

		// Refresh Index
		$this->elasticaType->getIndex()->refresh();
		
	}

	/**
	 * add a document to the list. To index all the list execute flushDocuments
	 * 
	 * @param   \Elastica\Document   $document
	 * */
	protected function pushDocument($document)
	{

		// Get language if exists
		$lang=($document->__isset('language')) ? $document->language : '*' ;

		//Add boost field if does not exist
		if (!$document->__isset('boost')&&$this->boost){
			$document->set('boost',$this->boost);
		}

		// Extract the first letter of the langue (en for en-GB)
		$explode = explode('-',$lang);
		$lang = $explode[0];

		// If it is the first of this language
		if(!array_key_exists($lang, $this->documents)){
			$this->documents[$lang] = array(); // Init array
		}

		// add document to the list
		$this->documents[$lang][] = $document;
	
		$mem_limit_bytes = trim(ini_get('memory_limit'))*1024*1024;

		if(memory_get_usage()>$mem_limit_bytes*0.20){ // Check memory use
			//if documents array is too big we flush it
			$this->flushDocuments();
		}
	
	}

	/**
	  * Index all documents
	  */
	public function flushDocuments(){

		foreach($this->documents as $lang =>$list){
			$this->setLanguage($lang);
			$this->addDocuments($list);
			unset($this->documents[$lang]); // Delete array
		}

	}


	/**
	 * Add documents
	 * 
	 * @param   array<\Elastica\Document>  $documents
	 * */
	private function addDocuments(&$documents)
	{
		if(!$documents||sizeof($documents)==0){
			return false;
		}
		// Create mapping if not exists
		$this->mapping();
		
		// Add article to type
		$this->elasticaType->addDocuments($documents);

		// Refresh Index
		$this->elasticaType->getIndex()->refresh();
	}
	
	/**
	 * Return true if the document exists
	 */
	private function documentExists($id,$type)
	{

		$queryTerm = new \Elastica\Query\Terms();
		$queryTerm->setTerms('_id', array($id));
		$elasticaFilterType = new \Elastica\Filter\Type($type);
		$query = Elastica\Query::create($queryTerm);
		$query->setFilter($elasticaFilterType);

		// Perform the search
		$count = $this->elasticaIndex->count($query);

		return ($count>0) ? true : false;
	}

	/**
	 *
	 * Method to remove a document in ElasticSearch.
	 * The document will be removed in types of all language
	 *
	 * @param   int  $id    A JTable object containing the record to be deleted
	 */
	protected function delete($id){
		
		$Jlang =& JFactory::getLanguage();
	
		$EStype = $this->elasticaIndex->getType($this->type);
		

		if($this->documentExists($id,$EStype->getName())){
			$EStype->deleteById($id); // * language
		}
		$this->elasticaType->getIndex()->refresh();

		foreach ($Jlang->getKnownLanguages() as $key=>$knownLang)
		{	
				$explode = explode('-',$key);
				$key = $explode[0]; // Slip to get the first part. example : en-GB becomes en
				$EStype = $this->elasticaIndex->getType($this->type."_".$key);

				if($this->documentExists($id,$EStype->getName())){
					$EStype->deleteById($id);
				}	

				$this->elasticaType->getIndex()->refresh();
		}
	
		
	}

	private function smartHighLight($resultES)
	{
		$data = $resultES->getData();
		$highlights = $resultES->getHighlights();
		$smart = array();

		foreach($this->mapping as $field=>$map){ // get all field elasticsearch
			if(array_key_exists($field, $highlights)){

				$text = $highlights[$field][0]; // get the first element because sort by score

				$text = preg_replace('/{.+?}/', '', $text); // Remove include joomla
				
				if(substr_compare($data[$field],strip_tags($text),0,5)){ // It is not the beginning
					$smart[$field]='... '.$text;
				}
				else{
					$smart[$field]=$text;
				}
				
			}
		}

		return $smart;
	}

	/**
	 * Method called to display elements of ElasticSearch result
	 * The view must be in the file elasticsearch/plg_name/view/type_name/default.php
	 * 
	 * @param Array $data
	 * 
	 * @return string html display of the element
	 * */
	public function onElasticSearchDisplay($type,$data){

		// Check the type
		if($type!=$this->type){
			return false;
		}
		
		$highlight = $this->smartHighLight($data);

		$path = JPATH_SITE.'/plugins/elasticsearch/'.$type;
		
		$view = new JView(array('name'=>'plg_'.$type,
								'base_path'=>$path,
								));
		
		// Pass data to the view
		$view->assign('data', $data->getData());

		$view->assign('highlight',$highlight);
		// Pass type to the view
		$view->assign('type', $type);
		
		return $view->loadTemplate();
	}


	/*
	 * Method to get types which will be highlighted 
	 * 
	 * @param   string   $context  The context of the content
	 * 
	 * @return array $field array which contains all types who will be highlight
	 * */
	public function onElasticSearchHighlight()
	{
		$field=array();
		foreach($this->mapping as $key=>$map)
		{
			if($map['type']=='string')
				$field[]=$key;
		}

		return $field;	
		
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

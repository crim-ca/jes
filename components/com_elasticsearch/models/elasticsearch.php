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
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
 // Load the ElasticSearch Conf.
require_once JPATH_ADMINISTRATOR . '/components/com_elasticsearch/helpers/config.php';
require_once JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php';

/**
 * ElasticSearch Model
 * This model makes all search request to ElasticSearch.
 *
 * @author Jean-Baptiste Cayrou
 * @author Adrien Gareau
 */
class ElasticSearchModelElasticSearch extends JModelItem
{

	protected $elasticaClient;

	protected $searchFields;

	protected $results;

	protected $totalHits;

	protected $areas;
	
	protected $elements = array();


         public function __construct($config = array())
        {   

                parent::__construct($config);

        		// Create a elastica Client
				$this->elasticaClient = ElasticSearchConfig::getElasticSearchClient();

				// Set Index
				$this->elasticaIndex = $this->elasticaClient->getIndex(ElasticSearchConfig::getIndexName());

        }


        /**
         * method to get the diffents type enable in plugins
         * Return an array like array( 0 => Array('type'=>'foo','type_display'=>'bar') ....)
         */
        public function getSearchAreas(){

        		if(!$this->areas){
	      			$dispatcher = JDispatcher::getInstance(); 
					JPluginHelper::importPlugin('elasticsearch'); 
					$this->areas = $dispatcher->trigger('onElasticSearchType', array());
        		}

				return $this->areas;

        }


        public function getSearchFields(){

        		if(!$this->searchFields){
	      			$dispatcher = JDispatcher::getInstance(); 
					JPluginHelper::importPlugin('elasticsearch');
					$searchFields = $dispatcher->trigger('onElasticSearchHighlight', array());
					$this->searchField=array();
					foreach($searchFields as $fields){
						foreach($fields as $field){
							$this->searchFields[]=$field;
						}
					}
        		}

				return $this->searchFields;

        }     

        /**
         * Request the plugins enabled to know what is fields we need to active the highlighting
         */
        private function getHighlightFields(){

				$dispatche = JDispatcher::getInstance(); 
				JPluginHelper::importPlugin('elasticsearch'); 
				$fields= $dispatche->trigger('onElasticSearchHighlight', array());

				return $fields;
        }

        /**
         * Main method to make a search
         */
        public function search(){
				$this->getSearchFields();
				$word=JRequest::getString('searchword', null, 'get');
				$offset=JRequest::getInt('start',0,'get');
				$limit =JRequest::getInt('limit',10,'get');
				
				if($limit==0){ // no limit
						$limit=10000;
				}

				$this->getSearchAreas();


				$elasticaQueryString = new \Elastica\Query\QueryString();


				// Log search word only on the first page
				if($offset==0){
					SearchHelper::logSearch($word);
				}
				
				// Convert accents
				$word = htmlentities($word, ENT_NOQUOTES, 'utf-8');
				$word = preg_replace('#\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;#', '\1', $word);
				$word = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $word);
				$word = preg_replace('#\&[^;]+\;#','', $word);


				// Check if there are quotes ( for exact search )
				$exactSearch=false;
				if(strlen($word)>1&&$word[0]=='"'&&$word[strlen($word)-1]=='"')
				{
					$exactSearch=true;
					$word=substr($word, 1,strlen($word)-2); // Remove external "
				}

				$word = Elastica\Util::replaceBooleanWordsAndEscapeTerm($word); // Escape ElasticSearch specials char

				if($exactSearch){
					$word='"'.$word.'"';
				}

				if($word=="") // Empty search
				{
					$word= "*";	
				}

				$elasticaQueryString->setQuery($word);

                                                                
				//Create the actual search object with some data.
				$elasticaQuery = new Elastica\Query();
				$elasticaQuery->setQuery($elasticaQueryString);


				
				if(ElasticSearchConfig::getHighLigthEnable())
				{
					$fields= $this->getHighlightFields();

					$hlfields=array();
					foreach($fields as $field)
					{
						foreach($field as $highlight) 
						{

							$hlfields[]=array($highlight => array(	'fragment_size' => 1000,
																	'number_of_fragments' => 1,
																	));

						}
						
					}
					
					$highlightFields=array('pre_tags' => array(ElasticSearchConfig::getHighLigthPre()),
										   'post_tags' => array(ElasticSearchConfig::getHighLigthPost()),
										   "order" => "score",
										   'fields'    => $hlfields );

					$elasticaQuery->setHighlight($highlightFields);
				}

				
				// Set offset and limit for pagination
				$elasticaQuery->setFrom($offset);
				$elasticaQuery->setLimit($limit);

				//Create a filter for _type
				$elasticaFilterype=$this->createFilterType($this->areas);
				
				// Add filter to the search object.
				$elasticaQuery->setFilter($elasticaFilterype);


				//Search on the index.
				$elasticaResultSet = $this->elasticaIndex->search($elasticaQuery);	
				
				$this->results = $elasticaResultSet->getResults();

				$this->totalHits = $elasticaResultSet->getTotalHits();

        }


		public function getHighlightElem()
		{
			return $this->elements;
		}
		
        public function getResults(){
        	return $this->results;	
        }


        public function getTotalHits(){
        	return $this->totalHits;
        }

    	/**	
    	 * Method to create a filter by ES Type for all type.
    	 * For example if the search if just for article,
    	 * the filter we be on article, article_en-GB etc for each language
    	*/
        private function createFilterType($plg_types){
			//By default search in all types
			$all_types=true;
						
			$elasticaFilterType  = new \Elastica\Filter\Terms("_type");
			//  Foreach existing ES types
			foreach($plg_types as $area )
			{
				//Check if this type is enable for the search
				$check=JRequest::getString($area['type'], null, 'get');
				if($check !="") // If enabled
				{
					$all_types=false;

					// Generate all type with language extension type_en-GB etc.
					$langTypes = $this->getTypesWithLang($area['type']);
					foreach($langTypes as $type){
						$elasticaFilterType->addTerm($type);
					}
				}
				
			}
			// If all_type still true, the search is for all content type
			foreach($plg_types as $area ){
				if($all_types){
					foreach($this->areas as $area )
					{
						$langTypes =  $this->getTypesWithLang($area['type']);
						foreach($langTypes as $type){
							$elasticaFilterType->addTerm($type);
						}
					}
				}
			}
			
			return $elasticaFilterType;
		}
		
		/**
		 * Method to generate all (language) types of a type
		 *
		 */
        private function getTypesWithLang($type){
			
			$Jlang =& JFactory::getLanguage();
			
			$types = array();
			$types[]=$type; // Add the type for " * " language
			$explode=explode('-',$Jlang->getTag());
			$types[]=$type."_".$explode[0]; // And the current language
		
			
			return $types;
		}


}

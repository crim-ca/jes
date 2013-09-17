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

JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

 // Load the ElasticSearch Conf.
require_once JPATH_ADMINISTRATOR . '/components/com_elasticsearch/helpers/config.php';

/**
 * Helper for ElasticSearch Component
 *
 * @author Jean-Baptiste Cayrou
 * @author Adrien Gareau
*/
class ElasticSearchHelper
{
	/*
	 * Return HTML of a single result Elastic in argument
	 * 
	 * @var elasticaResult $item
	 * @return string html of the item
	 */
	static public function getHTML($item){	
			$html='';
			$type=$item->getType();
			
			//Remove the suffixe _*
			$pos= strrpos($type,'_');
			if($pos){
				$type = substr($type,0,$pos);
			}
			
			$dispatcher	= JDispatcher::getInstance();
			$res = JPluginHelper::importPlugin('elasticsearch');

			// Trigger the index event.
			$results = $dispatcher->trigger('onElasticSearchDisplay', array($type,$item));
			
			foreach ($results as $result)
			{
				if($result){
					$html=$result;
				}
			}
			
			return $html;	
	}

	/*
	 * Function to truncate the highlight result of elasticsearch
	 * It will search the first highlighted word and start to this sentence to the limit charaters param
	 * @var string $text
 	 * @var int limit totals of characters
	 * @return string html of the item
	 */
	static public function truncateHighLight($text,$limit)
	{

			preg_match ('/'.preg_quote(ElasticSearchConfig::getHighLigthPre()).'/', $text ,$matches,PREG_OFFSET_CAPTURE);

			if($matches){
				$posPreTag = $matches[0][1];
				//If pos if at the beginning it is not necessary to truncate the left part
				if($posPreTag>$limit*0.75){

						$pos = strpos($text," ",$posPreTag-$limit*0.3);
						
						$text = substr($text,$pos);
						$text='... '.$text;
				}
				
				return JHtmlString::truncate($text,$limit+4,true,true);

			}
		return $text;


	}
	
}

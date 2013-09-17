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


/**
 * ElasticSearchConfig
 * Contains static methods to get configuration. 
 *
 * @author Jean-Baptiste Cayrou
 * @author Adrien Gareau
*/
class ElasticSearchConfig {
	
	static public function getHostServer()
	{
		$params = &JComponentHelper::getParams('com_elasticsearch');
		$paramValue = $params->get('host');
		
		return $paramValue;
	}
	
	static public function getPortServer(){
		$params = &JComponentHelper::getParams('com_elasticsearch');
		$paramValue = $params->get('port');
		
		return $paramValue;
	}
	
	static public function getIndexName(){
		$params = &JComponentHelper::getParams('com_elasticsearch');
		$paramValue = $params->get('index','joomla');
		
		return $paramValue;
	}
	
	static public function getElasticSearchClient(){
		$elasticaClient = new \Elastica\Client(array(
			'host' => ElasticSearchConfig::getHostServer(),
			'port' => ElasticSearchConfig::getPortServer()
		));
	
		return $elasticaClient;
	}

	/* Tag before a word highlighted
	*/
	static public function getHighLigthPre(){
		$params = &JComponentHelper::getParams('com_elasticsearch');
		$paramValue = $params->get('highlightPre','<b>');
		
		return $paramValue;
	}
	
	/* Tag after a word highlighted
	*/
	static public function getHighLigthPost(){
		$params = &JComponentHelper::getParams('com_elasticsearch');
		$paramValue = $params->get('highlightPost','</b>');
		
		return $paramValue;
	}

	/* Tag after a word highlighted
	*/
	static public function getHighLigthEnable(){
		$params = &JComponentHelper::getParams('com_elasticsearch');
		$paramValue = $params->get('highlightEnable',true);
		
		return $paramValue;
	}
}

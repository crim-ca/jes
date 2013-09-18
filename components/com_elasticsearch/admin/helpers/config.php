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

/**
 * ElasticSearchConfig
 * Contains static methods to get configuration. 
 *
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

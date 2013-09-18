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

JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

 // Load the ElasticSearch Conf.
require_once JPATH_ADMINISTRATOR . '/components/com_elasticsearch/helpers/config.php';

/**
 * Helper for ElasticSearch Component
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

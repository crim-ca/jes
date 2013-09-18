<?php
/**
 * @package elasticsearch
 * @subpackage plg_system_elasticaLib
 * @author Jean-Baptiste Cayrou and Adrien Gareau
 * @copyright Copyright 2013 CRIM - Computer Research Institute of Montreal
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<?php

 
defined('_JEXEC') or die;

/**
 * Elastic plugin class.
 * This class register and load Elastic library
 */
class plgSystemElasticaLib extends JPlugin
{
	
	static function __autoload($class){
		
		$path = str_replace('\\', '/', substr($class, 0));

		if (file_exists( JPATH_LIBRARIES.'/'.$path . '.php')) {
			require_once(JPATH_LIBRARIES.'/'.$path . '.php');
		}

	}
	
    /**
     * Method to register custom library.
     *
     * return  void
     */
    public function onAfterInitialise()
    {
		spl_autoload_register('plgSystemElasticaLib::__autoload');
    }
}

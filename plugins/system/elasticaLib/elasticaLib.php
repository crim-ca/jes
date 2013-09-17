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

 
defined('_JEXEC') or die;

/**
 * Elastic plugin class.
 * This class register and load Elastic library
 * @author Jean-Baptiste Cayrou
 * @author Adrien Gareau
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

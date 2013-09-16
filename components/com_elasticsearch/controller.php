<?php
/*
   Copyright 2013 CRIM http://www.crim.ca

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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * ElasticSearch Component Controller
 *
 * @author Jean-Baptiste Cayrou
 * @author Adrien Gareau
 */
class ElasticSearchController extends JController
{
	
	function search()
	{
		$keyword= JRequest::getString('searchword', null, 'post'); 
		
		
	}
	
}

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
// no direct access
defined('_JEXEC') or die;
?>

<dl class="search-results">
<?php foreach($this->results as $result) : ?>
	<?php echo ElasticSearchHelper::getHTML($result); ?> 
	
<?php endforeach; ?>
</dl>
<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>



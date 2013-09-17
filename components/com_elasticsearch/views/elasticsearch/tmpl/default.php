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
?>
<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_elasticsearch');?>" method="get">

	<fieldset class="word">
		<label for="search-searchword">
			<?php echo JText::_('COM_SEARCH_SEARCH'); ?>
		</label>
		<input type="text" name="searchword" id="search-searchword" size="30" maxlength="1000" value="<?php echo $this->searchword; ?>" class="inputbox" />
		<button name="Search" onclick="this.form.submit()" class="button"><?php echo JText::_('COM_SEARCH_SEARCH');?></button>		
	</fieldset>

	<?php if ($this->areas) : ?>
		<fieldset class="only">
		<legend><?php echo JText::_('COM_SEARCH_SEARCH_ONLY');?></legend>
		<?php foreach ($this->areas as $area) :
			$val = $area['type_display'];
			echo $val;
			//$checked = is_array($this->areas['type']) && in_array($val, $this->areas['active']) ? 'checked="checked"' : '';
		?>
		<input type="checkbox" name="<?php echo $area['type'];?>" value="<?php echo $val;?>" id="area-<?php echo $val;?>" />
			<label for="area-<?php echo $val;?>">
				<?php //echo JText::_($txt); ?>
			</label>
		<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>

<?php if ($this->totalResults > 0) : ?>
	<div class="form-limit">
		<label for="limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	
	<p class="counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</p>
<?php endif; ?>


</form>

	<?php
	 if ($this->totalResults > 0) :
			echo $this->loadTemplate('results');
		  else :
	endif; ?>


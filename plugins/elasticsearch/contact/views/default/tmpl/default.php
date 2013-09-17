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
<dt class="result-name">
	<?php if ($this->data['href']) :?>
		<a href="<?php echo JRoute::_($this->data['href']); ?>">
			<?php echo $this->escape($this->data['name']);?>
		</a>
	<?php else:?>
		<?php echo $this->escape($this->data['name']);?>
	<?php endif; ?>
</dt>
<?php if ($this->categories) : ?>
	<dd class="result-category">
		<span class="small">
			(<?php echo $this->escape($this->categories[0]); ?>)
		</span>
	</dd>
<?php endif; ?>
<dd class="result-text">
	 <div style="
    padding-top: 5;
">
	<div style="
    float: left;
    max-width: 15%;
">
	<img style="float: left; max-height: 60%; width: 70%;" src="https://cdn1.iconfinder.com/data/icons/humano2/128x128/apps/preferences-contact-list.png" />
	</div>
	<p> <?php echo $this->data['misc']; ?> </p>
	</div>
</dd>
<?php if ($this->data['created_at']) : ?>
	<dd class="result-created">
		<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $this->data['created_at']); ?>
	</dd>
<?php endif; ?>



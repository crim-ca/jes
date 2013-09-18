<?php
/**
 * @package elasticsearch
 * @subpackage plg_elasticsearch_contact
 * @author Jean-Baptiste Cayrou and Adrien Gareau
 * @copyright Copyright 2013 CRIM - Computer Research Institute of Montreal
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<dt class="result-name">
	<?php if (isset($this->data['href'])) :?>
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
<?php if (isset($this->data['created_at'])) : ?>
	<dd class="result-created">
		<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $this->data['created_at']); ?>
	</dd>
<?php endif; ?>



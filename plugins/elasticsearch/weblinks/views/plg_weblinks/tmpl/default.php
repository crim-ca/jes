<?php
/**
 * @package elasticsearch
 * @subpackage plg_elasticsearch_weblink
 * @author Jean-Baptiste Cayrou and Adrien Gareau
 * @copyright Copyright 2013 CRIM - Computer Research Institute of Montreal
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<?php
JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
require_once JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php';
// Split categories
$categories = explode(';',$this->data['categories']);


?>
<div class="result">
<dt class="result-title">
	<?php if ($this->data['href']) :?>
		<a href="<?php echo JRoute::_($this->data['href']); ?>">
			<?php echo $this->escape($this->data['title']);?>
		</a>
	<?php else:?>
		<?php echo $this->escape($this->data['title']);?>
	<?php endif; ?>
</dt>
<?php if ($categories) : ?>
	<dd class="result-category">
		<span class="small">
			(<?php echo $this->escape($categories[0]); ?>)
		</span>
	</dd>
<?php endif; ?>
<dd class="result-text">
	<?php 

		if(isset($this->highlight['description'])){

				echo ElasticSearchHelper::truncateHighLight($this->highlight['description'],200);
		}
		else{
			if(isset($this->highlight['description'])){
				echo ElasticSearchHelper::truncateHighLight($this->highlight['description'],500);
			}
			else
			{
				$text=SearchHelper::prepareSearchContent($this->data['description'],"");
				echo JHtmlString::truncate($text,500,true,false);
			}
		}
	?>
</dd>

</div>

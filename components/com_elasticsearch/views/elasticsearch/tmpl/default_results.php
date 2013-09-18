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



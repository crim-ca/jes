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
	defined('_JEXEC') or die('Restricted Access');
?>

<h2>Indexes updated :</h2>
<ul>
	<?php
		foreach ($this->results as $result){
			echo '<li>';
			echo $result;
			echo '</li>';
		}
	?>


</ul>


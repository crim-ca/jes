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
	defined('_JEXEC') or die('Restricted Access');
	
	JHtml::_('behavior.tooltip');

	if (!$this->pluginStatus["Content - ElasticSearch"]->enabled){
		JError::raiseWarning( 100, JText::_('COM_ELASTICSEARCH_ENABLE_ELASTIC_PLUGIN') );
	}

	if (!$this->pluginStatus["System - ElasticaLib"]->enabled){
		JError::raiseWarning( 100, JText::_('COM_ELASTICSEARCH_ENABLE_ELASTICA_PLUGIN') );
	}
	if (($this->status['ok'])!="ok"){
		JError::raiseWarning( 100, JText::_('COM_ELASTICSEARCH_ELASTICSEARCH_DISCONNECTED') );
	}
?>

<h2><?php echo JText::_("Status"); ?></h2>
<ul>
	<li><b><?php echo JText::_('COM_ELASTICSEARCH_SERVER_CONNECTION'); ?> :</b><?php echo ($this->status['ok']) ?  "ok" : "not ok"; ?></li>
	<li><b><?php echo JText::_('COM_ELASTICSEARCH_SERVER_ELASTICSEARCH_NODE'); ?></b> : <?php echo $this->escape($this->status['name']); ?></li>
	<li><b><?php echo JText::_('COM_ELASTICSEARCH_SERVER_INDEX'); ?></b> :<?php  echo $this->escape($this->indexName); ?></li>
</ul>
	
<h2><?php echo JText::_("Types"); ?></h2>

<?php if($this->items): ?>
<table class="adminlist" style="clear: both; width:500px">
	<thead>
		<tr>
			<th width="15%" style="">
				<?php echo JText::_('COM_ELASTICSEARCH_NAME'); ?>
			</th>
			<th class="center nowrap">
				<?php echo JText::_('COM_ELASTICSEARCH_LANG'); ?>
			</th>
			<th  width="20%" class="wrap">
				<?php echo JText::_('COM_ELASTICSEARCH_BOOST'); ?>
			</th>
			<th  width="20%" class="wrap">
				<?php echo JText::_('COM_ELASTICSEARCH_COUNT'); ?>
			</th>

		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $i => $item): ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td class="center" style="font-size: 12px;font-weight: bold;">
				<?php echo $item['name']; ?>
			</td>
			<td class="center">
				<?php echo implode(', ',$item['lang']); ?>
			</td>
			<td class="center">
				<?php echo $item['boost'] ?>
			</td>
			<td class="center">
				<?php echo $item['count'] ?>
			</td>
	
	
	<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7" class="nowrap">
		
			</td>
		</tr>
	</tfoot>
</table>

<?php else:
echo "<p>".JText::_("COM_ELASTICSEARCH_NO_TYPE_INDEXED")."</p>";
endif; ?>

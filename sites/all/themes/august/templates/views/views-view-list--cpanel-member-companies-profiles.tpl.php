<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * - $title : The title of this group of rows.  May be empty.
 * - $options['type'] will either be ul or ol.
 * @ingroup views_templates
 */
	$names = node_type_get_names();
?>
<div class="btn-toolbar" style="margin: 0;">
<div class="btn-group">
  <?php if(count($rows) > 1): ?>
    <a class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
    View
    <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
    <?php foreach ($rows as $id => $row): ?>
      <li class="<?php print $classes_array[$id]; ?>"><?php print l($names[$view->result[$id]->node_type], 'node/'.$view->result[$id]->nid); ?></li>
    <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <?php foreach ($rows as $id => $row): ?>
      <?php print l('View&nbsp;&nbsp;&nbsp;', 'node/'.$view->result[$id]->nid, array('html'=>true, 'attributes'=>array('class'=>array('btn', 'btn-mini', 'btn-primary')))); ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<div class="btn-group">
  <?php if(count($rows) > 1): ?>
    <a class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
    Edit
    <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
    <?php foreach ($rows as $id => $row): ?>
      <li class="<?php print $classes_array[$id]; ?>"><?php print l($names[$view->result[$id]->node_type], 'node/'.$view->result[$id]->nid.'/edit'); ?></li>
    <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <?php foreach ($rows as $id => $row): ?>
      <?php print l('Edit&nbsp;&nbsp;&nbsp;', 'node/'.$view->result[$id]->nid.'/edit', array('html'=>true, 'attributes'=>array('class'=>array('btn', 'btn-mini', 'btn-primary')))); ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
</div>
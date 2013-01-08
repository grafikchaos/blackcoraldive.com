<?php
	$names = node_type_get_names();
	$types = array();
	$nid = $row->nid;

  $query = db_select('node', 'n');
  $query->join('field_data_field_member_type', 'f', 'n.nid = f.entity_id');
  $query->join('field_data_field_machine_name', 'm', 'f.field_member_type_tid = m.entity_id');
  $query->fields('m',array('field_machine_name_value'))
  	->condition('nid', $nid,'=');//LIMIT to 2 records

  $result = $query->execute()->fetchAll();
  $types = array();
  foreach($result as $field){
  	$type = $field->field_machine_name_value;
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', $type)
      ->fieldCondition('field_belongs_to_member', 'target_id', $nid, '=')
      ->range(0, 1);
    $result = $query->execute();
    $types[$type] = empty($result['node']) ? FALSE : key($result['node']);
  }
?>
<div class="btn-toolbar" style="margin: 0;">
<div class="btn-group">
  <?php if(count($types) > 1): ?>

    <a class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
    View
    <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
    <?php foreach ($types as $type => $i): ?>
    	<?php if($i): ?>
      	<li><?php print l($names[$type], 'node/'.$i); ?></li>
      <?php else: ?>  
      	<li><?php print l('<i class="icon-plus"></i> Create '.$names[$type]. ' profile', 'node/add/'.$type, array('query' => array('field_belongs_to_member'=>$nid, 'destination'=>'admin/manage/members'), 'html'=>true)); ?></li>
      <?php endif; ?>    
    <?php endforeach; ?>
    </ul>

	<?php else: ?>

    <?php foreach ($types as $type => $i): ?>
      <?php if($i) print l('View', 'node/'.$i, array('html'=>true, 'attributes'=>array('class'=>array('btn', 'btn-mini', 'btn-primary')))); ?>
      <?php if(!$i) print l('<i class="icon-plus"></i> Create profile', 'node/add/'.$type, array('query' => array('field_belongs_to_member'=>$nid, 'destination'=>'admin/manage/members'), 'html'=>true, 'attributes'=>array('class'=>array('btn', 'btn-mini', 'btn-success')))); ?>
    <?php endforeach; ?>

	<?php endif; ?>
</div>
<?php if($i): ?>
<div class="btn-group">
  <?php if(count($types) > 1): ?>

    <a class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
    Edit
    <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
    <?php foreach ($types as $type => $i): ?>
    	<?php if($i): ?>
      	<li><?php print l($names[$type], 'node/'.$i.'/edit'); ?></li>
      <?php endif; ?>    
    <?php endforeach; ?>
    </ul>

	<?php else: ?>

    <?php foreach ($types as $type => $i): ?>
      <?php if($i) print l('Edit', 'node/'.$i.'/edit', array('query' => array('destination'=>'admin/manage/members'), 'html'=>true, 'attributes'=>array('class'=>array('btn', 'btn-mini', 'btn-primary')))); ?>
    	<?php endforeach; ?>

	<?php endif; ?>
</div>
<?php endif; ?>
</div>
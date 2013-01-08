<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php print render($content['field_company_logo']); ?>
  <h2<?php print $title_attributes; ?> class="member-title"><?php print $node->member_company_title; ?></h2>
  <h5>License #<?php print $node->member_company_license_number; ?></h5>
  <?php print render($content['field_member_website']); ?>
  <hr />
  <?php 
    hide($content['field_email']);
    hide($content['links']);
    hide($content['sharethis']);
    hide($content['flag_favorite']);
    hide($content['field_company_logo']);
    hide($content['field_member_website']);
  ?>
  <?php print render($content); ?>
  <hr />
  <?php print l('View '.ucfirst($node->type).' Profile', 'node/'.$node->nid, array('attributes'=>array('class'=>array('test')))); ?>
  <hr />
  <?php 
    $showcases = variable_get('showcase_active', array());
    $showcases += variable_get('showcase_archive', array());
    $showcases = implode('+', $showcases);
  ?>  
  <a href="<?php print url('homes/new'); ?>?company_name=<?php print urlencode($node->title); ?>&showcase=<?php print $showcases; ?>" class="test">View more homes by <?php print $node->member_company_title; ?></a>
  <hr />
  <?php print l('Email this '.$node->type, 'node/'.$node->nid, array('fragment' => 'contact-form', 'attributes'=>array('data-toggle'=>'modal', 'title' => 'Email this '.$node->type, 'class'=>array('btn btn-large btn-inverse email-member')))); ?>

  <div class="modal hide fade" id="contact-form">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">×</button>
      <h3>Contact <?php print $node->member_company_title; ?></h3>
    </div>
    <?php
      print render($content['field_email']);
    ?>
  </div>
</article> <!-- /.node -->

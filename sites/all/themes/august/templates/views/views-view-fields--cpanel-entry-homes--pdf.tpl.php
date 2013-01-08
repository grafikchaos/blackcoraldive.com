<hr><label></label>
<?php foreach ($fields as $id => $field): ?><?php print $field->wrapper_prefix; ?>
<label style="color:#a4a4a4;"><?php print $field->label_html; ?></label>
<?php print $field->content; ?><?php print $field->wrapper_suffix; ?><?php endforeach; ?>
<br>
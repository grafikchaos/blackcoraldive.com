<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <header class="post-header">
    <?php print render($title_prefix); ?>
    <?php if (!$page && $title): ?>
      <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
    <?php endif; ?>
    <?php print render($title_suffix); ?>

    <?php if ($display_submitted): ?>
      <span class="submitted">
        <?php print $submitted; ?>
      </span>
    <?php endif; ?>
  </header>

  <?php
    // Hide comments, tags, and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    hide($content['field_newsroom_categories']);
    hide($content['field_newsroom_tags']);
    hide($content['field_documents']);
    print render($content);
  ?>

  <?php if (!empty($content['field_newsroom_categories']['#items']) || !empty($content['field_newsroom_tags']['#items'])): ?>
    <footer class="content-extras">
      <?php print render($content['field_documents']); ?>
      <?php print render($content['field_newsroom_categories']); ?>
      <?php print render($content['field_newsroom_tags']); ?>
    </footer>
  <?php endif; ?>
  <?php print render($content['comments']); ?>

</article> <!-- /.node -->

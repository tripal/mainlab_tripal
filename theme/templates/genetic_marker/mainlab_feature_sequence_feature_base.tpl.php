<?php

$feature  = $variables['node']->feature;

$feature = chado_expand_var($feature,'field','feature.residues');

$headers = array();
$rows = array();
$rows [] = array(array('data' => 'Name', 'header' => TRUE, 'width' => '20%'), $feature->name);
$rows [] = array(array('data' => 'Unique Name', 'header' => TRUE, 'width' => '20%'), $feature->uniquename);
$rows [] = array(array('data' => 'Type', 'header' => TRUE, 'width' => '20%'), 'Sequence');
$link = mainlab_tripal_link_record('organism', $feature->organism_id->organism_id);
$rows [] = array(array('data' => 'Organism', 'header' => TRUE, 'width' => '20%'), $link ? "<a href=\"$link\">".$feature->organism_id->genus ." " . $feature->organism_id->species . "</a>" : $feature->organism_id->genus ." " . $feature->organism_id->species);
if ($feature->seqlen) {
  $rows [] = array(array('data' => 'Length', 'header' => TRUE, 'width' => '20%'), $feature->seqlen);
}
// allow site admins to see the feature ID
if (user_access('view ids')) {
  $rows[] = array(array('data' => 'Feature ID', 'header' => TRUE, 'class' => 'tripal-site-admin-only-table-row'), array('data' => $feature->feature_id, 'class' => 'tripal-site-admin-only-table-row'));
}
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_feature_sequence_feature-table-base',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);

if ($feature->residues) { ?>
    <pre id="tripal_feature-sequence-residues"><?php 
      print ">$feature->uniquename<br>";
      print wordwrap($feature->residues, 60, '<br>', TRUE); ?>  
    </pre>
<?php
}
?>

<style type="text/css">
#tripal_feature-sequence-residues {
   border:none;
   padding:none;
   border:1px solid #DDDDDD;
}

</style>

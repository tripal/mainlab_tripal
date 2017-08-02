<?php
$feature  = $variables['node']->feature;
$feature = chado_expand_var($feature,'table','featureprop', array('return_array' => 1));

// Process featureprop (i.e. Triait Symbol / Comments
$properties = $feature->featureprop;

$symbol = "N/A";
$comments = "N/A";
foreach ($properties AS $prop) {
    if ($prop->type_id->name == 'published_symbol') {
        $symbol = $prop->value;
    } else if ($prop->type_id->cv_id->name == 'MAIN' && $prop->type_id->name == 'comments') {
        $comments = $prop->value;
    }
}

// Generate MTL details
$mtl_details = $feature->mainlab_mtl->mtl_details;

// Synonyms
$synonyms = "N/A";
if ($mtl_details->synonyms) {
  $synonyms = "";
  foreach ($mtl_details->synonyms as $syn) {
    $synonyms .= $syn->name . ". ";
  }
}

// Population
$population = "N/A";
$slink = mainlab_tripal_link_record('stock', $mtl_details->population->stock_id);
if ($slink) {
  $population = "<a href=\"$slink\">". $mtl_details->population->uniquename . "</a>";
} 
else if ($mtl_details->population->uniquename) {
  $population = $mtl_details->population->uniquename;
}

// Female Parent
$fparent = "N/A";
$matlink = mainlab_tripal_link_record('stock', $mtl_details->population->mat_stock_id);
if ($matlink) {
  $fparent = "<a href=\"$matlink\">". $mtl_details->population->maternal . "</a>";
} 
else if ($mtl_details->population->maternal) {
  $fparent = $mtl_details->population->maternal;
}

// Male Parent
$mparent = "N/A";
$patlink = mainlab_tripal_link_record('stock', $mtl_details->population->pat_stock_id);
if ($patlink) {
  $mparent = "<a href=\"$patlink\">". $mtl_details->population->paternal . "</a>";
} 
else if ($mtl_details->population->paternal) {
  $mparent = $mtl_details->population->paternal;
}

// Colocalizing Markers
$colocM = "N/A";
if (count($mtl_details->colocalizing_marker) != 0) {
  $colocM = "";
}
foreach($mtl_details->colocalizing_marker as $marker) {
  $mlink = mainlab_tripal_link_record('feature', $marker->feature_id);
  $colocM .= "<a href=\"$mlink\">" . $marker->colocalizing_marker . "</a><br>";
}

// Neighboring Marks
$neighborM = "N/A";
if (count($mtl_details->neighboring_marker) != 0) {
  $neighborM = "";
}
foreach($mtl_details->neighboring_marker as $marker) {
  $nlink = mainlab_tripal_link_record('feature', $marker->feature_id);
  $neighborM .= "<a href=\"$nlink\">" . $marker->neighboring_marker . "</a><br>";
}

$headers = array();
$rows = array();
$rows [] = array(array('data' => 'MTL Label', 'header' => TRUE, 'width' => '20%'), $feature->uniquename);
$rows [] = array(array('data' => 'Published Symbol', 'header' => TRUE, 'width' => '20%'), $symbol);
$rows [] = array(array('data' => 'Trait Name', 'header' => TRUE, 'width' => '20%'), $feature->name);
$rows [] = array(array('data' => 'Trait Alias', 'header' => TRUE, 'width' => '20%'), $synonyms);
$rows [] = array(array('data' => 'Population', 'header' => TRUE, 'width' => '20%'), $population);
$rows [] = array(array('data' => 'Female Parent', 'header' => TRUE, 'width' => '20%'), $fparent);
$rows [] = array(array('data' => 'Male Parent', 'header' => TRUE, 'width' => '20%'), $mparent);
$rows [] = array(array('data' => 'Colocalizing Marker', 'header' => TRUE, 'width' => '20%'), $colocM);
$rows [] = array(array('data' => 'Neighboring Marker', 'header' => TRUE, 'width' => '20%'), $neighborM);
$rows [] = array(array('data' => 'Comments', 'header' => TRUE, 'width' => '20%'), $comments);
// allow site admins to see the feature ID
if (user_access('view ids')) {
  $rows[] = array(array('data' => 'Feature ID', 'header' => TRUE, 'class' => 'tripal-site-admin-only-table-row'), array('data' => $feature->feature_id, 'class' => 'tripal-site-admin-only-table-row'));
}
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_analysis_unigene-table-base',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);
?>

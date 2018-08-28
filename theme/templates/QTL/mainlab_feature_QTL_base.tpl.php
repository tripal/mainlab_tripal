<?php
$feature  = $variables['node']->feature;
$feature = chado_expand_var($feature,'table','featureprop', array('return_array' => 1));

// Process featureprop (i.e. Triait Symbol / LOD / R2 / AD ratio / Comments
$properties = $feature->featureprop;

$symbol   = "N/A";
$LOD      = "N/A";
$R2       = "N/A";
$ADr      = "N/A";
$comments = "N/A";
if ($properties) {
  foreach ($properties AS $prop) {
    if ($prop->type_id->name == 'published_symbol') {
      $symbol = $prop->value;
    } 
    else if ($prop->type_id->name == 'LOD') {
      $LOD = is_numeric($prop->value) ? round($prop->value, 2) : $prop->value;
    } 
    else if ($prop->type_id->name == 'R2') {
      $R2 = is_numeric($prop->value) ? round($prop->value, 2) : $prop->value;
    } 
    else if ($prop->type_id->name == 'additivity_dominance_ratio') {
      $ADr = is_numeric($prop->value) ? round($prop->value, 2) : $prop->value;
    } 
    else if ($prop->type_id->cv_id->name == 'MAIN' && $prop->type_id->name == 'comments') {
      $comments = $prop->value;
    }
  }
}

// Generate QTL details
$qtl_details = $feature->mainlab_qtl->qtl_details;

// Synonyms
$synonyms = "N/A";
if ($qtl_details->synonyms) {
  $synonyms = "";
  foreach ($qtl_details->synonyms as $syn) {
    $synonyms .= $syn->name . ". ";
  }
}

// Population
$population = "N/A";
$slink = mainlab_tripal_link_record('stock', $qtl_details->population->stock_id);
if ($slink) {
  $population = "<a href=\"$slink\">". $qtl_details->population->uniquename . "</a>";
} 
else if ($qtl_details->population->uniquename) {
  $population = $qtl_details->population->uniquename;
}

// Female Parent
$fparent = "N/A";
$matlink = mainlab_tripal_link_record('stock', $qtl_details->population->mat_stock_id);
if ($matlink) {
  $fparent = "<a href=\"$matlink\">". $qtl_details->population->maternal . "</a>";
} 
else if ($qtl_details->population->maternal) {
  $fparent = $qtl_details->population->maternal;
}

// Male Parent
$mparent = "N/A";
$patlink = mainlab_tripal_link_record('stock', $qtl_details->population->pat_stock_id);
if ($patlink) {
  $mparent = "<a href=\"$patlink\">". $qtl_details->population->paternal . "</a>";
} 
else if ($qtl_details->population->paternal) {
  $mparent = $qtl_details->population->paternal;
}

// Colocalizing Markers
$colocM = "N/A";
if (count($qtl_details->colocalizing_marker) != 0) {
  $colocM = "";
}
if ($qtl_details->colocalizing_marker) {
  foreach($qtl_details->colocalizing_marker as $marker) {
    $mlink = mainlab_tripal_link_record('feature', $marker->feature_id);
    $colocM .= "<a href=\"$mlink\">" . $marker->colocalizing_marker . "</a><br>";
  }
}

// Neighboring Marks
$neighborM = "N/A";
if (count($qtl_details->neighboring_marker) != 0) {
  $neighborM = "";
}
if ($qtl_details->neighboring_marker) {
  foreach($qtl_details->neighboring_marker as $marker) {
    $nlink = mainlab_tripal_link_record('feature', $marker->feature_id);
    $neighborM .= "<a href=\"$nlink\">" . $marker->neighboring_marker . "</a><br>";
  }
}

// Environments
$env = "N/A";
if (count($qtl_details->environment) != 0) {
  $env = "";
}
if ($qtl_details->environment) {
  foreach($qtl_details->environment as $environment) {
    $elink = mainlab_tripal_link_record('nd_geolocation', $environment->nd_geolocation_id);
    $env .= "<a href=\"$elink\">" . $environment->description . "</a><br>";
  }
}

$headers = array();
$rows = array();
$rows [] = array(array('data' => 'QTL Label', 'header' => TRUE, 'width' => '20%'), $feature->uniquename);
$rows [] = array(array('data' => 'Published Symbol', 'header' => TRUE, 'width' => '20%'), $symbol);
$rows [] = array(array('data' => 'Trait Name', 'header' => TRUE, 'width' => '20%'), $feature->name);
$rows [] = array(array('data' => 'Trait Alias', 'header' => TRUE, 'width' => '20%'), $synonyms);
$rows [] = array(array('data' => 'Trait Study', 'header' => TRUE, 'width' => '20%'), $qtl_details->study);
$rows [] = array(array('data' => 'Population', 'header' => TRUE, 'width' => '20%'), $population);
$rows [] = array(array('data' => 'Female Parent', 'header' => TRUE, 'width' => '20%'), $fparent);
$rows [] = array(array('data' => 'Male Parent', 'header' => TRUE, 'width' => '20%'), $mparent);
$rows [] = array(array('data' => 'Colocalizing Marker', 'header' => TRUE, 'width' => '20%'), $colocM);
$rows [] = array(array('data' => 'Neighboring Marker', 'header' => TRUE, 'width' => '20%'), $neighborM);
$rows [] = array(array('data' => 'Environment', 'header' => TRUE, 'width' => '20%'), $env);
$rows [] = array(array('data' => 'LOD', 'header' => TRUE, 'width' => '20%'), $LOD);
$rows [] = array(array('data' => 'Additivity Dominance Ratio', 'header' => TRUE, 'width' => '20%'), $ADr);
$rows [] = array(array('data' => 'R2', 'header' => TRUE, 'width' => '20%'), $R2);
$rows [] = array(array('data' => 'Comments', 'header' => TRUE, 'width' => '20%'), $comments);
// allow site admins to see the feature ID
if (user_access('view ids')) {
  $rows[] = array(array('data' => 'Feature ID', 'header' => TRUE, 'class' => 'tripal-site-admin-only-table-row'), array('data' => $feature->feature_id, 'class' => 'tripal-site-admin-only-table-row'));
}
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_feature_QTL-table-base',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);
?>
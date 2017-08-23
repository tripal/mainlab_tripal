<?php
$feature  = $variables['node']->feature;  ?>

<div class="tripal_feature-data-block-desc tripal-data-block-desc"></div> <?php
 
// the $headers array is an array of fields to use as the colum headers. 
// additional documentation can be found here 
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
// This table for the analysis has a vertical header (down the first column)
// so we do not provide headers here, but specify them in the $rows array below.
$headers = array();
$opt = array(
  'return_array' => 1,
  'include_fk' => array(
    'feature_id' => 1
  )
);
$feature = chado_expand_var($feature, 'table', 'feature_stock', $opt);
//$feature = chado_expand_var($feature, 'table', 'feature_pub', $opt);
$feature = chado_expand_var($feature, 'table', 'library_feature', $opt);

$feature_stock = $feature->feature_stock;
$feature_pub = $feature->feature_pub;
$feature_lib = $feature->library_feature;

// the $rows array contains an array of rows where each row is an array
// of values for each column of the table in that row.  Additional documentation
// can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7 
$rows = array();

// Name row
$rows[] = array(
  array(
    'data' => 'NCBI Accession',
    'header' => TRUE,
    'width' => '20%',
  ),
  $feature->name
);
// Unique Name row
$rows[] = array(
  array(
    'data' => 'Version',
    'header' => TRUE
  ),
  $feature->uniquename
);
// Type row
$rows[] = array(
  array(
    'data' => 'Type',
    'header' => TRUE
  ),
  $feature->type_id->name
);
// Organism row
$organism = $feature->organism_id->genus ." " . $feature->organism_id->species ." (" . $feature->organism_id->common_name .")";
$link = mainlab_tripal_link_record('organism', $feature->organism_id->organism_id);
if ($link) {
  $organism = l("<i>" . $feature->organism_id->genus . " " . $feature->organism_id->species . "</i> (" . $feature->organism_id->common_name .")", $link, array('html' => TRUE));
} 
$rows[] = array(
  array(
    'data' => 'Organism',
    'header' => TRUE,
  ),
  $organism
);
if ($feature_lib) {
  $display_lib = '';
  foreach ($feature_lib AS $fl) {
    $lib = chado_generate_var('library', array('library_id' => $fl->library_id));
    $lname = $lib->name ? $lib->name : $lib->uniquename;
    $link = mainlab_tripal_link_record('library', $fl->library_id);
    if ($link) {
      $display_lib .= "<a href=\"$link\">" . $lname . '</a><br>';
    }
    else {
      $display_lib .= $lname . '<br>';
    }
  }
  $rows[] = array(
    array(
      'data' => 'Library',
      'header' => TRUE
    ),
    $display_lib
  );
}

if ($feature_stock) {
  $display_stock = '';
  foreach ($feature_stock AS $fs) {
    $stock = chado_generate_var('stock', array('stock_id' => $fs->stock_id));
    $sname = $stock->name ? $stock->name : $stock->uniquename;
    $link = mainlab_tripal_link_record('stock', $fs->stock_id);
    if ($link) {
      $display_stock .= "<a href=\"$link\">" . $sname . '</a><br>';
    }
    else {
      $display_stock .= $sname . '<br>';
    }
  }
  $rows[] = array(
    array(
      'data' => 'Stock',
      'header' => TRUE
    ),
    $display_stock
  );
}

// allow site admins to see the feature ID
if (user_access('view ids')) { 
  // Feature ID
  $rows[] = array(
    array(
      'data' => 'Feature ID',
      'header' => TRUE,
      'class' => 'tripal-site-admin-only-table-row',
    ),
    array(
      'data' => $feature->feature_id,
      'class' => 'tripal-site-admin-only-table-row',
    ),
  );
}
// Is Obsolete Row
if($feature->is_obsolete == TRUE){
  $rows[] = array(
    array(
      'data' => '<div class="tripal_feature-obsolete">This feature is obsolete</div>',
      'colspan' => 2
    ),
  );
}

// the $table array contains the headers and rows array as well as other
// options for controlling the display of the table.  Additional
// documentation can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_feature-table-base',
    'class' => 'tripal-data-table'
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);

// once we have our table array structure defined, we call Drupal's theme_table()
// function to generate the table.
print theme_table($table); 

<?php
$feature  = $variables['node']->feature;
$fstocks = chado_generate_var('feature_stock', array('feature_id' => $feature->feature_id), array('return_array' => 1));

if (count($fstocks) > 0) {
  $headers = array('Stock Name', 'Type');
  
  // the $rows array contains an array of rows where each row is an array
  // of values for each column of the table in that row.  Additional documentation
  // can be found here:
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $rows = array();
  
  foreach ($fstocks as $stock){
    $stk = chado_generate_var('stock', array('stock_id' => $stock->stock_id->stock_id));
    
    $link = mainlab_tripal_link_record('stock', $stk->stock_id);
    $name = $stk->name ? $stk->name : $stk->uniquename;

    $rows[] = array(
      $link ? "<a href='$link'>" . $name . '</a>': $name,
      $stk->type_id->name,
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
      'id' => 'tripal_featur-table-library',
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
}

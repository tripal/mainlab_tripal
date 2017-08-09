<?php
$feature  = $variables['node']->feature;

// expand feature to include contacts
$options = array(
  'return_array' => 1,
  'include_fk' => array(
    'contact_id' => 1
  )
);
$feature = chado_expand_var($feature, 'table', 'library_feature', $options);
$library_feature = $feature->library_feature;

if ($library_feature) {
  $headers = array('Library Name', 'Type');
  
  // the $rows array contains an array of rows where each row is an array
  // of values for each column of the table in that row.  Additional documentation
  // can be found here:
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $rows = array();
  
  foreach ($library_feature as $lib){
    $lib = chado_generate_var('library', array('library_id' => $lib->library_id));
    
    $link = mainlab_tripal_link_record('library', $lib->library_id);
    $name = $lib->name ? $lib->name : $lib->uniquename;

    $rows[] = array(
      $link ? "<a href='$link'>" . $name . '</a>': $name,
      $lib->type_id->name,
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

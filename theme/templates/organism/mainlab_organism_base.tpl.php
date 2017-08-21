<?php
$node = $variables['node'];
$organism  = $node->organism;
$organism = chado_expand_var($organism,'field','organism.comment'); ?>

<div class="tripal_organism-data-block-desc tripal-data-block-desc"></div><?php

// generate the image tag
$image = '';
$image_url = '';

// If image not found, try to get image from organism_id (Tripal 1.x)

$file =  '/sites/default/files/tripal/tripal_organism/images/';
if (isset($organism->nid)) {
  $file .= $organism->nid . '.jpg';
}
else {
  $file .= $organism->genus . '_' . $organism->species . '.jpg';
}
if(file_exists(getcwd() . $file)) {
  global $base_url;
  $image_url = $base_url . $file; 
}

if (!$image_url && db_table_exists("chado_$node->bundle")) {
  $nid = db_select("chado_$node->bundle", 'b')
  ->fields('b', array('nid'))
  ->condition('entity_id', $node->id)
  ->execute()
  ->fetchField();
  if ($nid) {
    $fid = db_select('file_usage', 'fu')
    ->fields('fu', array('fid'))
    ->condition('id', $nid)
    ->execute()
    ->fetchField();
    if ($fid) {
      $file = file_load($fid);
      $image_url = file_create_url($file->uri);
    }
  }
}
if (!$image_url) {
  $image_url = tripal_get_organism_image_url($organism);
}
if ($image_url) {
  $image = "<img class=\"tripal-organism-img\" src=\"$image_url\">";
}

// the $headers array is an array of fields to use as the colum headers. 
// additional documentation can be found here 
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
// This table for the organism has a vertical header (down the first column)
// so we do not provide headers here, but specify them in the $rows array below.
$headers = array();

// the $rows array contains an array of rows where each row is an array
// of values for each column of the table in that row.  Additional documentation
// can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7 
$rows = array();

// genus row
$rows[] = array(
  array(
    'data' => 'Genus', 
    'header' => TRUE,
    'width' => '20%',
  ),
  '<i>' . $organism->genus . '</i>'
);

// species row
$rows[] = array(
  array(
    'data' => 'Species', 
    'header' => TRUE
  ), 
  '<i>' . $organism->species . '</i>'
);

// common name row
$rows[] = array(
  array(
    'data' => 'Common Name',
    'header' => TRUE
  ),
  $organism->common_name,
);

// abbreviation row
$rows[] = array(
  array(
    'data' => 'Abbreviation', 
    'header' => TRUE
  ),
  '<i>'. $organism->abbreviation . '</i>'
);

// allow site admins to see the organism ID
if (user_access('view ids')) {
  // Organism ID
  $rows[] = array(
    array(
      'data'   => 'Organism ID',
      'header' => TRUE,
      'class'  => 'tripal-site-admin-only-table-row',
    ),
    array(
      'data'  => $organism->organism_id,
      'class' => 'tripal-site-admin-only-table-row',
    ),
  );
}

// If the comment is an HTML table, append the content to the main table
$comment = preg_replace('/\s{2,}/', ' ', $organism->comment);
$has_table = preg_match_all ('/<tr>(.*?)<\/tr>/i', $comment, $matches);

if ($has_table) {
  foreach ($matches[1] AS $crow) {
    $has_row = preg_match_all('/<td>(.*?)<\/td>/i', $crow, $rmatches);
    if ($has_row) {
      $rheader = $rmatches[1][0];
      $rdata = $rmatches[1][1];
      $rows[] = array(
        array(
          'data' => $rheader,
          'header' => TRUE
        ),
        $rdata
      );
    }
  }
}
else {
  $rows[] = array(
    array(
      'data' => $organism->comment,
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
    'id' => 'tripal_organism-table-base',
    'class' => 'tripal-organism-data-table tripal-data-table',
  ), 
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(), 
  'empty' => '', 
); 

// once we have our table array structure defined, we call Drupal's theme_table()
// function to generate the table.
print theme_table($table); ?>
<div style="text-align: justify"><?php 
  print $image;
?></div>  
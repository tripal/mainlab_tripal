<?php
$featuremap = $variables['node']->featuremap;
$feature_positions = array();

// get the features aligned on this map
$options = array(  
  'return_array' => 1,
  'order_by' => array('map_feature_id' => 'ASC'),
  'include_fk' => array(
    'map_feature_id' => array(
      'type_id' => 1,
      'organism_id' => 1,
    ),
    'feature_id' => array(
      'type_id' => 1,
    ),
    'featuremap_id' => array(
       'unittype_id' => 1,
    ),
  ),
);

$featuremap = chado_expand_var($featuremap, 'table', 'featurepos', $options);
$feature_positions = $featuremap->featurepos;


// get the total number of records
$total_features = count($feature_positions);


if(count($feature_positions) > 0){ ?>
  <div class="tripal_featuremap-data-block-desc tripal-data-block-desc">This map contains <?php print number_format($total_features) ?> features:</div> <?php 
  
  // the $headers array is an array of fields to use as the colum headers.
  // additional documentation can be found here
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $headers = array('Linkage Group', 'Locus Name', 'Type', 'Position');
  
  // the $rows array contains an array of rows where each row is an array
  // of values for each column of the table in that row.  Additional documentation
  // can be found here:
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $rows = array();
  
  foreach ($feature_positions as $position){
    $map_feature = $position->map_feature_id;
    $feature     = $position->feature_id;  
    $organism    = $map_feature->organism_id; 

    // check if there are any values in the featureposprop table for the start and stop
    $mappos = $position->mappos;
    $options = array(
      'return_array' => 1,
      'include_fk' => array(
        'type_id' => 1,            
      ),
    );
    $position = chado_expand_var($position, 'table', 'featureposprop', $options);
    $featureposprop = $position->featureposprop;
    $start = 0;
    $stop = 0;
    if (is_array($featureposprop)) {
      foreach ($featureposprop as $index => $property) {
         if ($property->type_id->name == 'start') {
           $start = $property->value;
         }
         if ($property->type_id->name == 'stop') {
           $stop = $property->value;
         }
      }      
    }  
    if ($start and $stop and $start != $stop) {
      $mappos = "$start-$stop";
    }
    if ($start and !$stop) {
      $mappos = $start;
    } 
    if ($start and $stop and $start == $stop) {
      $mappos = $start;
    }
    
    if ($feature->type_id->name == 'QTL' || $feature->type_id->name == 'MTL') {
      $qtl_posprop = $position->featureposprop;
      foreach ($qtl_posprop AS $qtl_pp) {
        if ($qtl_pp->type_id->name == 'qtl_peak') {
          $mappos = $qtl_pp->value;
        }
      }
    }
    
    $mfname = $map_feature->name;
    if (property_exists($map_feature, 'nid')) {
      $mfname =  l($mfname, 'node/' . $map_feature->nid, array('attributes' => array('target' => '_blank')));
    }
    $orgname = $organism->genus ." " . $organism->species ." (" . $organism->common_name .")";
    if (property_exists($organism, 'nid')) {
      $orgname = l(
        "<i>" . $organism->genus . " " . $organism->species . "</i> (" . $organism->common_name .")", 
        "node/". $organism->nid, 
        array('html' => TRUE, 'attributes' => array('target' => '_blank'))
      );
    }
    if ($organism->genus == 'N/A') {
      $orgname = "N/A";
    }
    $organism =  $organism->genus . ' ' . $organism->species;

    $fname = $feature->name;
    if (property_exists($feature, 'nid')) {
      $fname = l($fname, 'node/' . $feature->nid, array('attributes' => array('target' => '_blank')));
    }
      
    $rows[] = array(
      $mfname,
      $fname,
      $feature->type_id->name,
      number_format($mappos, 1) . ' ' . $position->featuremap_id->unittype_id->name
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
      'id' => 'tripal_featuremap-table-featurepos',
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


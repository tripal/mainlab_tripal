<?php
$node = $variables['node'];
$feature = $node->feature;
$unigenes = $feature->tripal_analysis_unigene->unigenes;

// if this feature has a unigene then we want to show the box
if($unigenes){ ?>

  <div class="tripal_feature-info-box-desc tripal-info-box-desc">This <?php print $feature->type_id->name ?> is part of the following unigenes:</div><?php 

  $headers = array('Unigene Name', 'Analysis Name', 'Sequence type in Unigene');
  $rows = array();
  foreach ($unigenes AS $unigene) {
    $unigene_name = '';
    $link = mainlab_tripal_link_record('analysis', $unigene->analysis_id);
    if($link){
      $unigene_name .= "<a href=\"" . $link . "\">$unigene->unigene_name</a>";
    } 
    else {
      $unigene_name .= $unigene->unigene_name;
    }
    $analysis = '';
    if($link){
      $analysis .= "<a href=\"" . $link . "\">$unigene->name</a>";
    } 
    else {
      $analysis .= $unigene->name;
    }
    $type = '';
    if(property_exists($unigene, 'singlet')){
      $type .= "Singlet";
    } 
    else {
      $type .= $feature->type_id->name;
    }
    $rows[] = array(
      $unigene_name,
      $analysis,
      $type
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
      'id' => 'tripal_feature_unigene-table',
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  print theme_table($table);

}

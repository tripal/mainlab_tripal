<?php
$node = $variables['node'];
$organism = $node->organism;
$unigenes = $organism->tripal_analysis_unigene->unigenes;

if(count($unigenes) > 0){ ?>

  <div class="tripal_organism-info-box-desc tripal-info-box-desc">Below is a list of unigenes available for <i><?php print $organism->genus ?> <?php print $organism->species ?></i>. Click the unigene name for further details.</div><?php

  $headers = array('Unigene Name', 'Analysis Name', 'Date Constructed', 'Stats');
  $rows = array();
  // Unigene Name row
  foreach ($unigenes AS $unigene) {
    $unigene_name = '';
    $link = mainlab_tripal_link_record('analysis', $unigene->analysis_id);
    if($link){
      $unigene_name .= "<a href=\"" . $link . "\">$unigene->unigene_name</a>";
    } 
    else {
      $unigene_name .= $unigene->unigene_name;
    }
    $stats = '';
    if ($unigene->num_reads) {
      $stats .= "Reads: $unigene->num_reads<br>";
    }
    if ($unigene->num_clusters) {
      $stats .= "Clusters: $unigene->num_clusters<br>";
    }
    if ($unigene->num_contigs) {
      $stats .= "Contigs: $unigene->num_contigs<br>";
    }
    if ($unigene->num_singlets) {
      $stats .= "Singlets: $unigene->num_singlets<br>";
    }
    $rows[] = array(
      $unigene_name,
      $unigene->name,
      preg_replace("/^(\d+-\d+-\d+) .*/", "$1", $unigene->timeexecuted),
      $stats
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
  // once we have our table array structure defined, we call Drupal's theme_table()
  // function to generate the table.
  print theme_table($table);

}


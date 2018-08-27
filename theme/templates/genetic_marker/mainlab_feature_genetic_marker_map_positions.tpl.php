<?php
$feature = $variables['node']->feature;
$map_positions = property_exists($feature, 'map_positions') ? $feature->map_positions : array();
$counter_pos = count($map_positions);
 if ($counter_pos > 0) {
   $hasChr = false;
   foreach($map_positions AS $pos) {
     if ($pos->chr) {
       $hasChr = true;
     }
   }
  $header = $hasChr ? array ('#', 'Map Name', 'Linkage Group', 'Bin', 'Chromosome', 'Position', 'Locus') : array ('#', 'Map Name', 'Linkage Group', 'Bin', 'Position', 'Locus');
  $cmap_enabled = variable_get('mainlab_tripal_cmap_links', 1);
  if ($cmap_enabled) {
      $header[] = 'CMap';
  }
  
  $rows = array ();
  $counter = 1; 

  foreach($map_positions AS $pos) {
    $link = mainlab_tripal_link_record('featuremap', $pos->featuremap_id);
    $map = $link ? "<a href=\"$link\">$pos->name</a>" : $pos->name;
    $lg = $pos->linkage_group ? $pos->linkage_group : "N/A";
    $bin = $pos->bin ? $pos->bin : "N/A";
    if ($hasChr) {
      $chr = $pos->chr ?$pos->chr : "N/A";
    }
    $position = number_format($pos->locus_start, 2);
    $locus = $pos->locus_name;
    $highlight = $node->feature->uniquename;
    if ($cmap_enabled) {
      $cmap = (!$pos->urlprefix || !$pos->accession)? "N/A" : "<a href=\"$pos->urlprefix$pos->accession" . "&ref_map_acc=-1&highlight=" . $highlight . "\">View</a>";
      $rows[] = $hasChr ? array ($counter, $map, $lg, $bin, $chr, $position, $locus, $cmap) : array ($counter, $map, $lg, $bin, $position, $locus, $cmap);
    }
    else {
      $rows[] = $hasChr ? array ($counter, $map, $lg, $bin, $chr, $position, $locus) : array ($counter, $map, $lg, $bin, $position, $locus);
    }
    $counter ++;
  }
  $table = array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => array(
      'id' => 'tripal_feature_genetic_marker-table-map-positions',
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  print theme_table($table);
} ?>


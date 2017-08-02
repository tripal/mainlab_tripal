<?php
$feature = $variables['node']->feature;
$map_positions = $feature->mainlab_mtl->map_positions;
$counter_pos = count($map_positions);

if ($counter_pos > 0) {
  $header = array ('#', 'Map Name', 'Linkage Group', 'Bin', 'Chromosome', 'Position', 'CMap');
  $rows = array ();
  $counter = 1; 
  foreach($map_positions AS $pos) {
    $link = mainlab_tripal_link_record('featuremap', $pos->featuremap_id);
    $map = $link ? "<a href=\"$link\">$pos->name</a>" : $pos->name;
    $lg = $pos->linkage_group ? $pos->linkage_group : "N/A";
    $bin = $pos->bin ? $pos->bin : "N/A"; 
    $chr = $pos->chr ?$pos->chr : "N/A";
    $start = number_format($pos->mtl_start, 1) == 0 ? "N/A" : number_format($pos->mtl_start, 1);
    $highlight = $node->feature->uniquename;
    $cmap = (!$pos->urlprefix || !$pos->accession)? "N/A" : "<a href=\"$pos->urlprefix$pos->accession" . "&ref_map_acc=-1&highlight=" . $highlight . "\">View</a>";
    $rows[] = array ($counter, $map, $lg, $bin, $chr, $start, $cmap);
    $counter ++;
  }
  $table = array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => array(
      'id' => 'tripal_feature_mtl-table-map-positions',
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  print theme_table($table);
} ?>

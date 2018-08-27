<?php
$feature = $variables['node']->feature;
$map_positions = $feature->mainlab_qtl->map_positions;
$counter_pos = count($map_positions);

if ($counter_pos > 0) {
  $cmap_enabled = variable_get('mainlab_tripal_cmap_links', 1);
  $header = array ('#', 'Map Name', 'Linkage Group', 'Bin', 'Chromosome', 'Peak', 'Span Start', 'Span Stop');
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
    $chr = $pos->chr ?$pos->chr : "N/A";
    $start = $pos->qtl_start || $pos->qtl_start === '0' ? number_format($pos->qtl_start, 1) : '-';
    $stop = $pos->qtl_stop || $pos->qtl_stop === '0'  ? number_format($pos->qtl_stop, 1) : '-';
    $peak = $pos->qtl_peak || $pos->qtl_peak ? number_format($pos->qtl_peak, 1) : '-';
    $highlight = $node->feature->uniquename;
    if ($cmap_enabled) {
      $cmap = (!$pos->urlprefix || !$pos->accession)? "N/A" : "<a href=\"$pos->urlprefix$pos->accession" . "&ref_map_acc=-1&highlight=" . $highlight . "\">View</a>";
      $rows[] = array ($counter, $map, $lg, $bin, $chr, $peak, $start, $stop, $cmap);
    }
    else {
      $rows[] = array ($counter, $map, $lg, $bin, $chr, $peak, $start, $stop);
    }
    $counter ++;
  }
  $table = array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => array(
      'id' => 'tripal_feature_qtl-table-map-positions',
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  print theme_table($table);
} ?>

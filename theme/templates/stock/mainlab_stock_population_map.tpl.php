<?php
$stock = $variables['node']->stock;
$population_map = $stock->population_map;
$num_population_map = count($population_map);
if ($num_population_map > 0) {
  foreach ($population_map as $map){
    $link = mainlab_tripal_link_record('featuremap', $map->featuremap_id);
    $rows[] =  array("<a href=\"$link\">$map->name</a>", "$map->mtype", "$map->ptype", "$map->ggroup");
  }
  $header = array ('Name', 'Type', 'PopulationType', 'Genome Group');
  $table = array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => array(
      'id' => 'tripal_stock-table-population_map',
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  print theme_table($table);
}
?>

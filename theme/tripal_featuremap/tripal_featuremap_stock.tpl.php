<?php
$featuremap  = $variables['node']->featuremap;
// expand featuremap to include stockprop so we can find out the population size 
$featuremap = chado_expand_var($featuremap, 'table', 'featuremap_stock');
$stock = $featuremap->featuremap_stock->stock_id;
$stockprops = $stock->stockprop;

if ($stock) {
  $rows = array();
  $details = '<table class=\"tripal-subtable\" style=\"margin:0px !important;>';
  foreach ($stockprops AS $prop) {
      $details .= "<tr><td style=\"padding:2px 0px !important;width:100px;border:0px;\">" . str_replace("_", " ", ucfirst($prop->type_id->name)) . ":</td><td style=\"padding:2px 0px 2px 0px;border:0px;\">$prop->value </td></tr>";
  }
  $details .= "</table>";
  $rows [] = array ("<a href=\"/node/$stock->nid\">$stock->uniquename</a>", $stock->type_id->name, $details);
  $header = array ('Name', 'Type', 'Details');
  $table = array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => array(
      'id' => 'tripal_featuremap-table-stock',
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  print theme_table($table);
}


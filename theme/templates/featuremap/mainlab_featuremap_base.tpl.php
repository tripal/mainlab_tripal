<?php

$featuremap  = $variables['node']->featuremap;

// expand featuremap to include the organism
$featuremap = chado_expand_var($featuremap,'table','featuremap_organism', array('return_array' => TRUE));
$organisms = $featuremap->featuremap_organism;
$display_organism = "";
foreach ( $organisms as $org ) {
  $organism = $org->organism_id;
  $link = mainlab_tripal_link_record('organism', $organism->organism_id);
  if ($link) {
    $display_organism .= "<a href=\"$link\" target=\"_blank\">";
  }
  $display_organism .= "$organism->genus $organism->species";
  if ($link) {
    $display_organism .= "</a>";
  }
  $display_organism .= "<br>";
}

// expand featuremap to include the properties.
$featuremap = chado_expand_var($featuremap,'table','featuremapprop', array('return_array' => TRUE));
$featuremapprop = $featuremap->featuremapprop;

// expand featuremap to include stockprop so we can find out the population size 
$featuremap = chado_expand_var($featuremap, 'table', 'featuremap_stock');
$featuremap = chado_expand_var($featuremap, 'table', 'stockprop', array('return_array' => TRUE));
$stockprop = NULL;
$maternal = NULL;
$paternal = NULL;
$pop_size = NULL;

// Display only the first paternts and population size IF the map has multiple
if (is_array($featuremap->featuremap_stock)) {
  $featuremap->featuremap_stock = $featuremap->featuremap_stock [0];
}
$stockprop = $featuremap->featuremap_stock && property_exists($featuremap->featuremap_stock, 'stock_id') &&
property_exists($featuremap->featuremap_stock->stock_id, 'stockprop') ?
$featuremap->featuremap_stock->stock_id->stockprop :
NULL;
  
if ($stockprop) {
  foreach ($stockprop AS $prop) {
    if (property_exists($prop, 'type_id') && $prop->type_id->name == 'population_size') {
      $pop_size = $prop->value;
    }
  }
}
  
// expand featuremap to include stock parents
$featuremap = chado_expand_var($featuremap, 'table', 'stock_relationship', array('return_array' => TRUE));
$parents = property_exists($featuremap, 'featuremap_stock') && $featuremap->featuremap_stock &&
property_exists($featuremap->featuremap_stock, 'stock_id') &&
property_exists($featuremap->featuremap_stock->stock_id, 'stock_relationship') ?
$featuremap->featuremap_stock->stock_id->stock_relationship->object_id :
NULL;
  
if ($parents) {
  if (is_object($parents)) {
    $arr = array($parents);
    $parents = $arr;
  };
  foreach($parents AS $parent) {
    if ($parent->type_id->name == 'is_a_maternal_parent_of') {
      $maternal = $parent->subject_id;
    } else if ($parent->type_id->name == 'is_a_paternal_parent_of') {
      $paternal = $parent->subject_id;
    }
  }
}


// expand featuremap to include contacts
$featuremap = chado_expand_var($featuremap, 'table', 'featuremap_contact', array('return_array' => TRUE));
$contacts = $featuremap->featuremap_contact;

$headers = array();
$rows = array();
$display_name = $featuremap->cmap_url ? $featuremap->name . " [<a href=\"" . $featuremap->cmap_url . "&ref_map_accs=-1\" target=\"_blank\">View in CMap</a>]" : $featuremap->name;
$rows [] = array(array('data' => 'Name', 'header' => TRUE, 'width' => '20%'), $display_name);
$rows [] = array(array('data' => 'Species', 'header' => TRUE, 'width' => '20%'), $display_organism);
foreach ($featuremapprop AS $prop) {
  $rows [] = array(array('data' => str_replace("_", " ", ucfirst($prop->type_id->name)), 'header' => TRUE, 'width' => '20%'), $prop->value);
}
$rows [] = array(array('data' => 'Map unit', 'header' => TRUE, 'width' => '20%'), $featuremap->unittype_id->name);
// Print Maternal parents
if ($maternal) {
  $mlink = mainlab_tripal_link_record('stock', $maternal->stock_id);
  if ($mlink) {
    $rows [] = array(array('data' => 'Maternal parent', 'header' => TRUE, 'width' => '20%'), "<a href=\"$mlink\">". $maternal->uniquename . "</a>");
  }
  else {
    $rows [] = array(array('data' => 'Maternal parent', 'header' => TRUE, 'width' => '20%'), $maternal->uniquename);
  }
}

// Print Paternal parents
if ($paternal){
  $plink = mainlab_tripal_link_record('stock', $paternal->stock_id);
  if ($plink) {
    $rows [] = array(array('data' => 'Paternal parent', 'header' => TRUE, 'width' => '20%'), "<a href=\"$plink\">". $paternal->uniquename . "</a>");
  }
  else {
    $rows [] = array(array('data' => 'Paternal parent', 'header' => TRUE, 'width' => '20%'), $paternal->uniquename);
  }
}

// Print Population size
if ($pop_size) {
  $rows [] = array(array('data' => 'Population size', 'header' => TRUE, 'width' => '20%'), $pop_size);
}

// Print # of Loci
$num_loci = $featuremap->num_loci;
if ($num_loci){
  $rows [] = array(array('data' => 'Number of loci', 'header' => TRUE, 'width' => '20%'), $num_loci);
}

// Print # of Linkage group
$num_lg = $featuremap->num_lg;
if ($num_lg){
  $rows [] = array(array('data' => 'Number of linkage groups', 'header' => TRUE, 'width' => '20%'), $num_lg);
}

// Print Publications
$featuremap = chado_expand_var($featuremap,'table','featuremap_pub', array('return_array' => TRUE));
$pubs = $featuremap->featuremap_pub;
$rows [] = array(array('data' => 'Publication', 'header' => TRUE, 'width' => '20%'), $pubs ? "[<a href=\"?pane=publications\">view all</a>]" : "N/A");

// Print Contact
$data_contact = "";
if (is_array($contacts)) {
  foreach ($contacts AS $contact) {
    $data_contact .= "<a href=\"?pane=contact\">" . $contact->contact_id->name . "</a><br>";
  }
}
else {
  if ($contacts) {
    $data_contact = "<a href=\"?pane=contact\">" . $contacts->contact_id->name . "</a><br>";
  }
  else {
    $data_contact = "N/A";
  }
}
$rows [] = array(array('data' => 'Contact', 'header' => TRUE, 'width' => '20%'), $data_contact);

// allow site admins to see the feature ID
if (user_access('view ids')) {
  $rows[] = array(array('data' => 'Featuremap ID', 'header' => TRUE, 'class' => 'tripal-site-admin-only-table-row'), array('data' => $featuremap->featuremap_id, 'class' => 'tripal-site-admin-only-table-row'));
}
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_featuremap-table-custom_base',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);

?>

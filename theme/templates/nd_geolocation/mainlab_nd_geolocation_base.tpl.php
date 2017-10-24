<?php
$node = $variables['node'];
// get the feature details from chado
$nd_geolocation = $node->nd_geolocation;
$projects = $nd_geolocation->projects;

$name = $nd_geolocation->description;
$latitude = $nd_geolocation->latitude? $nd_geolocation->latitude : "N/A";
$longitude = $nd_geolocation->longitude? $nd_geolocation->longitude : "N/A";
$altitude = $nd_geolocation->altitude? $nd_geolocation->altitude : "N/A";

// Properties
$properties = array();
$properties['site_code'] ="N/A";
$properties['country'] ="N/A";
$properties['state'] ="N/A";
$properties['region'] ="N/A";
$properties['address'] = "N/A";
$properties['type'] ="N/A";
$properties['data_year'] ="N/A";
$properties['experimental_design'] ="N/A";
$properties['replications'] ="N/A";
$properties['plants_per_replication'] ="N/A";
$properties['plant_date'] ="N/A";
$properties['plot_distance'] = "N/A";
$properties['row_distance'] ="N/A";
$properties['plant_distance'] = "N/A";
$properties['irrigation'] ="N/A";
$properties['soil_type'] ="N/A";
$properties['pest_control'] ="N/A";
$properties['agronomic_control'] ="N/A";
$properties['pick_date'] = "N/A";
$properties['evaluation_date'] ="N/A";
$properties['comments'] ="N/A";

$nd_geolocationprops = $nd_geolocation->nd_geolocationprop;

foreach ($nd_geolocationprops as $property) {
    $property = chado_expand_var($property,'field','nd_geolocationprop.value');
    $ptype = $property->type_id->name;
    $pvalue = $property->value;
    $properties[$ptype] = $pvalue;
}
$properties['site_code'] = str_replace('COTTONDB_', '', $properties['site_code']); // remove the 'COTTONDB_' prefix for site_code

// Associated Dataset
$display_proj = "<a href=\"?pane=associated_dataset\">";
if (count($projects) == 1) {
  $display_proj .=$projects[0]->name;  
} else if (count($projects) > 1){
  $display_proj .= "show all " . count($projects);
}
$display_proj .= "</a>";
if (count($projects) == 0) {
  $display_proj = "N/A";
}

$headers = array();
$rows = array();
if ($properties['site_code'] != 'N/A') {$rows [] = array(array('data' => 'Environment Code', 'header' => TRUE, 'width' => '25%'), $properties['site_code']);}
if ($name != 'N/A') {$rows [] = array(array('data' => 'Environment Name', 'header' => TRUE, 'width' => '25%'), $name);}
if ($display_proj != 'N/A') {$rows [] = array(array('data' => 'Associated Dataset', 'header' => TRUE, 'width' => '25%'), $display_proj);}
if ($latitude != 'N/A') {$rows [] = array(array('data' => 'Latitude', 'header' => TRUE, 'width' => '25%'), $latitude);}
if ($longitude != 'N/A') {$rows [] = array(array('data' => 'Longitude', 'header' => TRUE, 'width' => '25%'), $longitude);}
if ($altitude != 'N/A') {$rows [] = array(array('data' => 'Altitude (m)', 'header' => TRUE, 'width' => '25%'), $altitude);}
if ($properties['country'] != 'N/A') {$rows [] = array(array('data' => 'Country', 'header' => TRUE, 'width' => '25%'), $properties['country']);}
if ($properties['state'] != 'N/A') {$rows [] = array(array('data' => 'State', 'header' => TRUE, 'width' => '25%'), $properties['state']);}
if ($properties['region'] != 'N/A') {$rows [] = array(array('data' => 'Region', 'header' => TRUE, 'width' => '25%'), $properties['region']);}
if ($properties['address'] != 'N/A') {$rows [] = array(array('data' => 'Address', 'header' => TRUE, 'width' => '25%'), $properties['address']);}
if ($properties['type'] != 'N/A') {$rows [] = array(array('data' => 'Type', 'header' => TRUE, 'width' => '25%'), $properties['type']);}
if ($properties['data_year'] != 'N/A') {$rows [] = array(array('data' => 'Data Year', 'header' => TRUE, 'width' => '25%'), $properties['data_year']);}
if ($properties['experimental_design'] != 'N/A') {$rows [] = array(array('data' => 'Experimental Design', 'header' => TRUE, 'width' => '25%'), $properties['experimental_design']);}
if ($properties['replications'] != 'N/A') {$rows [] = array(array('data' => 'Replications', 'header' => TRUE, 'width' => '25%'), $properties['replications']);}
if ($properties['plants_per_replication'] != 'N/A') {$rows [] = array(array('data' => 'Plants per Replication', 'header' => TRUE, 'width' => '25%'), $properties['plants_per_replication']);}
if ($properties['plant_date'] != 'N/A') {$rows [] = array(array('data' => 'Plant Date', 'header' => TRUE, 'width' => '25%'), $properties['plant_date']);}
if ($properties['plot_distance'] != 'N/A') {$rows [] = array(array('data' => 'Plot Distance', 'header' => TRUE, 'width' => '25%'), $properties['plot_distance']);}
if ($properties['row_distance'] != 'N/A') {$rows [] = array(array('data' => 'Row Distance', 'header' => TRUE, 'width' => '25%'), $properties['row_distance']);}
if ($properties['plant_distance'] != 'N/A') {$rows [] = array(array('data' => 'Plant Distance', 'header' => TRUE, 'width' => '25%'), $properties['plant_distance']);}
if ($properties['irrigation'] != 'N/A') {$rows [] = array(array('data' => 'Irrigation', 'header' => TRUE, 'width' => '25%'), $properties['irrigation']);}
if ($properties['soil_type'] != 'N/A') {$rows [] = array(array('data' => 'Soil Type', 'header' => TRUE, 'width' => '25%'), $properties['soil_type']);}
if ($properties['pest_control'] != 'N/A') {$rows [] = array(array('data' => 'Pest Control', 'header' => TRUE, 'width' => '25%'), $properties['pest_control']);}
if ($properties['agronomic_control'] != 'N/A') {$rows [] = array(array('data' => 'Agronomic Control', 'header' => TRUE, 'width' => '25%'), $properties['agronomic_control']);}
if ($properties['pick_date'] != 'N/A') {$rows [] = array(array('data' => 'Pick Date', 'header' => TRUE, 'width' => '25%'), $properties['pick_date']);}
if ($properties['evaluation_date'] != 'N/A') {$rows [] = array(array('data' => 'Evaluation Date', 'header' => TRUE, 'width' => '25%'), $properties['evaluation_date']);}
if ($properties['comments'] != 'N/A') {$rows [] = array(array('data' => 'Comments', 'header' => TRUE, 'width' => '25%'), $properties['comments']);}

$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_nd_geolocation-table-base',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);
?>
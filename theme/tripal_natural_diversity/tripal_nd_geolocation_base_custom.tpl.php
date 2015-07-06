<?php
$node = $variables['node'];
$nd_geolocation = $variables['node']->nd_geolocation;
// expand the nd_geolocation to include the properties.
$nd_geolocation = chado_expand_var($nd_geolocation,'table', 'nd_geolocationprop', array('return_array' => 1));

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
$projects = $nd_geolocation->projects;
$display_proj = "<a href=\"#\" onClick=\"$('.tripal-info-box').hide();$('#tripal_nd_geolocation-associated_dataset-box').fadeIn('slow');$('.tripal_toc').height($('#tripal_nd_geolocation-associated_dataset-box').parent().height());return false;\">";
if (count($projects) == 1) {
  $display_proj .=$projects[0]->name;  
} else if (count($projects) > 1){
  $display_proj .= "show all " . count($projects);
}
$display_proj .= "</a>";
if (count($projects) == 0) {
  $display_proj = "N/A";
}
?>
<div id="tripal_nd_geolocation-base-box" class="tripal_nd_geolocation-info-box tripal-info-box">
  <div class="tripal_nd_geolocation-info-box-title tripal-info-box-title">Environment Details</div>
  <div class="tripal_nd_geolocation-info-box-desc tripal-info-box-desc"></div>   

  <table id="tripal_nd_geolocation-table-base" class="tripal_nd_geolocation-table tripal-table tripal-table-vert">
    <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th width=40%>Environment Code</th>
      <td><?php print $properties['site_code']; ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Environment Name</th>
      <td><?php print $name ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Associated Dataset</th>
      <td><?php print $display_proj?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Latitude</th>
      <td><?php print $latitude?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Longitude</th>
      <td><?php print $longitude ?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Altitude (m)</th>
      <td><?php print $altitude ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Country</th>
      <td><?php print $properties['country']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>State</th>
      <td><?php print $properties['state']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Region</th>
      <td><?php print $properties['region']?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Address</th>
      <td><?php print $properties['address'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Type</th>
      <td><?php print $properties['type']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Data Year</th>
      <td><?php print $properties['data_year']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Experimental Design</th>
      <td><?php print $properties['experimental_design']?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Replications</th>
      <td><?php print $properties['replications'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Plants per Replication</th>
      <td><?php print $properties['plants_per_replication']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Plant Date</th>
      <td><?php print $properties['plant_date']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Plot Distance</th>
      <td><?php print $properties['plot_distance']?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Row Distance</th>
      <td><?php print $properties['row_distance'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Plant Distance</th>
      <td><?php print $properties['plant_distance']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Irrigation</th>
      <td><?php print $properties['irrigation']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Soil Type</th>
      <td><?php print $properties['soil_type']?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Pest Control</th>
      <td><?php print $properties['pest_control'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Agronomic Control</th>
      <td><?php print $properties['agronomic_control']?></td>
    </tr>
     <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Pick Date</th>
      <td><?php print $properties['pick_date'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row tripal-table-even-row">
      <th>Evaluation Date</th>
      <td><?php print $properties['evaluation_date']?></td>
    </tr>
         <tr class="tripal_nd_geolocation-table-odd-row tripal-table-odd-row">
      <th>Comments</th>
      <td><?php print $properties['comments'] ?></td>
    </tr>
  </table> 
</div>

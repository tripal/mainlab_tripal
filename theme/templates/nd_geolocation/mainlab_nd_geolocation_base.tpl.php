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

  <table id="tripal_nd_geolocation-table-base" class="tripal_nd_geolocation-table tripal-table tripal-contents-table">
    <tr class="tripal_nd_geolocation-table-even-row even">
      <th width=40%>Environment Code</th>
      <td><?php print $properties['site_code']; ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Environment Name</th>
      <td><?php print $name ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Associated Dataset</th>
      <td><?php print $display_proj?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Latitude</th>
      <td><?php print $latitude?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Longitude</th>
      <td><?php print $longitude ?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Altitude (m)</th>
      <td><?php print $altitude ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Country</th>
      <td><?php print $properties['country']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>State</th>
      <td><?php print $properties['state']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Region</th>
      <td><?php print $properties['region']?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Address</th>
      <td><?php print $properties['address'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Type</th>
      <td><?php print $properties['type']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Data Year</th>
      <td><?php print $properties['data_year']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Experimental Design</th>
      <td><?php print $properties['experimental_design']?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Replications</th>
      <td><?php print $properties['replications'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Plants per Replication</th>
      <td><?php print $properties['plants_per_replication']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Plant Date</th>
      <td><?php print $properties['plant_date']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Plot Distance</th>
      <td><?php print $properties['plot_distance']?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Row Distance</th>
      <td><?php print $properties['row_distance'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Plant Distance</th>
      <td><?php print $properties['plant_distance']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Irrigation</th>
      <td><?php print $properties['irrigation']?></td>
    </tr>
        <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Soil Type</th>
      <td><?php print $properties['soil_type']?></td>
    </tr>
 <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Pest Control</th>
      <td><?php print $properties['pest_control'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Agronomic Control</th>
      <td><?php print $properties['agronomic_control']?></td>
    </tr>
     <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Pick Date</th>
      <td><?php print $properties['pick_date'] ?></td>
    </tr>
    <tr class="tripal_nd_geolocation-table-even-row even">
      <th>Evaluation Date</th>
      <td><?php print $properties['evaluation_date']?></td>
    </tr>
         <tr class="tripal_nd_geolocation-table-odd-row odd">
      <th>Comments</th>
      <td><?php print $properties['comments'] ?></td>
    </tr>
  </table> 
</div>

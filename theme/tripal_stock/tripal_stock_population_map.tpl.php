<?php
$stock = $variables['node']->stock;
$population_map = $stock->population_map;
$num_population_map = count($population_map);
if ($num_population_map > 0) {
?>

<script type="text/javascript">
// Insert Trait Score value to the base template
$('#tripal_stock-table-population_map_value').html("[<a href='#' id='tripal_stock-table-population_map_value-link'>view all <?php print $num_population_map;?></a>]");
$('#tripal_stock-table-population_map_value-link').click(function() {
	$('.tripal-info-box').hide();
	$('#tripal_stock-population_map-box').fadeIn('slow');
	$('#tripal_stock_toc').height($('#tripal_stock-population_map-box').parent().height());
})
</script>

  <div id="tripal_stock-population_map-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">Map</div>
    <table id="tripal_stock-population_map-table" class="tripal_stock-table tripal-table tripal-table-horz" style="margin-bottom:20px;">
             <tr>
               <th>Name</th>
               <th>Type</th>
               <th>PopulationType</th>
               <th>Genome Group</th>
             </tr>
    <?php
      $counter = 0;
      $class = "";
      foreach ($population_map as $map){
         if ($counter % 2 == 0) {
            $class = "tripal_stock-table-even-row tripal-table-even-row";
         } else {
            $class = "tripal_stock-table-odd-row tripal-table-odd-row";
         }
         print "<tr class=\"$class\"><td><a href=\"/node/$map->nid\">$map->name</a></td><td>$map->mtype</td><td>$map->ptype</td><td>$map->ggroup</td></tr>";
         $counter ++;
      }
    ?>
    </table>
  </div>
 <?php
}

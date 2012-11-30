<?php
$feature = $variables['node']->feature;
$map_positions = $feature->map_positions;
$counter_pos = count($map_positions);
 ?>
 
<?php 
// Load the table pager javascript code as we'll need it after the allele table is created.
drupal_add_js(drupal_get_path('module', 'mainlab_tripal') . "/theme/mainlab/js/mainlab_table_pager.js");
?>

<?php if ($counter_pos > 0) { ?>
    <div id="tripal_feature-genetic_marker_map_positions-box" class="tripal_feature-info-box tripal-info-box">
    <div class="tripal_feature-info-box-title tripal-info-box-title">Map Positions</div>
      <div class="tripal_feature-info-box-desc tripal-info-box-desc">Marker '<?php print $node->feature->name ?>' includes:</div>

      <script type="text/javascript">
         // Insert Marker position count to the base template
         $('#tripal-feature-genetic_marker-map_position').html("[<a href='#' id='tripal-feature-genetic_marker-map_position-link'>view all <?php print $counter_pos;?></a>]");
         $('#tripal-feature-genetic_marker-map_position-link').click(function() {
	         $('.tripal-info-box').hide();
	         $('#tripal_feature-genetic_marker_map_positions-box').fadeIn('slow');
	         $('#tripal_feature_toc').height($('#tripal_feature-genetic_marker_map_positions-box').parent().height());
         })
      </script>
      
       <!-- Map positions -->
     	<?php 
     	  $counter_pos = count($map_positions);
     	  if ($counter_pos > 0) {
     	    print "Total $counter_pos map positions";
     	    print "<div id=\"cottongen-genetic_marker-mappositions\">
      					<table id=\"cottongen-genetic_marker-mappositions-table\"class=\"tripal_feature-table tripal-table tripal-table-horz\" style=\"margin-top:15px;margin-bottom:15px;border-bottom:2px solid #999999;;border-top:2px solid #999999\">
      						<tr><th style=\"width:20px;\">#</th><th>Map Name</th><th>Linkage Group</th><th>Bin</th><th>Chromosome</th><th>Position</th><th>Locus</th><th>CMap</th></tr>";
     	    $counter = 1;
     	    $bin = $pos->bin;
     	    if (!$bin) {$bin = "N/A";}
     	    foreach($map_positions AS $pos) {
               $class = genetic_markerGetTableRowClass($counter);
            	print "<tr class=\"$class\">
            	              <td>$counter</td>
            	              <td><a href=\"/node/$pos->nid\">$pos->name</a></td>
            	              <td>$pos->linkage_group</td>
            	              <td>$bin</td>
            	              <td>$pos->chr</td>
            	              <td>$pos->locus_start</td>
            	              <td>$pos->locus_name</td>
            	              <td><a href=\"$pos->urlprefix$pos->accession\">View</a></td>
            	           </tr>";
            	$counter ++;
          }
     	  	print "	 </table>
      				   </div>";
        }
     	?>
     </div>
    <?php } ?>
    
<script type="text/javascript">
// Create a pager for the marker position
tripal_table_make_pager ('cottongen-genetic_marker-mappositions-table', 0, 15);
// Adjust hieght of two columns whenever the page changes
$('#cottongen-genetic_marker-mappositions-table-pager').click(function () {
  $("#tripal_feature_toc").height($("#tripal_feature-genetic_marker_map_positions-box").parent().height());
});
</script>

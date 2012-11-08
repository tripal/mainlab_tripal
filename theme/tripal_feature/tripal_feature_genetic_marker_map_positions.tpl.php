<?php
$feature = $variables['node']->feature;
$map_positions = $feature->map_positions;
$counter_pos = count($map_positions);
 ?>
 
<script type="text/javascript">
/* 
 * Define javascript funtions that will be called later
 */
// A function to create pager for an HTML  table
function tripal_table_make_pager(table_id, page, rowsPerPage) {
	var table = document.getElementById(table_id);
  	var trs = table.getElementsByTagName("tr");
	if (trs.length > rowsPerPage) {
		var noRows = 0;
		// Count the number of rows <td> (not including header <th>)
		for (var i = 0; i < trs.length; i ++) {
	   		if (trs[i].getElementsByTagName('th')[0] != null) {
	   		} else {
		    	   noRows ++;	
	   		}
   		}
		var addPage = noRows % rowsPerPage == 0 ? 0 : 1;
		var noPages = parseInt (noRows / rowsPerPage + addPage);		
		var counter = 0;
		for (var i = 0; i < trs.length; i ++) {
		   // Header
	   		if (trs[i].getElementsByTagName('th')[0] != null) {
		   // Rows
	   		} else {
		   		var belongsToPage = parseInt(counter / rowsPerPage);
				if (noPages == page || belongsToPage == page) {
					$(trs[i]).show();
				} else {
					$(trs[i]).hide();
				}
		   		counter ++;	
	   		}
   		}
   		// Pager
   		var pager_id = table_id + "-pager";
   		var pager = document.getElementById(pager_id);
   		if (!pager && noPages > 1) {
   			var pager = document.createElement('div');
   			pager.id = pager_id;
   			var select = "<i>Page</i> <select onChange=\"tripal_table_make_pager('" + table_id + "', this.selectedIndex," + rowsPerPage + ");\">";
   			for (var i = 0; i < noPages; i ++) {
				select += "<option>" + (i +1) + "</option>";
   			}
   			select += "<option>All</option>";
   			select += "</select>";
   			pager.innerHTML = select;
   			pager.style.textAlign = "right";
   			$(table).after(pager);
   		}
   		// Adjust hieght of two columns
   	 $("#tripal_feature_toc").height($("#tripal_feature-genetic_marker_map_positions-box").parent().height());
	}
}
// A function to add the Map Position to the genetic map base table
function addMapPositionToBaseTable () {
   var link = document.createElement('a');
   link.href = "#";
   if (<?php print $counter_pos;?> == 1) {
        link.innerHTML = "<?php print $map_positions[0]->locus_name;?>";
   } else {
      link.innerHTML = "[Total <?php print $counter_pos;?> map positions]";
   }
   link.onclick = function () {
      $(".tripal-info-box").hide();
      $("#tripal_feature-genetic_marker_map_positions-box").fadeIn('slow');
	   $("#tripal_feature_toc").height($("#tripal_feature-genetic_marker_map_positions-box").parent().height());
	   return false;
	};
	$('#tripal-feature-genetic_marker-map_position').html("");
	$('#tripal-feature-genetic_marker-map_position').append(link);
}
</script>

<?php if ($counter_pos > 0) { ?>
    <div id="tripal_feature-genetic_marker_map_positions-box" class="tripal_feature-info-box tripal-info-box">
    <div class="tripal_feature-info-box-title tripal-info-box-title">Map Positions</div>
      <div class="tripal_feature-info-box-desc tripal-info-box-desc">Marker '<?php print $node->feature->name ?>' include:</div>
      <script type="text/javascript">
         addMapPositionToBaseTable();
      </script>
       <!-- Map positions -->
     	<?php 
     	  $counter_pos = count($map_positions);
     	  if ($counter_pos > 0) {
     	    print "Total $counter_pos map positions";
     	    print "<div id=\"cottongen-genetic_marker-mappositions\">
      					<table id=\"cottongen-genetic_marker-mappositions-table\"class=\"tripal_feature-table tripal-table tripal-table-horz\" style=\"margin-top:15px;margin-bottom:15px;border-bottom:2px solid #999999;;border-top:2px solid #999999\">
      						<tr><th style=\"width:20px;\">#</th><th>Locus</th><th>Genome</th><th>Linkage Group</th><th>Map</th><th>Position</th></tr>";
     	    $counter = 1;
     	    foreach($map_positions AS $pos) {
               $class = genetic_markerGetTableRowClass($counter);
            	print "<tr class=\"$class\"><td>$counter</td><td>$pos->locus_name</td><td>$pos->genome</td><td>$pos->linkage_group</td><td><a href=\"/node/$pos->nid\">$pos->name</a></td><td>$pos->locus_start</td></tr>";
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
tripal_table_make_pager ('cottongen-genetic_marker-mappositions-table', 0, 10);
</script>

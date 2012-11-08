<?php
$feature = $variables['node']->feature;
$polymorphism = $feature->feature_genotype->feature_id;
$counter_poly = count($polymorphism);
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
   	 $("#tripal_feature_toc").height($("#tripal_feature-genetic_marker_polymorphism-box").parent().height());
	}
}
</script>

<?php if ($counter_poly > 0) { ?>
    <div id="tripal_feature-genetic_marker_polymorphism-box" class="tripal_feature-info-box tripal-info-box">
    <div class="tripal_feature-info-box-title tripal-info-box-title">Polymorphism</div>
      <div class="tripal_feature-info-box-desc tripal-info-box-desc">Marker '<?php print $node->feature->name ?>' include:</div>
       <!-- Polymorphism -->
     	<?php 
     	  $counter_poly = count($polymorphism);
     	  if ($counter_poly > 0) {
     	    print "Total $counter_poly polymorphism";
     	    print "<div id=\"cottongen-genetic_marker-polymorphism\">
      					<table id=\"cottongen-genetic_marker-polymorphism-table\"class=\"tripal_feature-table tripal-table tripal-table-horz\" style=\"margin-top:15px;margin-bottom:15px;border-bottom:2px solid #999999;;border-top:2px solid #999999\">
      						<tr><th style=\"width:20px;\">#</th><th style=\"width:160px;\">Name</th><th>Description</th></tr>";
     	    $counter = 1;
     	    foreach($polymorphism AS $poly) {
               $class = genetic_markerGetTableRowClass($counter);
            	print "<tr class=\"$class\"><td>$counter</td><td>" . $poly->genotype_id->uniquename . "</td><td>" . $poly->genotype_id->description . "</td></tr>";
            	$counter ++;
          }
     	  	print "	 </table>
      				   </div>";
        }
     	?>
     </div>
    <?php } ?>
    
<script type="text/javascript">
// Create a pager for the polymorphism
tripal_table_make_pager ('cottongen-genetic_marker-polymorphism-table', 0, 10);
</script>

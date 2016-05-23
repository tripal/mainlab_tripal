<?php
$polymorphism = $variables['node'];
$counter_poly = count($polymorphism);
?>

<?php 
// Load the table pager javascript code as we'll need it after the allele table is created.
drupal_add_js(drupal_get_path('module', 'mainlab_tripal') . "/theme/js/mainlab_table_pager.js");
?>

<?php if ($counter_poly > 0) { 
               $keys = array_keys($polymorphism);
               $first_key = $keys[0];
	?>
    <div id="mainlab_polymorphism-box" class="tripal_details_full">
      <div class="tripal_feature-info-box-desc tripal-info-box-desc">Marker <?php print "<a href=\"/node/" . $polymorphism[$first_key]->marker_nid . "\">";print $polymorphism[$first_key]->marker_name . "</a>"?> includes:</div>
       <!-- Polymorphism -->
     	<?php 
     	    print "Total $counter_poly polymorphisms. <p><i>Note: </i>click on allele name to see all stocks.</p>";
     	    print "<div id=\"mainlab-genetic_marker-polymorphism\">
      					<table id=\"mainlab-genetic_marker-polymorphism-table\"class=\"tripal_feature-table tripal-table tripal-table-horz\" style=\"margin-top:15px;margin-bottom:15px;\">
      						<tr><th style=\"width:20px;\">#</th><th style=\"width:160px;\">Marker_Allele</th><th>Allele</th><th>Germplasm</th><th>Dataset</th></tr>";
     	    $counter = 1;
     	    foreach($polymorphism AS $poly) {
              $class = NULL;
             if ($counter % 2 == 0) {
	              $class = "tripal_feature-table-even-row even";
             } else {
	              $class = "tripal_feature-table-odd-row odd";
             }
             $name = explode("_", $poly->uniquename);
             // If allele = '+', it needs to be encoded
             $allele = urlencode($name [count($name) - 1]);
             $allelepage = "/allele/$poly->marker_name/$allele/$poly->marker_oid";
             print "<tr class=\"$class\">
                           <td>$counter</td>
                           <td><a href=\"$allelepage\">" . $poly->uniquename . "</a></td>
   				            <td>$poly->description</td>
							      <td><a href=\"/node/" . $poly->first_stock_nid . "\">" . $poly->first_stock_name . "</a>";                           
							       if ($poly->num_stocks > 1) {print " <a href=\"$allelepage\">[show all ". $poly->num_stocks . "] </a>";}
             print  	"</td>
						         <td>" . $poly->project . "</td>
							    </tr>";
             $counter ++;
          }
     	  	print "	 </table>
      				   </div>";
        
     	?>
     </div>
    <?php } ?>
    
<script type="text/javascript">
// Create a pager for the polymorphism
tripal_table_make_pager ('mainlab-genetic_marker-polymorphism-table', 0, 15);
</script>

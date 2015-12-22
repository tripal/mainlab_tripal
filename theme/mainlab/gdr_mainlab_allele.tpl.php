<?php
$allele = $variables['node'];
$stocks = $allele->stock;
$count_stock = count($stocks);
?>

<?php 
// Load the table pager javascript code as we'll need it after the allele table is created.
drupal_add_js(drupal_get_path('module', 'mainlab_tripal') . "/theme/mainlab/js/mainlab_table_pager.js");
?>

<?php if ($count_stock > 0) { ?>
    <div id="mainlab_allele-box" class="tripal_details_full">
      <div class="tripal_feature-info-box-desc tripal-info-box-desc">
        List of germplasm that has been detected to have allele <strong><?php print $allele->id;?></strong>
       using marker <strong><?php print $allele->name;?></strong></div>
       <!-- Stocks -->
     	<?php 
     	    print "Total $count_stock germplasms.";
     	    print "<div id=\"mainlab-allele\">
      					<table id=\"mainlab-allele-table\"class=\"tripal_feature-table tripal-table tripal-table-horz\" style=\"margin-top:15px;margin-bottom:15px;border-bottom:2px solid #999999;;border-top:2px solid #999999\">
      						<tr><th>#</th><th>Germplasm</th><th>Diversity Data</th><th>Dataset</th></tr>";
     	    $counter = 1;
     	    foreach($stocks AS $stock) {
              $class = NULL;
             if ($counter % 2 == 1) {
	              $class = "tripal_feature-table-even-row even";
             } else {
	              $class = "tripal_feature-table-odd-row odd";
             }
            $diversity_data = "<a href=\"/node/$stock->nid/?block=genotypic_data&\">$stock->uniquename</a>";
             print "<tr class=\"$class\">
                           <td style=\"padding-left:10px;\">$counter</td>
                           <td style=\"padding-left:10px;\"><a href=\"/node/$stock->nid\">" . $stock->uniquename . "</a></td>
						         <td style=\"padding-left:10px;\">$diversity_data</td>
						         <td style=\"padding-left:10px;\">$stock->project</td>
							   </tr>";
             $counter ++;
          }
     	  	print "	 </table>
      				   </div>";
        
     	?>
     </div>
    <?php } ?>
    
<script type="text/javascript">
// Create a pager for the allele table
tripal_table_make_pager ('mainlab-allele-table', 0, 15);
</script>
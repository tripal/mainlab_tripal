<?php
$stock = $variables['node']->stock;
$phenotypic_data = $stock->phenotypic_data;
$num_phenotypic_data = count($phenotypic_data);
if ($num_phenotypic_data > 0) {
?>

<?php 
// Load the table pager javascript code as we'll need it after the allele table is created.
drupal_add_js(drupal_get_path('module', 'mainlab_tripal') . "/theme/mainlab/js/mainlab_table_pager.js");
?>

<script type="text/javascript">
// Insert Trait Score value to the base template
$('#tripal_stock-table-phenotypic_data_value').html("[<a href='#' id='tripal_stock-table-phenotypic_data_value-link'>view all <?php print $num_phenotypic_data;?></a>]");
$('#tripal_stock-table-phenotypic_data_value-link').click(function() {
	$('.tripal-info-box').hide();
	$('#tripal_stock-phenotypic_data-box').fadeIn('slow');
	$('#tripal_stock_toc').height($('#tripal_stock-phenotypic_data-box').parent().height());
})
</script>

  <div id="tripal_stock-phenotypic_data-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">Phenotypic Data</div>
    Total <?php print $num_phenotypic_data;?> trait scores</br></br>
    <table id="tripal_stock-phenotypic_data-table" class="tripal_stock-table tripal-table tripal-table-horz" style="margin-bottom:20px;">
             <tr>
               <th>#</th>
               <th>Dataset</th>
               <th>Descriptor</th>
               <th>Value</th>
               <th>Environment</th>
               <th>Replication</th>
             </tr>
    <?php
      $counter = 0;
      $class = "";
      foreach ($phenotypic_data as $score){
         if ($counter % 2 == 0) {
            $class = "tripal_stock-table-even-row tripal-table-even-row";
         } else {
            $class = "tripal_stock-table-odd-row tripal-table-odd-row";
         }
         $descriptors = explode("_", $score->uniquename);
         $descriptor = $descriptors[count($descriptors) - 2];
         $env = str_replace("COTTONDB_", "", $score->environment);
         print "<tr class=\"$class\"><td>". ($counter + 1) . "</td><td>$score->project</td><td>$descriptor</td><td>$score->value</td><td>$env</td><td>$score->replications</td></tr>";
         $counter ++;
      }
    ?>
    </table>
  </div>
<script type="text/javascript">
// Create a pager for the allele table
tripal_table_make_pager ('tripal_stock-phenotypic_data-table', 0, 15);
//Adjust hieght of two columns whenever the page changes
$('#tripal_stock-phenotypic_data-table-pager').click(function () {
  $("#tripal_stock_toc").height($("#tripal_stock-phenotypic_data-box").parent().height());
});
</script>  
 <?php
}

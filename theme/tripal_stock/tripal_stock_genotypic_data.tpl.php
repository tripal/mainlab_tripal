<?php
$stock = $variables['node']->stock;
$genotypic_data = $stock->genotypic_data;
$num_genotypic_data = count($genotypic_data);
if ($num_genotypic_data > 0) {
?>

<?php 
// Load the table pager javascript code as we'll need it after the allele table is created.
drupal_add_js(drupal_get_path('module', 'mainlab_tripal') . "/theme/mainlab/js/mainlab_table_pager.js");
?>

<script type="text/javascript">
// Insert Genotypic data value to the base template
$('#tripal_stock-table-genotypic_data_value').html("[<a href='#' id='tripal_stock-table-genotypic_data_value-link'>view all <?php print $num_genotypic_data;?></a>]");
$('#tripal_stock-table-genotypic_data_value-link').click(function() {
	$('.tripal-info-box').hide();
	$('#tripal_stock-genotypic_data-box').fadeIn('slow');
	$('#tripal_stock_toc').height($('#tripal_stock-genotypic_data-box').parent().height());
})
</script>

  <div id="tripal_stock-genotypic_data-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">Genotypic Data</div>
      Total <?php print $num_genotypic_data;?> genotypic data</br></br>
    <table id="tripal_stock-genotypic_data-table" class="tripal_stock-table tripal-table tripal-table-horz" style="margin-bottom:20px;">
             <tr>
             <th>#</th>
               <th>Dataset</th>
               <th>Marker</th>
               <th>Genotype</th>
               <th>Marker_Allele</th>
             </tr>
    <?php
      $counter = 0;
      $class = "";
      foreach ($genotypic_data as $data){
         if ($counter % 2 == 0) {
            $class = "tripal_stock-table-even-row tripal-table-even-row";
         } else {
            $class = "tripal_stock-table-odd-row tripal-table-odd-row";
         }
         $descriptor = explode("_", $data->uniquename);
         $marker = $descriptor[0];
         for ($i = 1; $i < count($descriptor) - 1; $i ++) {
            $marker .= '_' . $descriptor[$i];
         }
         $gtype = $descriptor[count($descriptor) - 1];
         $alleles = explode("|", $gtype );
         $link_alleles = "";
         $index = 0;
         foreach($alleles AS $allele) {
            $link_alleles .= "<a href=\"/allele/$marker/$allele/$data->organism_id\">" .$marker ."_" . $allele . "</a>";
            if ($index < count($alleles) - 1) {
               $link_alleles .= "; ";
            }
            $index ++;
         }
         print "<tr class=\"$class\"><td>". ($counter + 1) . "</td><td>$data->project</td><td><a href=\"/node/$data->marker_nid\">$marker</a></td><td>$descriptor[1]</td><td>$link_alleles</td></tr>";
         $counter ++;
      }
    ?>
    </table>
  </div>
<script type="text/javascript">
// Create a pager for the allele table
tripal_table_make_pager ('tripal_stock-genotypic_data-table', 0, 15);
//Adjust hieght of two columns whenever the page changes
$('#tripal_stock-genotypic_data-table-pager').click(function () {
  $("#tripal_stock_toc").height($("#tripal_stock-genotypic_data-box").parent().height());
});
</script>  
 <?php
}

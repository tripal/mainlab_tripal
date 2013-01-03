<?php
$stock = $variables['node']->stock;
$trait_scores = $stock->trait_scores;
$num_trait_scores = count($trait_scores);
if ($num_trait_scores > 0) {
?>

<?php 
// Load the table pager javascript code as we'll need it after the allele table is created.
drupal_add_js(drupal_get_path('module', 'mainlab_tripal') . "/theme/mainlab/js/mainlab_table_pager.js");
?>

<script type="text/javascript">
// Insert Trait Score value to the base template
$('#tripal_stock-table-trait_scores_value').html("[<a href='#' id='tripal_stock-table-trait_scores_value-link'>view all <?php print $num_trait_scores;?></a>]");
$('#tripal_stock-table-trait_scores_value-link').click(function() {
	$('.tripal-info-box').hide();
	$('#tripal_stock-trait_scores-box').fadeIn('slow');
	$('#tripal_stock_toc').height($('#tripal_stock-trait_scores-box').parent().height());
})
</script>

  <div id="tripal_stock-trait_scores-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">Trait Score</div>
    Total <?php print $num_trait_scores;?> trait scores</br></br>
    <table id="tripal_stock-trait_scores-table" class="tripal_stock-table tripal-table tripal-table-horz" style="margin-bottom:20px;">
             <tr>
               <th>#</th>
               <th>Dataset</th>
               <th>Descriptor</th>
               <th>Value</th>
             </tr>
    <?php
      $counter = 0;
      $class = "";
      foreach ($trait_scores as $score){
         if ($counter % 2 == 0) {
            $class = "tripal_stock-table-even-row tripal-table-even-row";
         } else {
            $class = "tripal_stock-table-odd-row tripal-table-odd-row";
         }
         $descriptor = explode("_", $score->uniquename);
         print "<tr class=\"$class\"><td>". ($counter + 1) . "</td><td>$score->project</td><td>$descriptor[0]</td><td>$score->value</td></tr>";
         $counter ++;
      }
    ?>
    </table>
  </div>
<script type="text/javascript">
// Create a pager for the allele table
tripal_table_make_pager ('tripal_stock-trait_scores-table', 0, 15);
//Adjust hieght of two columns whenever the page changes
$('#tripal_stock-trait_scores-table-pager').click(function () {
  $("#tripal_stock_toc").height($("#tripal_stock-trait_scores-box").parent().height());
});
</script>  
 <?php
}

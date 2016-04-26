<?php
$stock = $variables['node']->stock;
$in_collection = $stock->in_collection;
$num_in_collection = count($in_collection);
if ($num_in_collection > 0) {
?>

<script type="text/javascript">
// Insert Trait Score value to the base template
$('#tripal_stock-table-in_collection_value').html("[<a href='#' id='tripal_stock-table-in_collection_value-link'>view all <?php print $num_in_collection;?></a>]");
$('#tripal_stock-table-in_collection_value-link').click(function() {
	$('.tripal-info-box').hide();
	$('#tripal_stock-in_collection-box').fadeIn('slow');
	$('#tripal_stock_toc').height($('#tripal_stock-in_collection-box').parent().height());
})
</script>

  <div id="tripal_stock-in_collection-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">In Collection</div>
    <table id="tripal_stock-in_collection-table" class="tripal_stock-table tripal-table tripal-table-horz" style="margin-bottom:20px;">
             <tr>
               <th>Name</th>
               <th>ID used in Collection</th>
               <th>External Database</th>
             </tr>
    <?php
      $counter = 0;
      $class = "";
      foreach ($in_collection as $coll){
         if ($counter % 2 == 0) {
            $class = "tripal_stock-table-even-row even";
         } else {
            $class = "tripal_stock-table-odd-row odd";
         }         
         $link = "";
         if ($coll->urlprefix) {
            $db = explode("_", $coll->db);
            $acc = $coll->accession;
            if ($coll->db == 'GRIN_COT' || $coll->db == 'GRIN_PVP') {
               $array = explode(" ", $coll->accession);
               $acc = $array[0];
               if (key_exists(1, $array)) {
	             $acc .= "+" . $array[1];
               }
            } else if ($coll->db == 'GRIN_Regist') {
               $array = explode("-", $coll->accession);
               $acc = $array[0] . " COTTON";
            }
            if ($coll->db == 'GRIN_COT') {
               $link = "<a href=\"". $coll->urlprefix . $acc ."\" target=_blank>" . $db[0] . ": " . $coll->version . "</a>";
            } else {
               $link = "<a href=\"". $coll->urlprefix . $acc ."\" target=_blank>" . $coll->version . "</a>";
            }
         } 
         print "<tr class=\"$class\"><td>$coll->collection</td><td>$coll->accession</td><td>$link</td></tr>";
         $counter ++;
      }
    ?>
    </table>
  </div>

 <?php
}

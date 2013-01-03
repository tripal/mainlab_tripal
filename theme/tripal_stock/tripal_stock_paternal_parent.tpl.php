<?php
$stock = $variables['node']->stock;
$paternal_parent = $stock->paternal_parent;
$num_mparent = count($paternal_parent);
if ($num_mparent > 0) {
	$first_mparent = $paternal_parent[0]->uniquename;
	if ($num_mparent > 1) {
		$first_mparent .= " [<a href=\"#\" id=\"tripal_stock-table-paternal_parent_value-link\">view all " . $num_mparent . "</a>]";
	}
?>
<script type="text/javascript">
// Insert paternal parent value to the base template
$('#tripal_stock-table-paternal_parent_value').html('<?php print $first_mparent;?>');
$('#tripal_stock-table-paternal_parent_value-link').click(function() {
	$('.tripal-info-box').hide();
	$('#tripal_stock-paternal_parent-box').fadeIn('slow');
	$('#tripal_stock_toc').height($('#tripal_stock-paternal_parent-box').parent().height());
})
</script>

  <div id="tripal_stock-paternal_parent-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">Paternal Parent of</div>
    <table id="tripal_stock-paternal_parent-table" class="tripal_stock-table tripal-table tripal-table-horz">
             <tr>
               <th>Germplasm Name</th>
               <th>Description</th>
               <th>Type</th>
             </tr>
    <?php
      $counter = 0;
      $class = "";
      foreach ($paternal_parent as $parent){
         if ($counter % 2 == 0) {
            $class = "tripal_stock-table-even-row tripal-table-even-row";
         } else {
            $class = "tripal_stock-table-odd-row tripal-table-odd-row";
         }
         print "<tr class=\"$class\"><td><a href=\"/node/$parent->nid\">$parent->uniquename</a></td><td>$parent->description</td><td>$parent->type</td></tr>";
         $counter ++;
      }
    ?>
    </table>
  </div> <?php
}

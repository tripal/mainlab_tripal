<?php
$featuremap  = $variables['node']->featuremap;

// expand featuremap to include stockprop so we can find out the population size 
$featuremap = tripal_core_expand_chado_vars($featuremap, 'table', 'featuremap_stock');
$stock = $featuremap->featuremap_stock->stock_id;
$stockprops = $stock->stockprop;

?>

<?php if ($stock) { ?>
<div id="tripal_featuremap-stock-box" class="tripal_featuremap-info-box tripal-info-box">
  <div class="tripal_featuremap-info-box-title tripal-info-box-title">Germplasm</div>
  <div class="tripal_featuremap-info-box-desc tripal-info-box-desc"></div>

   <table id="tripal_featuremap-stock-table" class="tripal_featuremap-table tripal-table tripal-table-vert" style="border-bottom:solid 2px #999999">
   <tr style="background-color:#EEEEFF;border-top:solid 2px #999999"><th style="padding:5px 10px 5px 10px;">Name</th><th>Type</th><th>Details</th></tr>
<?php
print "<tr class=\"" . featuremapGetTableRowClass(0) ."\">";
print "<td style=\"padding:5px 10px 5px 10px;\"><a href=\"/node/$stock->nid\">$stock->uniquename</a></td><td>" . $stock->type_id->name . "</td>";
print "<td><table class=\"tripal-subtable\">";
// print stock properties
foreach ($stockprops AS $prop) {
      print "<tr><td style=\"padding:2px 0px 2px 0px;width:110px;\">";
      print str_replace("_", " ", ucfirst($prop->type_id->name)) . "</td><td>:</td><td style=\"padding:2px 0px 2px 0px\">$prop->value";
      print "</td></tr>";
}
print "</table>";
print "</td></tr>";
?>
   </table>
</div>
<?php } ?>
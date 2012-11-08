<?php
$featuremap  = $variables['node']->featuremap;

// expand featuremap to include pubs 
$featuremap = tripal_core_expand_chado_vars($featuremap, 'table', 'featuremap_pub');
$pubs = $featuremap->featuremap_pub;
?>

<div id="tripal_featuremap-pub-box" class="tripal_featuremap-info-box tripal-info-box">
  <div class="tripal_featuremap-info-box-title tripal-info-box-title">Publications</div>
  <div class="tripal_featuremap-info-box-desc tripal-info-box-desc"></div>

   <table id="tripal_featuremap-pub-table" class="tripal_featuremap-table tripal-table tripal-table-vert" style="border-bottom:solid 2px #999999">
   <tr style="background-color:#EEEEFF;border-top:solid 2px #999999"><th style="width:80px;padding:5px 10px 5px 10px;">Year</th><th style="width:120px;padding-left:0px;">Reference</th><th style="padding-left:0px;">Title</th></tr>
<?php
$class = "";
$counter = 0;
if (is_array($pubs)) {
   foreach ($pubs AS $pub) {
      $class = featuremapGetTableRowClass($counter);
	   print "<tr class=\"" . $class ."\">";
	    print "<td style=\"padding:5px 10px 5px 10px;\">". $pub->pub_id->pyear . "</td><td style=\"padding:5px 0px 5px 0px;\">" . $pub->pub_id->uniquename . "</td><td style=\"padding:5px 0px 5px 0px;\">" . $pub->pub_id->title . "</td></tr>";
	   $counter ++;
   }
} else {
   print "<tr class=\"" .  featuremapGetTableRowClass(0) ."\">";
   print "<td style=\"padding:5px 10px 5px 10px;\">". $pubs->pub_id->pyear . "</td><td style=\"padding:5px 0px 5px 0px;\">" . $pubs->pub_id->uniquename . "</td><td style=\"padding:5px 0px 5px 0px;\">" . $pubs->pub_id->title . "</td></tr>";
}
?>
   </table>
</div>

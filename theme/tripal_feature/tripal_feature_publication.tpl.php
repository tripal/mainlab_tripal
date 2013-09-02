<?php
$feature  = $variables['node']->feature;

// expand feature to include pubs 
$feature = tripal_core_expand_chado_vars($feature, 'table', 'feature_pub');
$pubs = $feature->feature_pub;
$pubs = tripal_core_expand_chado_vars($pubs, 'field', 'pub.title');
$pubs = tripal_core_expand_chado_vars($pubs, 'table', 'pubprop');
?>

<div id="tripal_feature-pub-box" class="tripal_feature-info-box tripal-info-box">
  <div class="tripal_feature-info-box-title tripal-info-box-title">Publications</div>
  <div class="tripal_feature-info-box-desc tripal-info-box-desc"></div>

   <table id="tripal_feature-pub-table" class="tripal_feature-table tripal-table tripal-table-vert" style="border-bottom:solid 2px #999999">
   <tr style="background-color:#EEEEFF;border-top:solid 2px #999999"><th style="width:80px;padding:5px 10px 5px 10px;">Year</th><th style="width:120px;padding-left:0px;">Reference</th><th style="padding-left:0px;">Title</th></tr>
<?php

if (!is_array($pubs)) {
	$tmp = $pubs;
	$pubs = array();
	$pubs[0] = $tmp;
} 
	$counter = 0;
	$class = "";
   foreach ($pubs AS $pub) {
      if ($counter % 2 == 1) {
	      $class = "tripal_featuremap-table-even-row tripal-table-even-row";
      } else {
	      $class = "tripal_featuremap-table-odd-row tripal-table-odd-row";
      }
      	$citation = "";
      	$props = $pub->pub_id->pubprop;
      	if (!is_array($props)) {
			$tmp = $props;
			$props = array();
			$props [0] = $tmp;
		}
		foreach($props AS $prop) {
			if ($prop->type_id->name == 'Citation') {
				$p = tripal_core_expand_chado_vars($prop, 'field', 'pubprop.value'); 
				$citation = $p->value;
			}
		}
		if (!$citation) {
			$citation = $pub->pub_id->uniquename;
		}
	   print "<tr class=\"" . $class ."\">";
	    print "<td style=\"padding:5px 10px 5px 10px;\">". $pub->pub_id->pyear . "</td><td style=\"padding:5px 0px 5px 0px;\"><a href=\"/node/" . $pub->pub_id->nid . "\">" . $citation . "</a></td><td style=\"padding:5px 0px 5px 0px;\">" . $pub->pub_id->title . "</td></tr>";
	   $counter ++;
   }

?>
   </table>
</div>

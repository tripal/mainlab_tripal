<?php
$feature  = $variables['node']->feature;

// expand feature to include contacts
$feature = tripal_core_expand_chado_vars($feature, 'table', 'feature_contact');
$contacts = $feature->feature_contact;
$mycontacts = array();
if (is_array($contacts)) {
	$index = 0;
	foreach($contacts AS $contact) {
		$contact = tripal_core_expand_chado_vars($contact, 'table', 'contactprop');
	   $contactprops = $contact->contact_id->contactprop;
	   $contact_info = array();
	   $contact_info['contact_name'] = $contact->contact_id->name;
	   $contact_info['contact_desc'] = $contact->contact_id->description;
	   foreach ($contactprops AS $cprop) {
   		$contact_info[$cprop->type_id->name] = $cprop->value;
   	}	
	   $mycontacts[$index] = $contact_info;
	   $index ++;
	}
} else {
	$contacts = tripal_core_expand_chado_vars($contacts, 'table', 'contactprop');
	$contactprops = $contacts->contact_id->contactprop;
	$contact_info = array();
	$contact_info['contact_name'] = $contacts->contact_id->name;
	$contact_info['contact_desc'] = $contacts->contact_id->description;
	foreach ($contactprops AS $cprop) {
		$contact_info[$cprop->type_id->name] = $cprop->value;
	}	
	$mycontacts[0] = $contact_info; 
}
?>

<div id="tripal_feature-contact-box" class="tripal_feature-info-box tripal-info-box">
  <div class="tripal_feature-info-box-title tripal-info-box-title">Contact</div>
  <div class="tripal_feature-info-box-desc tripal-info-box-desc"></div>

   <table id="tripal_feature-contact-table" class="tripal_feature-table tripal-table tripal-table-vert" style="border-bottom:solid 2px #999999">
   <tr style="background-color:#EEEEFF;border-top:solid 2px #999999"><th style="padding:5px 10px 5px 10px;width:120px">Name</th><th>Details</th></tr>
<?php
$class = "";
$counter = 0;
foreach($mycontacts AS $con) {
   if ($counter % 2 == 1) {
	   $class = "tripal_featuremap-table-even-row tripal-table-even-row";
   } else {
	   $class = "tripal_featuremap-table-odd-row tripal-table-odd-row";
   }
	print "<tr class=\"" . $class ."\">";
	print "<td style=\"padding:5px 10px 5px 10px;\">" . $con['contact_name'] . "</td><td>";
	print "<table class=\"tripal-subtable\">";
   if ($con['contact_desc']) {
      print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Description</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">" . $con['contact_desc'] . "</td></tr>";
   }
   if (key_exists("first_name", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">First name</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['first_name'] . "</td></tr>";}
   if (key_exists("last_name", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Last name</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['last_name'] . "</td></tr>";}
   if (key_exists("title", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Title</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['title'] . "</td></tr>";}
   if (key_exists("institution", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Institution</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['institution'] . "</td></tr>";}
   if (key_exists("address", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Address</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['address'] . "</td></tr>";}
   if (key_exists("country",$con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Country</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['country'] . "</td></tr>";}
   if (key_exists("email", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Email</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\"><a href=\"mailto:". $con['email'] . "\">" . $con['email'] ."</td></tr>";}
   if (key_exists("phone", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Phone</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['phone'] . "</td></tr>";}
   if (key_exists("fax", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Fax</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['fax'] . "</td></tr>";}
   if (key_exists("keywords", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Keywords</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['keywords'] . "</td></tr>";}
   if (key_exists("last_update", $con)) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Last update</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con['last_update'] . "</td></tr>";}
   print "</table>";
   print "</td></tr>";
	$counter ++;
}
?>
   </table>
</div>

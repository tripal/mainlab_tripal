<?php
$featuremap  = $variables['node']->featuremap;

// expand featuremap to include contacts
$contacts = $featuremap->featuremap_contact;

// return if no data
if (!$contacts) {
  return;
}

$mycontacts = array();
if (is_array($contacts)) {
	$index = 0;
	foreach($contacts AS $contact) {
		$contact = chado_expand_var($contact, 'table', 'contactprop');
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
	$contacts = chado_expand_var($contacts, 'table', 'contactprop');
	$contactprops = $contacts->contact_id->contactprop;
	$contact_info = array();
	$contact_info['contact_name'] = $contacts->contact_id->name;
	$contact_info['contact_desc'] = $contacts->contact_id->description;
	foreach ($contactprops AS $cprop) {
		$contact_info[$cprop->type_id->name] = $cprop->value;
	}	
	$mycontacts[0] = $contact_info; 
}
$rows = array();
foreach ($mycontacts AS $con) {
  $details = '<table class=\"tripal-subtable\" style=\"margin:0px !important;\">';
  if ($con['contact_desc']) {
    $details .= "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;border:0px;\">Description:</td><td style=\"padding:2px 0px 2px 0px;\">" . $con['contact_desc'] . "</td></tr>";
  }
  $style_hd = "padding:2px 0px !important;width:80px;border:0px;";
  $style_td = "padding:2px 0px 2px 0px;border:0px;";
  if (key_exists("first_name", $con)) { $details .= "<tr><td style=\"$style_hd\">First name:</td><td style=\"$style_td\">". $con['first_name'] . "</td></tr>";}
  if (key_exists("last_name", $con)) { $details .= "<tr><td style=\"$style_hd\">Last name:</td><td style=\"$style_td\">". $con['last_name'] . "</td></tr>";}
  if (key_exists("title", $con)) { $details .= "<tr><td style=\"$style_hd\">Title:</td><td style=\"$style_td\">". $con['title'] . "</td></tr>";}
  if (key_exists("institution", $con)) { $details .= "<tr><td style=\"$style_hd\">Institution:</td><td style=\"$style_td\">". $con['institution'] . "</td></tr>";}
  if (key_exists("address", $con)) { $details .= "<tr><td style=\"$style_hd\">Address:</td><td style=\"$style_td\">". $con['address'] . "</td></tr>";}
  if (key_exists("country",$con)) { $details .= "<tr><td style=\"$style_hd\">Country:</td><td style=\"$style_td\">". $con['country'] . "</td></tr>";}
  if (key_exists("email", $con)) { $details .= "<tr><td style=\"$style_hd\">Email:</td><td style=\"$style_td\"><a href=\"mailto:". $con['email'] . "\">" . $con['email'] ."</td></tr>";}
  if (key_exists("phone", $con)) { $details .= "<tr><td style=\"$style_hd\">Phone:</td><td style=\"$style_td\">". $con['phone'] . "</td></tr>";}
  if (key_exists("fax", $con)) { $details .= "<tr><td style=\"$style_hd\">Fax:</td><td style=\"$style_td\">". $con['fax'] . "</td></tr>";}
  if (key_exists("keywords", $con)) { $details .= "<tr><td style=\"$style_hd\">Keywords:</td><td style=\"$style_td\">". $con['keywords'] . "</td></tr>";}
  if (key_exists("last_update", $con)) { $details .= "<tr><td style=\"$style_hd\">Last update:</td><td style=\"$style_td\">". $con['last_update'] . "</td></tr>";}
  $details .= "</table>";
  $rows [] = array ($con['contact_name'], $details);
}
$header = array ('Name', 'Details');
$table = array(
  'header' => $header,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_feature-table-contact',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);
?>
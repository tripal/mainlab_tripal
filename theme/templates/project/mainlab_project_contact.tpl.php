<?php
$project = $variables['node']->project;

// expand the project object to include the contacts from the project_contact
// table in chado.
$project = tripal_core_expand_chado_vars($project,'table','project_contact', array('return_array' => 1));
$contacts = $project->project_contact;

if (count($contacts) > 0) {
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
} ?>

<div id="tripal_project-contact-box" class="tripal_project-info-box tripal-info-box">
  <div class="tripal_project-info-box-desc tripal-info-box-desc"></div>

   <table id="tripal_project-contact-table" class="tripal_project-table tripal-table tripal-table-vert" style="border-bottom:solid 1px #CCCCCC">
   <tr style="background-color:#EEEEFF;border-top:solid 1px #CCCCCC"><th style="padding:5px 10px 5px 10px;width:120px">Name</th><th>Details</th></tr>
<?php
$class = "";
$counter = 1;
foreach($mycontacts AS $con) {
  $class = 'tripal_project-table-odd-row tripal-table-odd-row';
  if($counter % 2 == 0 ){
    $class = 'tripal_project-table-odd-row tripal-table-even-row';
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

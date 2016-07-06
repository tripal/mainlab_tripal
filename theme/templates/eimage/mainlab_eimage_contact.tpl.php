<?php
$eimage  = $variables['node']->eimage;
$contacts = $eimage->contacts;
if (count($contacts) < 1) {
  return;
}
?>

<div id="tripal_eimage-contact-box" class="tripal_eimage-info-box tripal-info-box">
  <div class="tripal_eimage-info-box-title tripal-info-box-title">Contact</div>
  <div class="tripal_eimage-info-box-desc tripal-info-box-desc"></div>

   <table id="tripal_eimage-contact-table" class="tripal_eimage-table tripal-table tripal-table-vert" style="border-bottom:solid 1px #999999">
   <tr style="background-color:#EEEEFF;border-top:solid 1px #999999"><th style="padding:5px 10px 5px 10px;width:120px">Name</th><th>Details</th></tr>
<?php
$class = "";
$counter = 0;
foreach($contacts AS $con) {
	if ($counter % 2 == 1) {
		$class = "tripal_eimage-table-even-row tripal-table-even-row";
	} else {
		$class = "tripal_eimage-table-odd-row tripal-table-odd-row";
	}
	print "<tr class=\"" . $class ."\">";
	print "<td style=\"padding:5px 10px 5px 10px;\">" . $con->name . "</td><td>";
	print "<table class=\"tripal-subtable\">";
   if ($con->description) {
      print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Description</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">" . $con->description . "</td></tr>";
   }
   if (property_exists($con, "first_name")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">First name</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->first_name . "</td></tr>";}
   if (property_exists($con, "last_name")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Last name</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->last_name . "</td></tr>";}
   if (property_exists($con, "title")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Title</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->title . "</td></tr>";}
   if (property_exists($con, "institution")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Institution</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->institution . "</td></tr>";}
   if (property_exists($con, "address")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Address</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->address . "</td></tr>";}
   if (property_exists($con, "country")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Country</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->country . "</td></tr>";}
   if (property_exists($con, "email")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Email</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\"><a href=\"mailto:". $con->email . "\">" . $con->email ."</td></tr>";}
   if (property_exists($con, "phone")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Phone</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->phone . "</td></tr>";}
   if (property_exists($con, "fax")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Fax</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->fax . "</td></tr>";}
   if (property_exists($con, "keywords")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Keywords</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->keywords . "</td></tr>";}
   if (property_exists($con, "last_update")) { print "<tr><td style=\"padding:2px 0px 2px 0px;width:80px;\">Last update</td><td>:</td><td style=\"padding:2px 0px 2px 0px;\">". $con->last_update . "</td></tr>";}
   print "</table>";
   print "</td></tr>";
	$counter ++;
}
?>
   </table>
</div>

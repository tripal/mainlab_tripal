<?php
$node = $variables['node'];
$eimage = $variables['node']->eimage;

// Pub image
$pub_img = '';
if (count($eimage->pubs) > 0) {
  $pub_img = 'sites/default/files/bulk_data/www.cottongen.org/cotton_photo/publication/image/' . $eimage->image_uri;
}

// Marker image
$marker_img = '';
if ($eimage->marker) {
  $marker_img = 'sites/default/files/bulk_data/www.cottongen.org/cotton_photo/genetic_marker/image/' . $eimage->image_uri;
}

// Stock image
$stock_img = '';
if ($eimage->stock) {
  $stock_img = 'sites/default/files/bulk_data/www.cottongen.org/cotton_photo/germplasm/image/' . $eimage->image_uri;
}

// Legends
$legends = $eimage->legends;
$legend = '';
foreach ($legends AS $leg) {
  $legend .= $leg . "<br>";
}
$legend =$legend ? $legend : "N/A"; 

//Contacts
$contacts = $eimage->contacts;
$contact = '';
foreach ($contacts AS $con) {
  $contact .= $con->name . "<br>";
}

// Pubs
$pubs = $eimage->pubs;
$pub = '';
foreach ($pubs AS $p) {
  $pub .= "<a href=\"/node/$p->nid\">" . $p->uniquename . "</a><br>";
}

// Projects
$projects = $eimage->project;
$project = '';
foreach ($projects AS $p) {
  $project .= "<a href=\"/node/$p->nid\">" . $p->name . "</a><br>";
}
?>
<div id="tripal_eimage-base-box" class="tripal_eimage-info-box tripal-info-box">

  <table id="tripal_eimage-table-base" class="tripal_eimage-table tripal-table tripal-table-vert">
    <tr class="tripal_eimage-table-even-row even">
      <th width=30%>Image Name</th>
      <td><?php print $eimage->image_uri; ?></td>
    </tr>
    <tr class="tripal_eimage-table-odd-row odd">
      <th>Image Legend</th>
      <td><?php print $legend?></td>
    </tr>
    
    <?php 
      $class_even = "tripal_eimage-table-even-row even";
      $class_odd = "tripal_eimage-table-odd-row odd";
      $class_counter = 0;
      
      // Associated Projects
      if (count($projects) > 0) {
        $class = $class_counter % 2 == 0 ? $class_even : $class_odd;
        $class_counter ++;
        print "<tr class=\"$class\"><th>Associated Project</th><td>$project</td></tr>";
      }
      // Associated Pubs
      if (count($pubs) > 0) {
        $class = $class_counter % 2 == 0 ? $class_even : $class_odd;
        $class_counter ++;
        print "<tr class=\"$class\"><th>Associated Publication</th><td>$pub</td></tr>";
      }
      // Associated Germplasm
      if ($eimage->stock) {
        $class = $class_counter % 2 == 0 ? $class_even : $class_odd;
        $class_counter ++;
        print "<tr class=\"$class\"><th>Associated Germplasm</th><td><a href=\"" . $eimage->stock->nid . "\">" . $eimage->stock->uniquename . "</a></td></tr>";
      }
      // Associated Germplasm
      if ($eimage->marker) {
        $class = $class_counter % 2 == 0 ? $class_even : $class_odd;
        $class_counter ++;
        print "<tr class=\"$class\"><th>Associated Marker</th><td><a href=\"" . $eimage->marker->nid . "\">" . $eimage->marker->uniquename . "</a></td></tr>";
      }
      // Contact
      if (count($contacts) > 0) {
        $class = $class_counter % 2 == 0 ? $class_even : $class_odd;
        $class_counter ++;
        $prepend = "<a href=\"#\" onClick=\"$('.tripal-info-box').hide();$('#tripal_eimage-contact-box').fadeIn('slow');$('.tripal_toc').height($('#tripal_eimage-contact-box').parent().height());return false;\">";
        print "<tr class=\"$class\"><th>Contact</th><td>$prepend$contact</a></td></tr>";
      }
    ?>
      
  </table> 
  <?php
  $image = $eimage->image_uri;
  if ($pub_img) {
    print "<div style=\"margin-top:25px;clear:both;\"><a href=\"/$pub_img\" target=\"_blank\"><img src=\"/$pub_img\" width=\"100%\" style=\"max-width:800px\"></a></div>";
  }
  if ($marker_img) {
    print "<div style=\"margin-top:25px;clear:both;\"><a href=\"/$marker_img\" target=\"_blank\"><img src=\"/$marker_img\" width=\"100%\" style=\"max-width:800px\"></a></div>";
  }
  if ($stock_img) {
    print "<div style=\"margin-top:25px;clear:both;\"><a href=\"/$stock_img\" target=\"_blank\"><img src=\"/$stock_img\" width=\"100%\" style=\"max-width:800px\"></a></div>";
  }
  ?>
</div>

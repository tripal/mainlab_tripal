<?php
$stock = $variables['node']->stock;
$images = $stock->images;

//$path = file_directory_path() . '/tripal/tripal_stock/images/';
$site = mainlab_tripal_get_site();
if ($site == 'gdr') {
	$icons = '/bulk_data/www.rosaceae.org/gdr_photo/germplasm/icon/';
	$imgs = '/bulk_data/www.rosaceae.org/gdr_photo/germplasm/image/';
} else if ($site == 'cottongen') {
	$icons = '/bulk_data/www.cottongen.org/cotton_photo/germplasm/icon/icon-';
	$imgs = '/bulk_data/www.cottongen.org/cotton_photo/germplasm/image/';
} else {
  return;
}

$icon_path = 'sites/default/files' . $icons;
$img_path = 'sites/default/files' . $imgs;

if (count($images) > 0) { ?>
  <div id="tripal_stock-images-box" class="tripal_stock-info-box tripal-info-box">
    <!--<div class="tripal_stock-info-box-desc tripal-info-box-desc">The feature '<?php print $stock->name ?>' has the following images</div>  -->
        
    <table id="tripal_stock-images-table" class="tripal_stock-table tripal-table tripal-table-horz" style="width:700px;">
        <tr>
          <?php 
            $count = 1;
            $private_data_only = 1;
            foreach($images as $img) {
              if (file_exists($icon_path . $img->image_uri)) {
                $iconurl = url($icon_path . $img->image_uri);
                $imgurl = url($img_path . $img->image_uri);
                $private_data_only = 0;
                print "<td  style=\"width:200px;padding:20px 0px 0px 20px;\">";
                print "<a href=$imgurl target=_blank><img src=\"$iconurl\" style=\"cursor:pointer;\"></a>"; 
                print "<div style=\"clear:left;margin-bottom:10px;\">" . $img->legend ."</div></td>";
                if ($count % 3 == 0) {
                  print "</tr><tr>";
                }
                $count ++;
              }
            }
            if ($private_data_only) {
              print "<td>no public data</td>";
            } else {
              // Add more td to ensure the width of each cell is fixed
              $more = 3 - (count($images) % 3);
              for ($i = 0; $i < $more; $i ++) {
	            print "<td  style=\"width:200px;padding:20px 0px 0px 20px;\"></td>";
            }
}
        ?>
      </tr> 
    </table>
  </div>
<?php } ?>
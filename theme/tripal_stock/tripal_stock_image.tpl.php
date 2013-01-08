<?php
$stock = $variables['node']->stock;
$images = $stock->images;

$path = file_directory_path() . '/tripal/tripal_stock/images/';

if (count($images) > 0) { ?>
<script type="text/javascript">
function tripal_stock_show_image(src) {
  $('#tripal_stock-image_source').attr('src', src);
  $('#tripal_stock-image_display_large').fadeIn();
}
</script>
  <div id="tripal_stock-image_display_large" style="position:absolute;margin-top:-100px;margin-left:150px;border:1px solid #ccc;padding:10px;background-color:#FFFFFF;cursor:pointer;display:none;" onclick="$(this).fadeOut();"><img id="tripal_stock-image_source"></div>
  <div id="tripal_stock-images-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">Images</div>
    <!--<div class="tripal_stock-info-box-desc tripal-info-box-desc">The feature '<?php print $stock->name ?>' has the following images</div>  -->
        
    <table id="tripal_stock-images-table" class="tripal_stock-table tripal-table tripal-table-horz">
        <tr>
          <?php 
            $count = 1;
            foreach($images as $img) {
              $imgurl = url($path . $img->image_uri); 
              $size = getimagesize($path . $img->image_uri);
              $w = $size [0];
              $h = $size [1];
              $resize = 'width';
              if ($w > $h) {
                $resize = 'height';
              }
              print "<td  style=\"width:138px;\"><div style=\"float:left;height:138px;width:138px;overflow:hidden;border:1px solid #ccc;margin:10px 0px 10px 0px;\">";
              print "<img src=\"$imgurl\" $resize=\"146\" style=\"cursor:pointer;\" onclick=\"tripal_stock_show_image('$imgurl')\"></div>"; 
              print "<div style=\"clear:left;margin-bottom:10px;\">" . $img->legend ."</div></td>";
              if ($count % 4 == 0) {
                print "</tr><tr>";
              }
            $count ++; 
            }
            // Add more td to ensure the width of each cell is fixed
            $more = 4 - (count($images) % 4);
            for ($i = 0; $i < $more; $i ++) {
              print "<td  style=\"width:138px;\"></td>";
              
            }
        ?>
      </tr> 
    </table>
  </div>
<?php } ?>
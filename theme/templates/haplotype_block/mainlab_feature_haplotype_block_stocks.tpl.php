<?php
$feature = $variables['node']->feature;
$stocks = $feature->stocks;

if (count($stocks) > 0) { ?>
  <div id="tripal_feature-stocks-box" class="tripal_feature-info-box tripal-info-box">
    <table id="tripal_feature-stocks-table" class="tripal_feature-table tripal-table tripal-table-horz" style="width:100%;">
      <tr width=100%><th>#</th><th>Germplasm</th></tr>
          <?php 
            $count = 1;
            foreach($stocks as $stock) {
              $class = "even";
              if ($count % 2 == 1) {
                $class = "odd";
              }
              print "<tr class=\"tripal_feature-table-$class-row $class\"><td>$count</td><td><a href=/legacy/breeders_toolbox/germplasm/$stock->stock_id>$stock->uniquename</a></td></tr>";
              $count ++; 
            }
            // Add more td to ensure the width of each cell is fixed
            
        ?>
    </table>
  </div>
<?php } ?>
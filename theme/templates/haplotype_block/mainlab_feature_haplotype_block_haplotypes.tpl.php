<?php
$feature = $variables['node']->feature;
$haplotypes = $feature->haplotypes;

if (count($haplotypes) > 0) { ?>
  <div id="tripal_feature-haplotypes-box" class="tripal_feature-info-box tripal-info-box">
    <div class="tripal_feature-info-box-title tripal-info-box-title">Haplotype</div>
    <!--<div class="tripal_feature-info-box-desc tripal-info-box-desc">The feature '<?php print $feature->name ?>' has the following associated germplasms</div>  -->
        
    <table id="tripal_feature-haplotypes-table" class="tripal_feature-table tripal-table tripal-table-horz" style="width:100%;">
      <tr width=100%><th>Marker</th>
          <?php 
            $count = 1;
            foreach($haplotypes as $marker_feature_id => $haplotype) {
              $arr = $haplotype->haplotypes;
              ksort($arr);
              if ($count == 1) {
                foreach ($arr AS $k => $v) {
                  print "<th>$k</th>";
                }
                print "</tr>";
              }
              $class = "even";
              if ($count % 2 == 1) {
                $class = "odd";
              }
              print "<tr class=\"tripal_feature-table-$class-row $class\">";
              print "<td><a href=/node/$haplotype->nid>$haplotype->name</a></td>";
              foreach ($arr AS $k => $v) {
              	print "<td>$v</td>";
              }
              print "</tr>";
              $count ++; 
            }
            // Add more td to ensure the width of each cell is fixed
            
        ?>
    </table>
  </div>
<?php } ?>
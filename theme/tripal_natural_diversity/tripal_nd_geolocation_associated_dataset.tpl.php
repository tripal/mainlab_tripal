<?php
$nd_geolocation = $node->nd_geolocation;
$associated_dataset =$nd_geolocation->projects;

if (count($associated_dataset) > 0) { ?>
  <div id="tripal_nd_geolocation-associated_dataset-box" class="tripal_nd_geolocation-info-box tripal-info-box">
    <div class="tripal_nd_geolocation-info-box-title tripal-info-box-title">Associated Dataset</div>
    <div class="tripal_nd_geolocation-info-box-desc tripal-info-box-desc">Associated dataset includes:</div>
    <table class="tripal_nd_geolocation-table tripal-table tripal-table-horz">
      <tr>
        <th>Name</th>
        <th>Description</th>
      </tr> <?php
      $i = 0;
      foreach ($associated_dataset as $proj) {
        $class = 'tripal_nd_geolocation-table-odd-row tripal-table-odd-row';
        if ($i % 2 == 0 ) {
           $class = 'tripal_nd_geolocation-table-odd-row tripal-table-even-row';
        }
        $i++; 
        ?>
        <tr class="<?php print $class ?>">
          <td><?php print $proj->name ?></td>
          <td><?php print $proj->description ?></td>
        </tr><?php 
      } ?>
    </table>
  </div> <?php
}

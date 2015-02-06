<?php
$feature  = $variables['node']->feature;
if (!$feature->name) {
  $feature->name = $feature->uniquename; // show uniquname if there is no name
}
?>

<div id="tripal_feature-base-box" class="tripal_feature-info-box tripal-info-box">
  <div class="tripal_feature-info-box-title tripal-info-box-title">Haplotype Block Details</div>
  <div class="tripal_feature-info-box-desc tripal-info-box-desc"></div> <?php 
  if(strcmp($feature->is_obsolete,'t')==0){ ?>
    <div class="tripal_feature-obsolete">This feature is obsolete</div> <?php 
  }?>
  <table id="tripal_feature-base-table" class="tripal_feature-table tripal-table tripal-table-vert">

    <!-- Name -->
    <tr class="tripal_feature-table-even-row tripal-table-even-row">
      <th width=30%>Name</th>
      <td><?php print $feature->name; ?></td>
    </tr>
    <!-- Species -->
    <tr class="tripal_feature-table-odd-row tripal-table-odd-row">
      <th>Species</th>
      <td><?php print $feature->organism_id->genus . ' '. $feature->organism_id->species; ?></td>
    </tr>
    <!-- Germplasms -->
    <tr class="tripal_feature-table-even-row tripal-table-even-row">
      <th>Germplasm</th>
      <td><?php 
        $num_stocks = count($feature->stocks);
        $display_stocks = $num_stocks == 0 ? "N/A" : "[<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-stocks-box\">view all $num_stocks</a>]";
        print $display_stocks;
      ?></td>
    </tr>
    <!-- Haplotypes -->
    <tr class="tripal_feature-table-odd-row tripal-table-odd-row">
      <th>Haplotype</th>
      <td><?php 
        $num_haplotypes = count($feature->haplotypes);
        $display_haplotypes = $num_haplotypes == 0 ? "N/A" : "[<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-haplotypes-box\">view all</a>]";
        print $display_haplotypes;
      ?></td>
    </tr>
   </table>
</div>

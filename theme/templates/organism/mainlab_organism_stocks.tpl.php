<?php

$organism = $variables['node']->organism;
$org = $organism->genus . '-' . $organism->species;
$stock = mainlab_stock_listing($org, 1); 

if ($stock->total_number > 0) { ?>
  <div id="tripal_organism-stock_browser-box" class="tripal_organism-info-box tripal-info-box">
    <?php print $stock->table; ?>
  </div> 

<script type="text/javascript">
   // Insert to the base template
   <?php if ($stock->total_number > 0) {?>
         jQuery('#tripal-organism-germplasm').html("[<a href='?pane=stocks'>view all <?php print $stock->total_number;?></a>]");
   <?php } ?>
</script>

<?php print $stock->js; ?>
<?php
} 





<?php
$stock = $variables['node']->stock;
$in_collection = $stock->in_collection;
$num_in_collection = count($in_collection);
if ($num_in_collection > 0) {
?>

  <div id="tripal_stock-in_collection-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">In Collection</div>
    <table id="tripal_stock-in_collection-table" class="tripal_stock-table tripal-table tripal-table-horz" style="margin-bottom:20px;">
             <tr>
               <th>Name</th>
               <th>ID used in Collection</th>
               <th>External Database</th>
             </tr>
    <?php
      $counter = 0;
      $class = "";
      foreach ($in_collection as $coll){
         if ($counter % 2 == 0) {
            $class = "tripal_stock-table-even-row even";
         } else {
            $class = "tripal_stock-table-odd-row odd";
         }
         $link = "";
         // Added hyperlink if urlprefix exists
         if ($coll->urlprefix) {
            $db = explode("_", $coll->db);
            $acc = $coll->accession;
            // Rewrite accession according to its db
            if ($coll->db == 'GRIN_COT' || $coll->db == 'GRIN_PVP') {
               $array = explode(" ", $acc);
               $acc = $array[0];
               if (key_exists(1, $array)) {
	             $acc .= "+" . $array[1];
               }
            } else if ($coll->db == 'GRIN_Regist') {
               $array = explode("-", $acc);
               $acc = $array[0] . " COTTON";
            }
            // Add hyperlinks
            $accs = explode(';', $acc);
            $versions = explode(';', $coll->version);
            if ($coll->db == 'GRIN_COT') {
              for ($i = 0; $i < count($accs); $i ++) {
                 $link .= "<a href=\"". $coll->urlprefix . $accs[$i] ."\" target=_blank>" . $db[0] . ": " . $versions[$i] . "</a>";
                 $link = $i < count($accs) - 1 ? $link .= '; ' : $link; // Add a semicolon if there are more than one record
              }
            } else {
              for ($i = 0; $i < count($accs); $i ++) {
                 $link = "<a href=\"". $coll->urlprefix . $accs[$i] ."\" target=_blank>" . $versions[$i] . "</a>";
                 $link = $i < count($accs) - 1 ? $link .= '; ' : $link; // Add a semicolon if there are more than one record
               }
            }
         }
         // Use accession instead of version if version is empty
         $id = str_replace('; ', '', $coll->version) ? $coll->version : $coll->accession;
         print "<tr class=\"$class\"><td>$coll->collection</td><td>$id</td><td>$link</td></tr>";
         $counter ++;
      }
    ?>
    </table>
  </div>

 <?php
}

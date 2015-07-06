<?php

$stock = $variables['node']->stock;

// expand the stock object to include the feature maps and their properties
$options = array('return_array' => 1);
$stock = chado_expand_vars($stock, 'table', 'featuremap_stock', $options);
$featuremap_stocks = $stock->featuremap_stock;

if (count($featuremap_stocks) > 0) {?>
  <div id="tripal_stock-maps-box" class="tripal_stock-info-box tripal-info-box">
    <div class="tripal_stock-info-box-title tripal-info-box-title">Maps</div>
<!--    <div class="tripal_stock-info-box-desc tripal-info-box-desc">This stock has been used with the following maps.</div> -->
    <table id="tripal_stock-table-collection" class="tripal_stock-table tripal-table tripal-table-horz">     
      <tr class="tripal_stock-table-odd-row tripal-table-even-row">
        <th>Map Name</th>
        <th>Map Details</th>
      </tr> <?php
      foreach ($featuremap_stocks as $featuremap_stock){ 
        // get the ma properties
        $featuremap = $featuremap_stock->featuremap_id;
        $values = array('featuremap_id' => $featuremap->featuremap_id);
        $properties = tripal_core_generate_chado_var('featuremapprop', $values, $options);
        
        $class = 'tripal_stock-table-odd-row tripal-table-odd-row';
        if($i % 2 == 0 ){
          $class = 'tripal_stock-table-odd-row tripal-table-even-row';
        } ?>
        <tr class="<?php print $class ?>">
          <td><?php 
            if($featuremap->nid){    
              $link =  url("node/$featuremap->nid");        
              print "<a href=\"$link\">$featuremap->name</a>";
            } 
            else {
              print $featuremap->name;
            } ?>
          </td>
          <td>
            <table class="tripal-subtable"> <?php
              foreach ($properties as $property){ ?>
                <tr>
                  <td><?php print ucwords(preg_replace('/_/', ' ', $property->type_id->name))?></td>
                  <td>:</td>
                  <td><?php print $property->value ?></td>
                </tr> <?php
              } ?>
            </table>
          </td>
        </tr> <?php
        $i++; 
      }?>  
    </table> 
  </div><?php
}

<?php

$stock = $variables['node']->stock;

// expand the stock object to include the feature maps and their properties
//$options = array('return_array' => 1);
//$stock = chado_expand_var($stock, 'table', 'library_stock', $options);
$library_stocks = $stock->library_stock;

if (count($library_stocks) > 0) {?>
  <div id="tripal_stock-maps-box" class="tripal_stock-info-box tripal-info-box">
    <table id="tripal_stock-table-collection" class="tripal_stock-table tripal-table tripal-table-horz">     
      <tr class="tripal_stock-table-odd-row tripal-table-even-row">
        <th>Name</th>
        <th colspan=3>Details</th>
      </tr> <?php
      $i = 0;
      foreach ($library_stocks as $library_stock){ 
        // get the ma properties
        $library = $library_stock->library_id;
        $values = array('library_id' => $library->library_id);
        $properties = tripal_core_generate_chado_var('libraryprop', $values, array('return_array' => 1));

        $class = 'tripal_stock-table-odd-row odd';
        if($i % 2 == 0 ){
          $class = 'tripal_stock-table-odd-row even';
        } ?>
        <tr class="<?php print $class ?>">
          <td><?php 
            if(property_exists($library, 'nid')){    
              $link =  url("node/$library->nid");        
              print "<a href=\"$link\">$library->name</a>";
            } 
            else {
              print $library->name;
            } ?>
          </td>
          <td>
            <table class="tripal-subtable"> <?php
              foreach ($properties as $property){ ?>
                <tr>
                  <td width=160px><?php print ucwords(preg_replace('/_/', ' ', $property->type_id->name))?></td>
                  <td width=15px>:</td>
                  <td><?php if (property_exists($property, 'value')) {print $property->value;} ?></td>
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

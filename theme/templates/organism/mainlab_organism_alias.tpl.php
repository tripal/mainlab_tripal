<?php

$properties = property_exists($node->organism, 'organismprop') ? $node->organism->organismprop : NULL;
$cname = array();
$syn = array();

if ($properties) {
  foreach ($properties AS $p) {
    if ($p->type_id->name == 'alias_common') {
       array_push($cname, $p->value);
    } else if ($p->type_id->name == 'alias_synonym') {
      array_push($syn, $p->value);
    }
  }
}

if(count($cname) > 0 || count($syn) > 0){ ?>
    <?php if (count($syn) > 0){?>
    <table class="tripal_organism-synonym-table tripal-table tripal-table-horz">
      <tr><th>Synonym</th></tr> <?php  
      $i = 0;
      // iterate through each property
      foreach ($syn as $s){
        $class = 'tripal_organism-table-odd-row odd';
        if($i % 2 == 0 ){
           $class = 'tripal_organism-table-even-row even';
        }?>   
        <tr class="<?php print $class ?>">
          <td><?php print $s?></td>
        </tr> <?php        
        $i++;
      } ?>
    </table><br><br>
    <?php } ?>
    <?php if (count($cname) > 0){?>
    <table class="tripal_organism-commonname-table tripal-table tripal-table-horz">
      <tr><th>Common Name</th></tr> <?php  
      $i = 0;
      // iterate through each property
      foreach ($cname as $c){
        $class = 'tripal_organism-table-odd-row odd';
        if($i % 2 == 0 ){
           $class = 'tripal_organism-table-even-row even';
        }?>   
        <tr class="<?php print $class ?>">
          <td><?php print $c?></td>
        </tr> <?php        
        $i++;
      } ?>
    </table>
    <?php }
}

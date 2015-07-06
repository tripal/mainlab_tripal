<?php

$options = array('return_array' => 1);
$node = chado_expand_var($node, 'table', 'organismprop', $options);
$properties = $node->organism->organismprop;

if(count($properties) > 0){ ?>
	<div id="tripal_organism-properties-box" class="tripal_organism-info-box tripal-info-box">
	  <div class="tripal_organism-info-box-title tripal-info-box-title">Properties</div>
	  <!-- <div class="tripal_organism-info-box-desc tripal-info-box-desc">Properties for the organism '<?php print $node->organism->genus . " " . $node->organism->species ?>' include:</div> -->
	  <table class="tripal_organism-table tripal-table tripal-table-horz">
	    <tr><th>Type</th><th>Value</th></tr> <?php	
			$i = 0;
			// iterate through each property
			foreach ($properties as $property){
			  $class = 'tripal_organism-table-odd-row tripal-table-odd-row';
	      if($i % 2 == 0 ){
	         $class = 'tripal_organism-table-even-row tripal-table-even-row';
	      }?>   
        <tr class="<?php print $class ?>">
          <td><?php print ucwords(preg_replace('/_/', ' ', $property->type_id->name)) ?></td>
          <td><?php print $property->value?></td>
        </tr> <?php	      
				$i++;
			} ?>
	  </table>
	</div><?php  
}

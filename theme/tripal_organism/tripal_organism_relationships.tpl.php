<?php

$organism = $variables['node']->organism;

$all_relationships = $organism->all_relationships;
$object_rels = $all_relationships['object'];
$subject_rels = $all_relationships['subject'];

if (count($object_rels) > 0 or count($subject_rels) > 0) {
?>
  <div id="tripal_organism-relationships-box" class="tripal_organism-info-box tripal-info-box">
    <div class="tripal_organism-info-box-title tripal-info-box-title">Fertile/Sterile/Incompatible Species</div>
    <div class="tripal_organism-info-box-desc tripal-info-box-desc"></div> <?php
    
      // first add in the subject relationships.  
      $count_fertile;
      $count_sterile;
      $count_incompatible;
      $first_fertile;
      $first_sterile;
      $first_incompatible;
      foreach ($subject_rels as $rel_type => $objects){
         // make the type a bit more human readable
         $rel_type = preg_replace("/_/", ' ', $rel_type);
         $rel_type = preg_replace("/^is/", '', $rel_type); ?>
         <p>This species is <b><?php print $rel_type ?></b> the following:
         <table id="tripal_organism-relationships_as_object-table" class="tripal_organism-table tripal-table tripal-table-horz">
           <tr>
             <th>Organism</th>
           </tr> <?php
           $counter = 0;
           $class = 'odd';
           foreach ($objects as $object){ ?>
             <tr class="<?php print $class; ?>">
               <td>
                 <?php 
                  if ($object->nid) {
                    print "<i><a href=\"" . url("node/" . $object->nid) . "\" target=\"_blank\">" . $object->genus . " " . $object->species . "</i></a>";
                  }
                  else {
                    print "<i>" . $object->genus . " " . $object->species . "</i>";
                  } 
                  
                  if ($rel_type == 'fertile with') {
                  	$first_fertile = $object->genus . " " . $object->species;
                  } else if ($rel_type == 'sterile with') {
                  	$first_sterile = $object->genus . " " . $object->species;
                  } else if ($rel_type == 'incompatible with') {
                  	$first_incompatible = $object->genus . " " . $object->species;
                  }
                  ?>
               </td>
             </tr> <?php
             if ($counter % 2 == 0) {
               $class = 'even';
             } else {
               $class = 'odd';
             }
             $counter ++;
           } ?>
         </table>
         </p><br><?php
      }
      ?>
  </div> 
  
  
  <?php
}

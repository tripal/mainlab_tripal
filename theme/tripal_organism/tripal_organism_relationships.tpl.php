<?php

$organism = $variables['node']->organism;

$all_relationships = $organism->all_relationships;
$object_rels = $all_relationships['object'];
$subject_rels = $all_relationships['subject'];

// make the organism type a bit more human readable
$organism_type =  preg_replace("/_/", ' ', $organism->type_id->name);

if (count($object_rels) > 0 or count($subject_rels) > 0) {
?>
  <div id="tripal_organism-relationships-box" class="tripal_organism-info-box tripal-info-box">
    <div class="tripal_organism-info-box-title tripal-info-box-title">Relationships</div>
    <div class="tripal_organism-info-box-desc tripal-info-box-desc"></div> <?php
    
      // first add in the subject relationships.  
      foreach ($subject_rels as $rel_type => $rels){
         // make the type a bit more human readable
         $rel_type = preg_replace("/_/", ' ', $rel_type);
         $rel_type = preg_replace("/^is/", '', $rel_type);
         // iterate through each parent   
         foreach ($rels as $objects){?>
           <p>This species is <b><?php print $rel_type ?></b> the following <?php print $obj_type ?>:
           <table id="tripal_organism-relationships_as_object-table" class="tripal_organism-table tripal-table tripal-table-horz">
             <tr>
               <th>Organism</th>
             </tr> <?php
             foreach ($objects as $object){ ?>
               <tr>
                 <td><?php 
                    if ($object->nid) {
                      print "<i><a href=\"" . url("node/" . $object->nid) . "\" target=\"_blank\">" . $object->genus . " " . $object->species . "</i></a>";
                    }
                    else {
                      print "<i>" . $object->genus . " " . $object->species . "</i>";
                    } ?>
                 </td>
               </tr> <?php
             } ?>
             </table>
             </p><br><?php
         }
      }
      /*
      // second add in the object relationships. 
      foreach ($object_rels as $rel_type => $rels){
         // make the type more human readable
         $rel_type = preg_replace('/_/', ' ', $rel_type);
         $rel_type = preg_replace("/^is/", '', $rel_type);
         // iterate through the children         
         foreach ($rels as $subject_type => $subjects){?>
           <p>The following <?php print $subject_type ?> are <b><?php print $rel_type ?></b> this species:
           <table id="tripal_organism-relationships_as_object-table" class="tripal_organism-table tripal-table tripal-table-horz">
             <tr>
               <th>Stock Name</th>
               <th>Type</th>
             </tr> <?php
             foreach ($subjects as $subject){ ?>
               <tr>
                 <td><?php 
                    if ($subject->nid) {
                      print "<i><a href=\"" . url("node/" . $subject->nid) . "\" target=\"_blank\">" . $subject->genus . " " . $subject->species . "</i></a>";
                    }
                    else {
                      print "<i>" . $subject->genus . " " . $subject->species . "</a>";
                    } ?>
                 </td>
               </tr> <?php
             } ?>
             </table>
             </p><br><?php
         }
      } */?>
  </div> <?php
}

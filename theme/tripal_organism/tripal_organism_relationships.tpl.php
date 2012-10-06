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
         foreach ($rels as $obj_type => $objects){?>
           <p>This organism is <b><?php print $rel_type ?></b> the following <?php print $obj_type ?> organism(s):
           <table id="tripal_organism-relationships_as_object-table" class="tripal_organism-table tripal-table tripal-table-horz">
             <tr>
               <th>Organism</th>
               <th>Type</th>
             </tr> <?php
             foreach ($objects as $object){ ?>
               <tr>
                 <td><?php 
                    if ($object->record->nid) {
                      print "<a href=\"" . url("node/" . $object->record->nid) . "\" target=\"_blank\">" . $object->record->object_id->name . "</a>";
                    }
                    else {
                      print $object->record->object_id->name;
                    } ?>
                 </td>
                 <td><?php print ucwords(preg_replace('/_/', ' ', $object->record->object_id->type_id->name)) ?></td>                 
               </tr> <?php
             } ?>
             </table>
             </p><br><?php
         }
      }
      
      // second add in the object relationships.  
      foreach ($object_rels as $rel_type => $rels){
         // make the type more human readable
         $rel_type = preg_replace('/_/', ' ', $rel_type);
         $rel_type = preg_replace("/^is/", '', $rel_type);
         // iterate through the children         
         foreach ($rels as $subject_type => $subjects){?>
           <p>The following <?php print $subject_type ?> are <b><?php print $rel_type ?></b> this organism:
           <table id="tripal_organism-relationships_as_object-table" class="tripal_organism-table tripal-table tripal-table-horz">
             <tr>
               <th>Stock Name</th>
               <th>Type</th>
             </tr> <?php
             foreach ($subjects as $subject){ ?>
               <tr>
                 <td><?php 
                    if ($subject->record->nid) {
                      print "<a href=\"" . url("node/" . $subject->record->nid) . "\" target=\"_blank\">" . $subject->record->subject_id->name . "</a>";
                    }
                    else {
                      print $subject->record->subject_id->name;
                    } ?>
                 </td>
                 <td><?php print ucwords(preg_replace('/_/', ' ', $subject->record->subject_id->type_id->name)) ?></td>                 
               </tr> <?php
             } ?>
             </table>
             </p><br><?php
         }
      } ?>
  </div> <?php
}

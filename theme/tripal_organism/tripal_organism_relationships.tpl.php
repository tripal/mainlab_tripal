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
         <p>This species is <b><?php print $rel_type ?></b> the following <?php print $obj_type ?>:
         <table id="tripal_organism-relationships_as_object-table" class="tripal_organism-table tripal-table tripal-table-horz">
           <tr>
             <th>Organism</th>
           </tr> <?php
           foreach ($objects as $object){ ?>
             <tr>
               <td>
                 <?php 
                  if ($object->nid) {
                    print "<i><a href=\"" . url("node/" . $object->nid) . "\" target=\"_blank\">" . $object->genus . " " . $object->species . "</i></a>";
                  }
                  else {
                    print "<i>" . $object->genus . " " . $object->species . "</i>";
                  } 
                  
                  if ($rel_type == 'fertile with') {
                  	$count_fertile ++;
                  	if (!$first_fertile) { $first_fertile = $object->genus . " " . $object->species;}
                  } else if ($rel_type == 'sterile with') {
                  	$count_sterile ++;
                  	if (!$first_sterile) { $first_sterile = $object->genus . " " . $object->species;}
                  } else if ($rel_type == 'incompatible with') {
                  	$count_incompatible ++;
                  	if (!$first_incompatible) { $first_incompatible = $object->genus . " " . $object->species;}
                  }
                  ?>
               </td>
             </tr> <?php
           } ?>
         </table>
         </p><br><?php
      }
      
      /*// second add in the object relationships. 
      foreach ($subject_rels as $rel_type => $subjects){
         // make the type a bit more human readable
         $rel_type = preg_replace("/_/", ' ', $rel_type);
         $rel_type = preg_replace("/^is/", '', $rel_type); ?>
         <p>This following species are <b><?php print $rel_type ?></b> with this species:
         <table id="tripal_organism-relationships_as_object-table" class="tripal_organism-table tripal-table tripal-table-horz">
           <tr>
             <th>Organism</th>
           </tr> <?php
           foreach ($subjects as $subject){ ?>
             <tr>
               <td><?php 
                  if ($subject->nid) {
                    print "<i><a href=\"" . url("node/" . $subject->nid) . "\" target=\"_blank\">" . $subject->genus . " " . $subject->species . "</i></a>";
                  }
                  else {
                    print "<i>" . $subject->genus . " " . $subject->species . "</i>";
                  } ?>
               </td>
             </tr> <?php
           } ?>
         </table>
         </p><br><?php
      } */?>
  </div> 
  
<script type="text/javascript">
   // Insert to the base template
   <?php if ($count_fertile > 0) {?>
      $('#tripal-organism-fertile-species').html("<?php print $first_fertile;?> [<a href='#' id='tripal-organism-fertile-species-link'>view all <?php print $count_fertile;?></a>]");
      $('#tripal-organism-fertile-species-link').click(function() {
         $('.tripal-info-box').hide();
         $('#tripal_organism-relationships-box').fadeIn('slow');
         $('#tripal_organism_toc').height($('#tripal_organism-relationships-box').parent().height());
      })
   <?php } ?>
   <?php if ($count_sterile > 0) {?>
      $('#tripal-organism-sterile-species').html("<?php print $first_sterile;?> [<a href='#' id='tripal-organism-sterile-species-link'>view all <?php print $count_sterile;?></a>]");
      $('#tripal-organism-sterile-species-link').click(function() {
         $('.tripal-info-box').hide();
         $('#tripal_organism-relationships-box').fadeIn('slow');
         $('#tripal_organism_toc').height($('#tripal_organism-relationships-box').parent().height());
      })
   <?php } ?>
   <?php if ($count_incompatible > 0) {?>
      $('#tripal-organism-incompatible-species').html("<?php print $first_incompatible;?> [<a href='#' id='tripal-organism-incompatible-species-link'>view all <?php print $count_incompatible;?></a>]");
      $('#tripal-organism-incompatible-species-link').click(function() {
         $('.tripal-info-box').hide();
         $('#tripal_organism-relationships-box').fadeIn('slow');
         $('#tripal_organism_toc').height($('#tripal_organism-relationships-box').parent().height());
      })
   <?php } ?>
</script>
  
  <?php
}

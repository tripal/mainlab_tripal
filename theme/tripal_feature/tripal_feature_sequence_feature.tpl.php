<?php

$feature  = $variables['node']->feature;

$feature = chado_expand_var($feature,'field','feature.residues');
?>
<div id="tripal_feature-base-box" class="tripal_feature-info-box tripal-info-box">
  <div class="tripal_feature-info-box-title tripal-info-box-title">Sequence Details</div>
  <div class="tripal_feature-info-box-desc tripal-info-box-desc"></div>

   <?php if(strcmp($feature->is_obsolete,'t')==0){ ?>
      <div class="tripal_feature-obsolete">This feature is obsolete</div>
   <?php }?>
   <table id="tripal_feature-base-table" class="tripal_feature-table tripal-table tripal-table-vert">
      <tr class="tripal_feature-table-even-row tripal-table-even-row">
        <th>Name</th>
        <td><?php print $feature->name; ?></td>
      </tr>
      <!--<tr class="tripal_feature-table-even-row tripal-table-even-row">
        <th>Internal ID</th>
        <td><?php print $feature->feature_id; ?></td>
      </tr>-->
      <tr class="tripal_feature-table-odd-row tripal-table-odd-row">
        <th>Type</th>
        <td><?php /* print $feature->type_id->name;*/ print "Sequence"; ?></td>
      </tr>
      <tr class="tripal_feature-table-even-row tripal-table-even-row">
        <th>Organism</th>
        <td>
          <?php if ($feature->organism_id->nid) { 
      	   print "<a href=\"".url("node/".$feature->organism_id->nid)."\">".$feature->organism_id->genus ." " . $feature->organism_id->species . "</a>";      	 
          } else { 
            print $feature->organism_id->genus ." " . $feature->organism_id->species;
          } ?>
        </td>
     	</tr> <?php   
     	if ($feature->seqlen) { ?>
        <tr class="tripal_feature-table-even-row tripal-table-even-row">
          <th>Length</th>
          <td><?php print $feature->seqlen ?></td>
        </tr> <?php 
     	} ?>   	                                
   </table>
   <?php 
if ($feature->residues) { ?>
      <div id="mainlab_tripal-sequence"><pre id="tripal_feature-sequence-residues"><?php 
      // format the sequence to break ever 100 residues
      print ereg_replace("(.{60})","\\1<br>",$feature->residues); ?>  
    </pre></div>
<?php
}
?>
</div>


<style type="text/css">
#tripal_feature-base-box {
   padding-bottom:20px;
}
#tripal_feature-sequence-residues {
   border:none;
   padding:none;
}
#mainlab_tripal-sequence {
   margin-top: 28px;
   padding-left:80px;
   border:1px solid #DDDDDD;
}
</style>

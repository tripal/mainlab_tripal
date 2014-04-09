<?php

$feature  = $variables['node']->feature;
if (!$feature->name) {
  $feature->name = $feature->uniquename; // show uniquname if there is no name
}
$feature = tripal_core_expand_chado_vars($feature,'table','featureprop');

// get marker properties
$properties = $feature->featureprop;
if (!$properties) {
	$properties = array();
} elseif (!is_array($properties)) {
	$properties = array($properties);
}
$kv_properties = array();
foreach($properties AS $property) {
   if ($kv_properties[$property->type_id->name]) {
     $kv_properties[$property->type_id->name] = $kv_properties[$property->type_id->name] . "<br>" . $property->value;
   } else {
     $kv_properties[$property->type_id->name] = $property->value;
   }
}

// get genbank accession
$feature = tripal_core_expand_chado_vars($feature,'table','feature_dbxref');
if ($feature->feature_dbxref->dbxref_id->db_id->name == 'DB:genbank') {
  $accession = $feature->feature_dbxref->dbxref_id->accession;
}

// get germplasm
$fstock = tripal_core_expand_chado_vars($feature,'table','feature_stock');
$stock = $fstock->feature_stock->stock_id->uniquename;
$stock_nid = $fstock->feature_stock->stock_id->nid;

// get sequence & primer
$f_rel = tripal_core_expand_chado_vars($feature,'table','feature_relationship');
$objs = $f_rel->feature_relationship->object_id;

$seqs = array();
if ($objs) {
  if (!is_array($objs)) {
    $tmp = $objs;
    $objs = array();
    array_push($objs, $tmp);
  }
	foreach ($objs AS $obj) {
		if ($obj->type_id->name == 'sequence_of') {
			$seq = $obj->subject_id;
			array_push($seqs, $seq);
		}
	}
}

// get primers and associated with 
$subjs = $f_rel->feature_relationship->subject_id;
$primers = array();
$assoc_with = array();
if ($subjs) {
  foreach ($subjs AS $subj) {
    if ($subj->type_id->name == 'adjacent_to') {
      array_push($primers, $subj->object_id);
    }
    if ($subj->type_id->name == 'associated_with') {
      array_push($assoc_with, $subj->object_id);
    }
  }
}

// expand feature to include polymorphism
$feature = tripal_core_expand_chado_vars($feature, 'table', 'feature_genotype');
$polymorphism = $feature->feature_genotype->feature_id;

// expand feature to include pubs
$feature = tripal_core_expand_chado_vars($feature, 'table', 'feature_pub');
$feature = tripal_core_expand_chado_vars($feature, 'table', 'feature_pub');
$pubs = $feature->feature_pub;

// get contact
$feature = tripal_core_expand_chado_vars($feature, 'field', 'pub.title');
$contacts = $feature->feature_contact;

// Define function to get table row class
function genetic_markerGetTableRowClass($c) {
	if ($c % 2 == 1) {
		$class = "tripal_feature-table-even-row tripal-table-even-row";
	} else {
		$class = "tripal_feature-table-odd-row tripal-table-odd-row";
	}
	return $class;
}
$counter = 0;
?>
<script type="text/javascript">
function showPolymorphism () {
	$(".tripal-info-box").hide();
	$("#tripal_feature-genetic_marker_polymorphism-box").fadeIn('slow');
	$("#tripal_feature_toc").height($("#tripal_feature-genetic_marker_polymorphism-box").parent().height());
	return false;	
}
</script>
<div id="tripal_feature-base-box" class="tripal_feature-info-box tripal-info-box">
  <div class="tripal_feature-info-box-title tripal-info-box-title">Marker Details</div>
  <div class="tripal_feature-info-box-desc tripal-info-box-desc"></div>

   <?php if(strcmp($feature->is_obsolete,'t')==0){ ?>
      <div class="tripal_feature-obsolete">This feature is obsolete</div>
   <?php }?>
   <table id="tripal_feature-base-table" class="tripal_feature-table tripal-table tripal-table-vert">
      <!-- Name -->
      <tr class="tripal_feature-table-even-row tripal-table-even-row">
        <th style="width:40%;">Name</th>
        <td><?php print $feature->name; ?></td>
      </tr>
      <!-- Alias -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Alias</th><td>"; if (key_exists('alias', $kv_properties)) { print $kv_properties['alias'];} else { print "N/A";} print "</td></tr>"; $counter ++;?>
      <!-- Genbank ID -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Genbank ID</th><td>"; if ($accession && $accession != 'GDR_markers') { print "<a href=\"http://www.ncbi.nlm.nih.gov/nuccore/$accession\" target=\"_blank\">" . $accession . "</a>";} else { print "N/A";} print "</td></tr>"; $counter ++;?>
      <!-- Type -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Type</th><td>"; if (key_exists('marker_type', $kv_properties)) { print $kv_properties['marker_type'];} else { print "N/A";} print "</td></tr>"; $counter ++;?>
      <!-- Species -->
      <?php $class = genetic_markerGetTableRowClass($counter); $counter ++?>
      <tr class="<?php print $class?>">
        <th>Species</th>
        <td>
          <?php if ($feature->organism_id->nid) { 
      	   print "<a href=\"".url("node/".$feature->organism_id->nid)."\">".$feature->organism_id->genus ." " . $feature->organism_id->species . "</a>";      	 
          } else { 
            print $feature->organism_id->genus ." " . $feature->organism_id->species;
          } ?>
        </td>
     	</tr> 
     	<!-- Stock (or Germplasm) -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Germplasm</th><td>"; if ($stock) { print "<a href=\"/node/$stock_nid\">". $stock ."</a>";} else {print "N/A";} print "</td></tr>"; $counter ++;?>
     	<!-- Source Sequence -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Source Sequence</th><td>"; foreach ($seqs AS $seq) { print "<a href=\"/node/$seq->nid\">". $seq->name ."</a> "; }; if(count($seqs) == 0) {print "N/A";} print "</td></tr>"; $counter ++;?>
     	<!-- Source Type -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Source Type</th><td>"; if (key_exists('source', $kv_properties)) { print $kv_properties['source']; } else { print "N/A"; } print "</td></tr>"; $counter ++;?>
     	<!-- Repeat Motif -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Repeat Motif</th><td>"; if (key_exists('repeat_motif', $kv_properties)) {print $kv_properties['repeat_motif']; } else { print "N/A";} print "</td></tr>"; $counter ++;?>
     	<!-- PCR Condition -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>PCR Condition</th><td>"; if (key_exists('pcr_condition', $kv_properties)) { print $kv_properties['pcr_condition']; } else { print "N/A"; } print "</td></tr>"; $counter ++;?>
     	<!-- Primers -->
     	<?php 
     	   if (count($primers) == 0) {
            $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Primer</th><td>N/A</td></tr>"; $counter ++;
         } else {
     	      $no_primers = 1;
     	      foreach($primers AS $primer) {
               if ($primer->type_id->name == 'primer') {
     	            $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Primer $no_primers</th><td>" . $primer->name . ": " . $primer->residues ."</td></tr>"; $counter ++;
     	            $no_primers ++;
     	         }
     	      }
     	   }
     	?>
     	<!-- Product Length -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Product Length</th><td>"; if (key_exists('product_length', $kv_properties)) { print $kv_properties['product_length']; } else { print "N/A"; } print"</td></tr>"; $counter ++;?>
     	<!-- Max Length -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Max Length</th><td>"; if (key_exists('max_length', $kv_properties)) { print $kv_properties['max_length']; } else { print "N/A"; } print "</td></tr>"; $counter ++;?>
     	<!-- Restriction Enzyme -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Restriction Enzyme</th><td>"; if (key_exists('restriction_enzyme', $kv_properties)) { print $kv_properties['restriction_enzyme']; } else {print "N/A";} print"</td></tr>"; $counter ++;?>
     	<!-- Polymorphism -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Polymorphism</th><td>"; if ($polymorphism) { print "<a href=\"/polymorphism/$feature->feature_id\">P_ " . $feature->name . "</a>"; } else { print "N/A"; } print "</td></tr>"; $counter ++;?>
     	<!-- Map position (content dynamically inserted using javascript) -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Map position</th><td id=\"tripal-feature-genetic_marker-map_position\">N/A</td></tr>"; $counter ++;?>
      <!-- Publication -->
     	<?php 
         $class = genetic_markerGetTableRowClass($counter);
         $counter ++;
         print "<tr class=\"" . $class ."\">";
         print "<th nowrap>Publication</th><td>";
         if (is_array($pubs)) {
	         print "[<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-pub-box\">view all " . count($pubs) . "</a>]";
         } else {
           if ($pubs) {
              print "[<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-pub-box\">view</a>]<br>";
           } else {
              print "N/A";
           }
         }
      ?>
      <!-- Contact -->
      <?php 
         $class = genetic_markerGetTableRowClass($counter);
         $counter ++;
         print "<tr class=\"" . $class ."\">";
         print "<th nowrap>Contact</th><td>";
         if (is_array($contacts)) {
            foreach ($contacts AS $contact) {
	            print "<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-contact-box\">" . $contact->contact_id->name . "</a><br>";
            }
         } else {
            if ($contacts) {
               print "<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-contact-box\">" . $contacts->contact_id->name . "</a><br>";
            } else {
               print "N/A";
            }
         }
        ?>
        <!-- Associated With -->
        <?php
          if (count($assoc_with) == 0) {
            $class = genetic_markerGetTableRowClass($counter); 
            print "<tr class=\"" . $class ."\"><th>Associated With</th><td>N/A</td></tr>"; 
            $counter ++;
          } 
          else {
            $no_assoc = 1;
            $class = genetic_markerGetTableRowClass($counter); 
            print "<tr class=\"" . $class ."\"><th>Associated With</th><td>";
            foreach($assoc_with AS $assoc) {
              if ($assoc->nid) {
                print l($assoc->name, 'node/' . $assoc->nid);
              } 
              else {
                print $assoc->name . "<br>";
              }
              $no_assoc ++;
            }
            print "</td></tr>";
          }
        ?>
      <!-- Comment -->
     	<?php $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Comments</th><td>"; if (key_exists('comments', $kv_properties)) { print $kv_properties['comments'];} else { print "N/A";} print "</td></tr>"; $counter ++;?>

   </table>
</div>

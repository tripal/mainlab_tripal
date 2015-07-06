<?php

$feature  = $variables['node']->feature;
if (!$feature->name) {
  $feature->name = $feature->uniquename; // show uniquname if there is no name
}
$feature = chado_expand_var($feature, 'table', 'featureprop', array('return_array' => TRUE));

// get marker properties
$properties = $feature->featureprop;
$kv_properties = array();
$marker_type = '';
$snp = '';
foreach($properties as $property) {
  if ($property->type_id->name == "marker_type") {
    $marker_type = $property->value;
  }
  if ($property->type_id->name == "SNP") {
    $snp = $property->value;
  }
  if (key_exists($property->type_id->name, $kv_properties)) {
    $kv_properties[$property->type_id->name] = $kv_properties[$property->type_id->name] . "<br>" . $property->value;
  } 
  else {
    $kv_properties[$property->type_id->name] = $property->value;
  }
}

// get genbank accession
$feature = chado_expand_var($feature,'table','feature_dbxref');
if (is_object($feature->feature_dbxref) && $feature->feature_dbxref->dbxref_id->db_id->name == 'DB:genbank') {
  $accession = $feature->feature_dbxref->dbxref_id->accession;
}

// get dbSNP accession
$options = array(
  'return_array' => TRUE,
  'include_fk' => array(
    'dbxref_id' => array(
      'db_id' => TRUE
    ),
  ),
);
$feature = chado_expand_var($feature, 'table', 'feature_dbxref', $options);
$feature_dbxrefs = $feature->feature_dbxref;
if ($feature_dbxrefs) {
  foreach ($feature_dbxrefs as $feature_dbxref) {
    if ($feature_dbxref->dbxref_id->db_id->name == 'dbSNP') {
      $dbSNP_accession = $feature_dbxref->dbxref_id;
    }
    if ($feature_dbxref->dbxref_id->db_id->name == 'dbSNP:rs') {
      $dbSNPrs_accession = $feature_dbxref->dbxref_id;
    }
  }
}

// get germplasm
$fstock = chado_expand_var($feature,'table','feature_stock');
$stock = is_object($fstock->feature_stock) ? $fstock->feature_stock->stock_id->uniquename : NULL;
$stock_nid = is_object($fstock->feature_stock) ? $fstock->feature_stock->stock_id->nid : NULL;

// get source sequence & probes
$f_rel = chado_expand_var($feature,'table','feature_relationship');
$objs = $f_rel->feature_relationship->object_id;
$seqs = array();
$probes = array();
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
    if ($obj->type_id->name == 'associated_with') {
      $probes[$obj->subject_id->uniquename] = $obj->subject_id;
    }
  }
}

// reorder probes by allele order
$allele_order = explode('/', $snp);
if (count($allele_order) > 0) {
  $ordered_probes = array();
  $order_failed = false;
  foreach ($allele_order AS $ao) {
    $probe_key = $feature->uniquename . '_' . $ao;
    if (key_exists($probe_key, $probes)) {
      array_push($ordered_probes, $probes[$probe_key]);
    }
    else {
      $order_failed = true;
    }
  }
  if (!$order_failed) {
    $probes = $ordered_probes;
  }
}

// get primers and associated with 
$subjs = $f_rel->feature_relationship->subject_id;
$primers = array();
$assoc_with = array();
if ($subjs) {
  foreach ($subjs AS $subj) {
    if ($subj->type_id->name == 'adjacent_to') {
      $primers[$subj->object_id->uniquename] = $subj->object_id;
    }
    if ($subj->type_id->name == 'associated_with') {
      array_push($assoc_with, $subj->object_id);
    }
  }
}
ksort($primers);

// expand feature to include polymorphism
$feature = chado_expand_var($feature, 'table', 'feature_genotype');
$polymorphism = $feature->feature_genotype->feature_id;

// expand the feature to include polymorphic sesquence
$poly_seq = tripal_feature_get_property($feature->feature_id, 'polymorhpic_sequence', 'MAIN');

// expand feature to include pubs
$feature = chado_expand_var($feature, 'table', 'feature_pub');
$pubs = $feature->feature_pub;

// get contact
$feature = chado_expand_var($feature, 'field', 'pub.title');
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
$counter = 0; ?>

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
  <div class="tripal_feature-info-box-desc tripal-info-box-desc"></div> <?php 
  if(strcmp($feature->is_obsolete,'t')==0){ ?>
    <div class="tripal_feature-obsolete">This feature is obsolete</div> <?php 
  }?>
  <table id="tripal_feature-base-table" class="tripal_feature-table tripal-table tripal-table-vert">

    <!-- Name -->
    <tr class="tripal_feature-table-even-row tripal-table-even-row">
      <th style="width:40%;">Name</th>
      <td><?php print $feature->name; ?></td>
    </tr>

    <!-- Alias -->
    <tr class="<?php print genetic_markerGetTableRowClass($counter++) ?>">
      <th>Alias</th>
      <td> <?php
        if (key_exists('alias', $kv_properties)) { 
          print $kv_properties['alias'];
        } 
        else { 
          print "N/A";
        } ?>
      </td>
    </tr>

    <!-- Genbank ID --> <?php
    if ($marker_type != "SNP") { ?>
      <tr class="<?php print genetic_markerGetTableRowClass($counter++)?>">
        <th>Genbank ID</th>
        <td> <?php
          if ($accession && $accession != 'GDR_markers') {
            if ($feature->feature_dbxref->db_id->url) { ?>
              <a href="<?php print $feature->feature_dbxref->db_id->url . '/' . $accession ?>"><?php print $accession ?></a> <?php
            }
            else {
              print "$accession";
            } 
          } 
          else { 
            print "N/A";
          } ?>
        </td>
      </tr> <?
    } ?>

    <!-- dbSNP ID --> <?php 
    if ($marker_type == "SNP") { ?>
      <tr class="<?php print genetic_markerGetTableRowClass($counter++)?>">
        <th>dbSNP ID</th>
        <td> <?php
          if ($dbSNP_accession) { ?>
             <a href="<?print $dbSNP_accession->db_id->url . $dbSNP_accession->accession?>" target="_blank"><?php print $dbSNP_accession->accession ?></a> <?php
          } 
          if ($dbSNPrs_accession) { ?>
             <a href="<?print $dbSNPrs_accession->db_id->url . $dbSNPrs_accession->accession?>" target="_blank"><?php print $dbSNPrs_accession->accession ?></a> <?php
          }
          if (!$dbSNP_accession and !$dbSNPrs_accession ) { 
             print "N/A";
          } ?>
        </td>
      </tr> <?php
    }?>

    <!-- Type -->
    <tr class="<?php print genetic_markerGetTableRowClass($counter++) ?>">
      <th>Type</th>
      <td> <?php
        if (key_exists('marker_type', $kv_properties)) {
          print $kv_properties['marker_type'];
        }
        else {
          print "N/A";
        } ?>
      </td>
    </tr> 
 
    <!-- SNP --><?php
    if ($marker_type == "SNP") {  ?>
      <tr class="<?php print genetic_markerGetTableRowClass($counter++) ?>"> 
        <th>SNP Alleles</th>
        <td> <?php
          if ($snp) {
            print $snp;
          }
          else {
            print "N/A";
          } ?>
        </td>
      </tr>  <?php
    } ?>
    
    <!-- Probes --><?php
      if (count($probes) == 0) {
        $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Probe</th><td>N/A</td></tr>"; $counter ++;
      }
      else {
        $no_probes = 1;
        foreach($probes AS $probe) {
          if ($probe->type_id->name == 'probe') {
            $class = genetic_markerGetTableRowClass($counter);
            print "<tr class=\"" . $class ."\"><th>Probe $no_probes</th><td>" . $probe->uniquename . ": " . $probe->residues ."</td></tr>"; $counter ++;
            $no_probes ++;
          }
        }
      }?>

    <!-- Species --><?php
    $class = genetic_markerGetTableRowClass($counter); $counter ++?>
    <tr class="<?php print $class?>">
      <th>Species</th>
        <td><?php
          if ($feature->organism_id->nid) { 
            print "<a href=\"".url("node/".$feature->organism_id->nid)."\">".$feature->organism_id->genus ." " . $feature->organism_id->species . "</a>";
          }
          else { 
            print $feature->organism_id->genus ." " . $feature->organism_id->species;
          } ?>
        </td>
      </tr> 
       
      <!-- Stock (or Germplasm) --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Germplasm</th><td>";
      if ($stock) {
        print "<a href=\"/node/$stock_nid\">". $stock ."</a>";
      }
      else {
        print "N/A";
      }
      print "</td></tr>";
      $counter ++;?>
      
      <!-- Source Sequence --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Source Sequence</th><td>";
      foreach ($seqs AS $seq) {
        print "<a href=\"/node/$seq->nid\">". $seq->name ."</a> ";
      };
      if(count($seqs) == 0) {
        print "N/A";
      }
      print "</td></tr>";
      $counter ++;?>
      
      <!-- Source Type --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Source Type</th><td>";
      if (key_exists('source', $kv_properties)) {
        print $kv_properties['source'];
      }
      else {
        print "N/A";
      }
      print "</td></tr>";
      $counter ++;?>
      
      <!-- Repeat Motif --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Repeat Motif</th><td>";
      if (key_exists('repeat_motif', $kv_properties)) {
        print $kv_properties['repeat_motif'];
      }
      else {
        print "N/A";
      }
      print "</td></tr>";
      $counter ++;?>

      <!-- PCR Condition --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>PCR Condition</th><td>";
      if (key_exists('pcr_condition', $kv_properties)) {
        print $kv_properties['pcr_condition'];
      } 
      else {
        print "N/A";
      }
      print "</td></tr>";
      $counter ++;?>

      <!-- Primers --><?php
      if (count($primers) == 0) {
        $class = genetic_markerGetTableRowClass($counter); print "<tr class=\"" . $class ."\"><th>Primer</th><td>N/A</td></tr>"; $counter ++;
      }
      else {
        $no_primers = 1;
        foreach($primers AS $primer) {
          if ($primer->type_id->name == 'primer') {
            $class = genetic_markerGetTableRowClass($counter);
            print "<tr class=\"" . $class ."\"><th>Primer $no_primers</th><td>" . $primer->uniquename . ": " . $primer->residues ."</td></tr>"; $counter ++;
            $no_primers ++;
          }
        }
      }?>

      <!-- Product Length --><?php
       $class = genetic_markerGetTableRowClass($counter);
       print "<tr class=\"" . $class ."\"><th>Product Length</th><td>";
       if (key_exists('product_length', $kv_properties)) {
         print $kv_properties['product_length'];
       }
       else {
         print "N/A";
       }
       print"</td></tr>";
       $counter ++;?>

      <!-- Max Length --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Max Length</th><td>";
      if (key_exists('max_length', $kv_properties)) {
        print $kv_properties['max_length'];
      } 
      else {
        print "N/A";
      }
      print "</td></tr>"; $counter ++;?>

      <!-- Restriction Enzyme --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Restriction Enzyme</th><td>";
      if (key_exists('restriction_enzyme', $kv_properties)) {
        print $kv_properties['restriction_enzyme'];
      } else {
        print "N/A";
      }
      print"</td></tr>";
      $counter ++;?>

      <!-- Polymorphism --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Polymorphism</th><td>";
      if ($polymorphism) {
        print "<a href=\"/polymorphism/$feature->feature_id\">P_ " . $feature->name . "</a>";
      }
      else {
        print "N/A";
      }
      print "</td></tr>";
      $counter ++;?>

      <!-- Map position (content dynamically inserted using javascript) --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Map position</th><td id=\"tripal-feature-genetic_marker-map_position\">N/A</td></tr>";
      $counter ++;?>
      
      <!-- Publication --><?php 
      $class = genetic_markerGetTableRowClass($counter);
      $counter ++;
      print "<tr class=\"" . $class ."\">";
      print "<th nowrap>Publication</th><td>";
      if (is_array($pubs)) {
        print "[<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-pub-box\">view all " . count($pubs) . "</a>]";
      }
      else {
        if ($pubs) {
          print "[<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-pub-box\">view</a>]<br>";
        }
        else {
          print "N/A";
        }
      }?>

      <!-- Contact --><?php
      $class = genetic_markerGetTableRowClass($counter);
      $counter ++;
      print "<tr class=\"" . $class ."\">";
      print "<th nowrap>Contact</th><td>";
      if (is_array($contacts)) {
        foreach ($contacts AS $contact) {
          print "<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-contact-box\">" . $contact->contact_id->name . "</a><br>";
        }
      }
      else {
        if ($contacts) {
          print "<a class=\"tripal_feature_toc_item\" href=\"#tripal_feature-contact-box\">" . $contacts->contact_id->name . "</a><br>";
        }
        else {
          print "N/A";
        }
      }?>

      <!-- Associated With --><?php
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
      }?>

      <!-- Comment --><?php
      $class = genetic_markerGetTableRowClass($counter);
      print "<tr class=\"" . $class ."\"><th>Comments</th><td>";
      if (key_exists('comments', $kv_properties)) {
        print $kv_properties['comments'];
      }
      else {
        print "N/A";
      }
      print "</td></tr>";
      $counter ++;?>

   </table>
</div>

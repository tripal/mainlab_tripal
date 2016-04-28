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
$accession = '';
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
$dbSNP_accession = '';
$dbSNPrs_accession = '';
$feature_dbxrefs = !$feature_dbxrefs || is_array($feature_dbxrefs) ? $feature_dbxrefs : array($feature_dbxrefs);
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
$stock_nid = is_object($fstock->feature_stock) && property_exists($fstock->feature_stock->stock_id, 'nid') ? $fstock->feature_stock->stock_id->nid : NULL;

// get source sequence & probes
$f_rel = chado_expand_var($feature,'table','feature_relationship', array('return_array' => 1));
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
    if ($obj->type_id->name == 'derives_from') {
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
$feature = chado_expand_var($feature, 'table', 'feature_pub', array('return_array' => 1));
$pubs = $feature->feature_pub;

// get contact
$feature = chado_expand_var($feature, 'table', 'feature_contact', array('return_array' => 1));
$contacts = $feature->feature_contact;


$headers = array();
$rows = array();
// Name
$rows [] = array(array('data' => 'Name', 'header' => TRUE, 'width' => '20%'), $feature->name);
// Alias
if (key_exists('alias', $kv_properties)) {$rows [] = array(array('data' => 'Alias', 'header' => TRUE, 'width' => '20%'), key_exists('alias', $kv_properties) ? $kv_properties['alias'] : "N/A");}
//Genbank ID
if ($marker_type != "SNP") {
  $gbid = "N/A";
  $rows [] = array(array('data' => 'Genbank ID', 'header' => TRUE, 'width' => '20%'), $gbid);
}
//dbSNP ID
else {
  $dbsnp_id = "N/A";
  if ($dbSNP_accession) {
    $dbsnp_id = "<a href=\"" . $dbSNP_accession->db_id->url . $dbSNP_accession->accession . "\" target=\"_blank\">" . $dbSNP_accession->accession . "</a>";
  } 
  if ($dbSNPrs_accession) {
    $dbsnp_id = "<a href=\"" . $dbSNPrs_accession->db_id->url . $dbSNPrs_accession->accession. "\" target=\"_blank\">" .  $dbSNPrs_accession->accession . "</a>";
  }
  $rows [] = array(array('data' => 'dbSNP ID', 'header' => TRUE, 'width' => '20%'), $dbsnp_id);
}
$rows [] = array(array('data' => 'Type', 'header' => TRUE, 'width' => '20%'), key_exists('marker_type', $kv_properties) ? $kv_properties['marker_type'] : "N/A");
if ($marker_type == "SNP") {
  $rows [] = array(array('data' => 'SNP Alleles', 'header' => TRUE, 'width' => '20%'), $snp ? $snp : "N/A");  
}
// Probes
$probename = "N/A";
if (count($probes) > 0) {
  $no_probes = 1;
  foreach($probes AS $probe) {
    if ($probe->type_id->name == 'probe') {
      $probe = chado_expand_var($probe, 'field', 'feature.residues');
      $probename = mainlab_tripal_get_site() == 'cottongen' ? $probe->name : $probe->uniquename;
      $rows [] = array(array('data' => "Probe $no_probes", 'header' => TRUE, 'width' => '20%'), $probename . ": " . $probe->residues);
      $no_probes ++;
    }
  }
} else {
  //$rows [] = array(array('data' => 'Probe', 'header' => TRUE, 'width' => '20%'), "N/A");
}
// Species
$rows [] = array(array('data' => 'Species', 'header' => TRUE, 'width' => '20%'), $feature->organism_id->nid ? "<a href=\"".url("node/".$feature->organism_id->nid)."\">".$feature->organism_id->genus ." " . $feature->organism_id->species . "</a>" : $feature->organism_id->genus ." " . $feature->organism_id->species);
// Germplasm
if ($stock) {$rows [] = array(array('data' => 'Germplasm', 'header' => TRUE, 'width' => '20%'), $stock ? "<a href=\"/node/$stock_nid\">". $stock ."</a>" : "N/A");}
// Source Sequence
$srcseq = "";
foreach ($seqs AS $seq) {
  if (property_exists($seq, 'nid')) {
    $srcseq .= "<a href=\"/node/$seq->nid\">". $seq->name ."</a><br>";
  }
  else {
    $srcseq .= $seq->name . "<br>";
  }
}
if (count($seqs) == 0) {
  $srcseq = "N/A";
}
if ($srcseq != 'N/A') {$rows [] = array(array('data' => 'Source Sequence', 'header' => TRUE, 'width' => '20%'), $srcseq);}
// Source Type
if (key_exists('source', $kv_properties)) {$rows [] = array(array('data' => 'Source Type', 'header' => TRUE, 'width' => '20%'), key_exists('source', $kv_properties) ? $kv_properties['source'] : "N/A");}
// Repeat Motif
if (key_exists('repeat_motif', $kv_properties)) {$rows [] = array(array('data' => 'Repeat Motif', 'header' => TRUE, 'width' => '20%'), key_exists('repeat_motif', $kv_properties) ? $kv_properties['repeat_motif'] : "N/A");}
// PCR Condition
if (key_exists('pcr_condition', $kv_properties)) {$rows [] = array(array('data' => 'PCR Condition', 'header' => TRUE, 'width' => '20%'), key_exists('pcr_condition', $kv_properties) ? $kv_properties['pcr_condition'] : "N/A");}
// Primers
if (count($primers) > 0) {
  $no_primers = 1;
  foreach($primers AS $primer) {
    $primer = chado_expand_var($primer, 'field', 'feature.residues');
    if ($primer->type_id->name == 'primer') {
      $primername = mainlab_tripal_get_site() == 'cottongen' ? $primer->name ? $primer->name : $primer->uniquename : $primer->uniquename;
      $rows [] = array(array('data' => "Primer $no_primers", 'header' => TRUE, 'width' => '20%'), $primername . ": " . $primer->residues);
      $no_primers ++;
    }
  }
}
// Product Length
if (key_exists('product_length', $kv_properties)) {$rows [] = array(array('data' => 'Product Length', 'header' => TRUE, 'width' => '20%'), key_exists('product_length', $kv_properties) ? $kv_properties['product_length'] : "N/A");}
// Max Length
if (key_exists('max_length', $kv_properties)) {$rows [] = array(array('data' => 'Max Length', 'header' => TRUE, 'width' => '20%'), key_exists('max_length', $kv_properties) ? $kv_properties['max_length'] : "N/A");}
// Restriction Enzyme
if (key_exists('restriction_enzyme', $kv_properties)) {$rows [] = array(array('data' => 'Restriction Enzyme', 'header' => TRUE, 'width' => '20%'), key_exists('restriction_enzyme', $kv_properties) ? $kv_properties['restriction_enzyme'] : "N/A");}
// Polymorphism
if ($polymorphism) {$rows [] = array(array('data' => 'Polymorphism', 'header' => TRUE, 'width' => '20%'), $polymorphism ? "<a href=\"/polymorphism/$feature->feature_id\">P_ " . $feature->name . "</a>" : "N/A");}
// Publication
if ($pubs) {$rows [] = array(array('data' => 'Publication', 'header' => TRUE, 'width' => '20%'), $pubs ? "[<a href=\"?pane=publications\">view all</a>]" : "N/A");}
// Contact
$data_contact = "";
if (is_array($contacts)) {
  foreach ($contacts AS $contact) {
    $data_contact .= "<a href=\"?pane=contact\">" . $contact->contact_id->name . "</a><br>";
  }
}
else {
  if ($contacts) {
    $data_contact = "<a href=\"?pane=contact\">" . $contacts->contact_id->name . "</a><br>";
  }
  else {
    $data_contact = "N/A";
  }
}
if ($data_contact != 'N/A') {$rows [] = array(array('data' => 'Contact', 'header' => TRUE, 'width' => '20%'), $data_contact);}
// Associated With
$data_assoc_with = "";
if (count($assoc_with) == 0) {
  $data_assoc_with = "N/A";
}
else {
  $no_assoc = 1;
  foreach($assoc_with AS $assoc) {
    if ($assoc->nid) {
      $data_assoc_with = l($assoc->name, 'node/' . $assoc->nid);
    }
    else {
      $data_assoc_with .= $assoc->name . "<br>";
    }
    $no_assoc ++;
  }
}
if ($data_assoc_with != 'N/A') {$rows [] = array(array('data' => 'Associated With', 'header' => TRUE, 'width' => '20%'), $data_assoc_with);}
// Comment
if (key_exists('comments', $kv_properties)) {$rows [] = array(array('data' => 'Comment', 'header' => TRUE, 'width' => '20%'), key_exists('comments', $kv_properties) ? $kv_properties['comments'] : "N/A");}
// allow site admins to see the feature ID
if (user_access('view ids')) {
  $rows[] = array(array('data' => 'Feature ID', 'header' => TRUE, 'class' => 'tripal-site-admin-only-table-row'), array('data' => $feature->feature_id, 'class' => 'tripal-site-admin-only-table-row'));
}
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_feature_genetic_marker-table-base',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);
?>



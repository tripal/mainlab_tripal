<?php
/*
 * 
 */
function mainlab_tripal_seq_extract_page() {
  $_SESSION = $_SESSION ? $_SESSION : array();
  if (key_exists('mainlab_tripal_seq_extract', $_SESSION) && key_exists('download', $_SESSION['mainlab_tripal_seq_extract'])){   
    $analysis   = $_SESSION['mainlab_tripal_seq_extract']['analysis'];
    $ftype      = $_SESSION['mainlab_tripal_seq_extract']['ftype'];
    $fnames     = $_SESSION['mainlab_tripal_seq_extract']['fnames'];
    $upstream   = $_SESSION['mainlab_tripal_seq_extract']['upstream'];
    $downstream = $_SESSION['mainlab_tripal_seq_extract']['downstream'];

    unset($_SESSION['mainlab_tripal_seq_extract']['download']);
     
    drupal_add_http_header('Content-Type', 'text');
    drupal_add_http_header('Content-Disposition', 'attachment; filename="sequences.fasta.txt"');
    ini_set('max_execution_time', 0); // we don't want the download to time out
    
    mainlab_tripal_seq_extract_get_features($analysis, 
      $ftype, $fnames['items_array'], $upstream, $downstream);
      
    return;
  }
 
  
  // generate the search form 
  $output = '';
  $output .= "<div id=\"tripal-feature-seq-extract-form-block\">";
  $output .= drupal_render(drupal_get_form('mainlab_tripal_seq_extract_form'));  
  $output .= "</div>";
  return $output;
}
/*
 * 
 */
function theme_mainlab_tripal_seq_extract_form($form) {
  $rows = array(
    0 => array(
      array('data' => drupal_render($form['description']), 'colspan' => '2'),
    ),
    1 => array(
      array('data' => drupal_render($form['analysis']), 'colspan' => '2'),
    ),
    2 => array(     
      array('data' => drupal_render($form['fnames']), 'colspan' => '2'),
    ),
    3 => array(
      drupal_render($form['upstream']),
      drupal_render($form['downstream']) ,
    ),
    4 => array(
      array('data' => drupal_render($form['retrieve_btn']) . drupal_render($form['reset_btn']), 'colspan' => '2'),
    ),
  );
  $headers = array();
  $table = theme('table', $headers, $rows, array('id' => 'mainlab-feature-seq-extract-form-table', 'border' => '0'));   
    
  $markup = $table;
  $form['criteria'] = array(
    '#type' => 'markup',
    '#value' =>  $markup,
    '#weight' => -10,
  );
  return drupal_render($form);
}
/**
*
 */
function mainlab_tripal_seq_extract_form($form, &$form_state) {   
  //tripal_core_ahah_init_form();
  
  // we want to allow the query string to provide values for the form
  if (key_exists('fnames', $_GET)) {
    $form_state['values']['fnames']['items'] = $_GET['fnames'];  
  }
  if (key_exists('ftype', $_GET)) {
    $form_state['values']['ftype'] = $_GET['ftype'];  
  }
  if (key_exists('analysis', $_GET)) {
    $form_state['values']['analysis'] = $_GET['analysis'];  
  }
  if (key_exists('upstream', $_GET)) {
    $form_state['values']['upstream'] = $_GET['upstream'];  
  }
  if (key_exists('downstream', $_GET)) {
    $form_state['values']['downstream'] = $_GET['downstream'];  
  }
  if (key_exists('clear', $_GET)) {
    unset($_SESSION['mainlab_tripal_seq_extract']);
  }

  // get defaults
  $danalysis   = key_exists('value', $form_state) && isset($form_state['values']['analysis'])   ? $form_state['values']['analysis']   : NULL;
  $dftype      = key_exists('value', $form_state) &&isset($form_state['values']['ftype'])      ? $form_state['values']['ftype']      : NULL;
  $dfnames     = key_exists('value', $form_state) &&isset($form_state['values']['fnames'])     ? $form_state['values']['fnames']     : NULL;
  $dupstream   = key_exists('value', $form_state) &&isset($form_state['values']['upstream'])   ? $form_state['values']['upstream']   : NULL;
  $ddownstream = key_exists('value', $form_state) &&isset($form_state['values']['downstream']) ? $form_state['values']['downstream'] : NULL;

  if (key_exists('mainlab_tripal_seq_extract', $_SESSION)) {
    $danalysis   = NULL && key_exists('analysis', $_SESSION['mainlab_tripal_seq_extract'])  ? $form_state['values']['analysis']   : $_SESSION['mainlab_tripal_seq_extract']['analysis'];
    $dftype      = NULL && key_exists('ftype', $_SESSION['mainlab_tripal_seq_extract']) ? $form_state['values']['ftype']      : $_SESSION['mainlab_tripal_seq_extract']['ftype'];
    $dfnames     = NULL && key_exists('fnames', $_SESSION['mainlab_tripal_seq_extract']) ? $form_state['values']['fnames']     : $_SESSION['mainlab_tripal_seq_extract']['fnames'];
    $dupstream   = NULL && key_exists('upstream', $_SESSION['mainlab_tripal_seq_extract']) ? $form_state['values']['upstream']   : $_SESSION['mainlab_tripal_seq_extract']['upstream'];
    $ddownstream = NULL && key_exists('downstream', $_SESSION['mainlab_tripal_seq_extract']) ? $form_state['values']['downstream'] : $_SESSION['mainlab_tripal_seq_extract']['downstream'];
  }
  
  $form = array();
  
  // because we're using Tripal's file_upload_combo form element we
  // need to allow the form to upload files
  $form['#attributes']['enctype'] = 'multipart/form-data';
  $form['#method'] = 'POST';
    
  $form['description'] = array(
    '#type' => 'markup',
    '#value' => t('Use this form to retrieve sequences in FASTA format.')
  );
  
  $analyses = array();
  $analyses[] = '';
  global $base_url;
  $sql = "
    SELECT DISTINCT A.analysis_id, A.name
    FROM {analysis} A
      INNER JOIN {analysisprop} AP ON A.analysis_id = AP.analysis_id
      INNER JOIN {cvterm} CVT on CVT.cvterm_id = AP.type_id
    WHERE CVT.name = 'Analysis Type' and 
      (AP.value = 'whole_genome' or AP.value = 'bulk_data' or AP.value = 'unigene')
  ";
  $args = array();
  $sql .=" ORDER BY A.name ";
  $results = chado_query($sql, $args);  
  while ($analysis = $results->fetchObject()) {
    if (in_array($analysis->name, array('Fragaria vesca Whole Genome v1.1.a2  (Re-annotation of v1.1)','Fragaria vesca Whole Genome v2.0.a1 Assembly & Annotation', 'Malus x domestica Whole Genome v3.0.a1 Assembly & Annotation'))) {
      continue;
    }
    $analyses[$analysis->name] = $analysis->name;  
  }
  $form['instruction'] = array(
    '#markup' => 'Download nucleotide and protein sequences including chromosomes, 
    scaffolds, genes, mRNAs, transcript coding sequences, protein, reftrans contigs and 
    unigene contigs. For the sequences aligned to larger sequences, such as genes, mRNAs 
    and transcript coding sequences, a numeric value specifying the number of upstream 
    bases and downstream bases can be typed in the text boxes.' . 
    '<b> | <a href="https://www.youtube.com/watch?v=">Short video tutorial</a> | <a href="/tutorial/sequence_retrieval">Text tutorial</a> | <a href="/contact">Email us with problems and suggestions</a></b>'
    ,
    '#prefix' => '<div style="padding:10px 0px">',
    '#suffix' => '</div>'
  );
  $form['analysis'] = array(
    '#title'         => t('Dataset'),
    '#type'          => 'select',
    '#options'       => $analyses, 
    '#default_value' => $danalysis,    
    '#multiple'      => FALSE,
    '#description'  => t('You can limit sequences by the analyses to which it was derived or was used. If specified, only features associated with the specific analysis will be retrieved.'),
  );
  
/*   $ftype = array(
    '' => '',
    'CDS' => 'CDS',
    'chromosome' => 'chromosome',
    'contig' => 'contig',
    'EST'  => 'EST',
    'gene' => 'gene',
    'mRNA' => 'mRNA',
    //'genetic_marker' => 'genetic marker',
    'mapped_genetic_marker' => 'aligned genetic marker',
    'polypeptide'     => 'protein',
    'supercontig'    => 'scaffold',
  );
  $form['ftype'] = array(
    '#title'         => t('Sequence Type'),
    '#type'          => 'select',
    '#options'       => $ftype,     
    '#multiple'      => FALSE,
    '#default_value' => $dftype,
    '#description'   => t('The type of feature to retrieve (e.g. mRNA). All features that match this type will be retrieved.'),
  ); */
  
  $form['fnames'] = array(
    '#title'         => t('Sequence Name'),
    '#type'          => 'file_upload_combo',
    '#default_value' => $dfnames,
    '#description'   => t('The names of the features to retrieve. Separate each with a new line or comma. Leave blank to retrieve all features matching other criteria.'),
    '#rows'          => 8
  );
  $form['upstream'] = array(
    '#title'         => t('Upstream Bases'),
    '#type'          => 'textfield',
    '#description'   => t('A numeric value specifying the number of upstream bases to include. Only works if the feature is aligned to a larger sequence.'),
    '#default_value' => $dupstream,
    '#size'          => 5,
  );
  $form['downstream'] = array(
    '#title'         => t('Downstream Bases'),
    '#type'          => 'textfield',
    '#description'   => t('A numeric value specifying the number of downstream bases to incldue.  Only works if the feature is aligned to a larger sequence.'),
    '#default_value' => $ddownstream,
    '#size'          => 5,
  );
  $form['retrieve_btn'] = array(
    '#type' => 'submit',
    '#value' => 'Retrieve',
  );
  $form['reset_btn'] = array(
    '#type' => 'submit',
    '#value' => 'Reset',
  );
  return $form;
}

/**
 * 
 */
function mainlab_tripal_seq_extract_form_validate($form, &$form_state) {
  $analysis   = $form_state['values']['analysis'];
  $ftype      = $form_state['values']['ftype'];
  $fnames     = $form_state['values']['fnames'];
  $upstream   = $form_state['values']['upstream'];
  $downstream = $form_state['values']['downstream'];
  $sub_features = array();
  $op         = $form_state['values']['op'];
  
  if ($op == 'Retrieve') {
    if ($upstream and !preg_match('/^\d+$/', $upstream)) {
      form_set_error('upstream', 'Please enter a positive numeric value for the upstream bases');
    }
     if ($downstream and !preg_match('/^\d+$/', $downstream)) {
      form_set_error('downstream', 'Please enter a positive numeric value for the downstream bases');
    }
    if (!$ftype and !$fnames and !$analysis) {
      form_set_error('', 'Please provide a feature name, source or a feature type.');
    }
  }
  if ($ftype == 'polypeptide' and $upstream) {
    form_set_error('upstream', 'When selecting a protein the upstream value should not be set');
  }
  if ($ftype == 'polypeptide' and $downstream) {
    form_set_error('upstream', 'When selecting a protein the downstream value should not be set');
  }
}

/**
 * 
 */
function mainlab_tripal_seq_extract_form_submit($form, &$form_state) {
  $analysis   = $form_state['values']['analysis'];
  $ftype      = $form_state['values']['ftype'];
  $fnames     = $form_state['values']['fnames'];
  $upstream   = $form_state['values']['upstream'];
  $downstream = $form_state['values']['downstream'];
  $op         = $form_state['values']['op'];

  if ($op == 'Retrieve') {
    $_SESSION['mainlab_tripal_seq_extract']['analysis']   = $analysis;
    $_SESSION['mainlab_tripal_seq_extract']['ftype']      = $ftype;
    $_SESSION['mainlab_tripal_seq_extract']['fnames']     = $fnames;
    $_SESSION['mainlab_tripal_seq_extract']['upstream']   = $upstream;
    $_SESSION['mainlab_tripal_seq_extract']['downstream'] = $downstream;
    $_SESSION['mainlab_tripal_seq_extract']['download']   = 1;
  }
  if ($op == 'Reset') {
    unset($_SESSION['mainlab_tripal_seq_extract']);
  }
}

/*
 * 
 */
function mainlab_tripal_seq_extract_get_features($analysis_name, 
  $type, $feature_name, $upstream, $downstream) {

  $derive_from_parent = 0;
  $aggregate = 0;
  $sub_features = array();
  $relationship = '';
  $rel_part = '';
    
  // get the list of features
  $vars = array();
  $sql  = "SELECT DISTINCT F.feature_id, F.name, F.uniquename, O.genus, O.species, CVT.name as feature_type ".
          "FROM {feature} F ".
          "  INNER JOIN {organism} O on O.organism_id = F.organism_id ".
          "  INNER JOIN {cvterm} CVT on CVT.cvterm_id = F.type_id ";
  if ($analysis_name) {
     $sql .= "  INNER JOIN {analysisfeature} AF on AF.feature_id = F.feature_id ".
             "  INNER JOIN {analysis} A on AF.analysis_id = A.analysis_id ";
  }         
  $sql .= "WHERE (1=1) ";
  if ($type) {
    if ($type == 'CDS') {
      $sql .= "AND CVT.name = :CDS ";
      $derive_from_parent = 1;
      $aggregate = 1;
      $sub_features = array('CDS');
      $type = 'mRNA';
      $vars[':CDS'] = $type;
    }
    elseif ($type == 'gene') {
      $sql .= "AND CVT.name = :gene ";
      $derive_from_parent = 1;
      $type = 'gene';
      $vars[':gene'] = $type;
    }
    elseif ($type == 'mapped_genetic_marker') {
      $sql .= "AND (CVT.name = :marker or CVT.name = 'genetic_marker') ";
      $derive_from_parent = 1;
      $type = 'match';
      $vars[':marker'] = $type;
    }
    elseif ($type == 'genetic_marker') {
      $sql .= "AND CVT.name = :genetic_marker ";
      $relationship = 'sequence_of';
      $rel_part = 'object';
      $vars[':genetic_marker'] = $type;
      $upstream = 0;
      $downstream = 0;
    }
    elseif ($type == 'polypeptide') {
      $sql .= "AND CVT.name = :polypeptide ";
      $derive_from_parent = 0;
      $vars[':polypeptide'] = $type;
      $upstream = 0;
      $downstream = 0;
    }
    else {
      $sql .= "AND CVT.name = :type ";
      $vars[':type'] = $type;
    }
  }
  if ($feature_name) {
    if (is_array($feature_name)) {
      $sql .= "AND F.name IN (";
      foreach ($feature_name as $i => $fname) {
        $sql .= ":feature$i, ";
        $vars[":feature$i"] = $fname;
      } 
      // remove the trailing comma and close the paren
      $sql = substr($sql, 0, -2) . ")";
    }
    else {
      $sql .= "AND F.name = :featurename";
      $vars[':featurename'] = $feature_name;
    }
  }
  if ($analysis_name) {
    $sql .= "AND A.name = :analysis";
    $vars[':analysis'] = $analysis_name;
  }
  $num_bases_per_line = 50;
  $num_seqs = 0;
  $q = chado_query($sql, $vars);
 
  while ($feature = $q->fetchObject()) {
    $feature_id = $feature->feature_id;
    
    // build the header for each FASTA entry
    $feature_name = $feature->name;
      
    // generate the sequence
    if ($upstream > 0 or $downstream > 0) {
      $derive_from_parent = 1;
    }
    $sequences = tripal_get_feature_sequences(array('feature_id' => $feature_id, 'name' => $feature_name), 
      array('width' => $num_bases_per_line, 'derive_from_parent' => $derive_from_parent, 'aggregate' => $aggregate,
      'upstream' => $upstream, 'downstream' => $downstream, 'sub_feature_types' => $sub_features, 'relationship_type' => $relationship, 'relationship_part' => $rel_part));
    
    // print the sequence
    foreach ($sequences AS $sequence) {
      print '>' . $sequence['defline'] . "\n";
      print $sequence['residues'] . "\n";
    }
    $num_seqs++;
  }
  if ($num_seqs == 0) {    
    print "No Sequences Found";
  }
}


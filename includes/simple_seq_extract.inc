<?php
/*
 * 
 */
function mainlab_tripal_seq_extract_page() {
  
  if ($_SESSION['mainlab_tripal_seq_extract']['download']){   
    $analysis   = $_SESSION['mainlab_tripal_seq_extract']['analysis'];
    $ftype      = $_SESSION['mainlab_tripal_seq_extract']['ftype'];
    $fnames     = $_SESSION['mainlab_tripal_seq_extract']['fnames'];
    $upstream   = $_SESSION['mainlab_tripal_seq_extract']['upstream'];
    $downstream = $_SESSION['mainlab_tripal_seq_extract']['downstream'];

    unset($_SESSION['mainlab_tripal_seq_extract']['download']);
     
    drupal_set_header('Content-Type: text');
    drupal_set_header('Content-Disposition: attachment; filename="sequences.fasta.txt"');
    ini_set('max_execution_time', 0); // we don't want the download to time out
    
    mainlab_tripal_seq_extract_get_features($analysis, 
      $ftype, $fnames['items_array'], $upstream, $downstream);
      
    return;
  }
 
  
  // generate the search form 
  $output .= '';
  $output .= "<div id=\"tripal-feature-seq-extract-form-block\">";
  $output .= drupal_get_form('mainlab_tripal_seq_extract_form');  
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
    
  $markup .= $table;
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
function mainlab_tripal_seq_extract_form(&$form_state = NULL) {   
  tripal_core_ahah_init_form();
  
  // we want to allow the query string to provide values for the form
  if ($_GET['fnames']) {
    $form_state['values']['fnames']['items'] = $_GET['fnames'];  
  }
  if ($_GET['ftype']) {
    $form_state['values']['ftype'] = $_GET['ftype'];  
  }
  if ($_GET['analysis']) {
    $form_state['values']['analysis'] = $_GET['analysis'];  
  }
  if ($_GET['upstream']) {
    $form_state['values']['upstream'] = $_GET['upstream'];  
  }
  if ($_GET['downstream']) {
    $form_state['values']['downstream'] = $_GET['downstream'];  
  }

  // get defaults
  $danalysis   = isset($form_state['values']['analysis'])   ? $form_state['values']['analysis']   : $_SESSION['mainlab_tripal_seq_extract']['analysis'];
  $dftype      = isset($form_state['values']['ftype'])      ? $form_state['values']['ftype']      : $_SESSION['mainlab_tripal_seq_extract']['ftype'];
  $dfnames     = isset($form_state['values']['fnames'])     ? $form_state['values']['fnames']     : $_SESSION['mainlab_tripal_seq_extract']['fnames'];
  $dupstream   = isset($form_state['values']['upstream'])   ? $form_state['values']['upstream']   : $_SESSION['mainlab_tripal_seq_extract']['upstream'];
  $ddownstream = isset($form_state['values']['downstream']) ? $form_state['values']['downstream'] : $_SESSION['mainlab_tripal_seq_extract']['downstream'];

  
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
  $sql = "
    SELECT DISTINCT A.analysis_id, A.name
    FROM {analysis} A
      INNER JOIN {analysisprop} AP ON A.analysis_id = AP.analysis_id
      INNER JOIN {cvterm} CVT on CVT.cvterm_id = AP.type_id
    WHERE CVT.name = 'analysis_type' and AP.value = 'whole_genome'
      
  ";
  $args = array();
  $sql .=" ORDER BY A.name ";
  $results = chado_query($sql, $args);  
  while ($analysis = db_fetch_object($results)) {
    $analyses[$analysis->name] = $analysis->name;  
  }
  $form['analysis'] = array(
    '#title'         => t('Analyses'),
    '#type'          => 'select',
    '#options'       => $analyses, 
    '#default_value' => $danalysis,    
    '#multiple'      => FALSE,
    '#description'  => t('You can limit sequences by the analyses to which it was derived or was used. If specified, only features associated with the specific analysis will be retrieved.'),
  );
  
  $ftype = array(
    '' => '',
    'CDS' => 'CDS',
    'chromosome' => 'chromosome',
    'contig' => 'contig',
    'EST'  => 'EST',
    'gene' => 'gene',
    'mRNA' => 'mRNA',
    'genetic_marker' => 'genetic marker, original sequence',
    'mapped_genetic_marker' => 'genetic marker, aligned sequence',
    'polypeptide' => 'protein',
    'scaffold'  => 'scaffold',
  );
  $form['ftype'] = array(
    '#title'         => t('Feature Type'),
    '#type'          => 'select',
    '#options'       => $ftype,     
    '#multiple'      => FALSE,
    '#default_value' => $dftype,
    '#description'   => t('The type of feature to retrieve (e.g. mRNA). All features that match this type will be retrieved.'),
  );
  
  $form['fnames'] = array(
    '#title'         => t('Feature Name'),
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
    $sql .= "AND CVT.name = '%s' ";
    if ($type == 'CDS') {
      $derive_from_parent = 1;
      $aggregate = 1;
      $sub_features = array('CDS');
      $type = 'mRNA';
    }
    if ($type == 'mapped_genetic_marker') {
      $derive_from_parent = 1;
      $type = 'genetic_marker';
    }
    $vars[] = $type; 
  }
  if ($feature_name) {
    if (is_array($feature_name)) {
      $sql .= "AND F.name IN (";
      foreach ($feature_name as $i => $fname) {
        $sql .= "'%s', ";
        $vars[] = $fname;
      } 
      // remove the trailing comma and close the paren
      $sql = substr($sql, 0, -2) . ")";
    }
    else {
      $sql .= "AND F.name = '%s'";
      $vars[] = $feature_name;
    }
  }
  if ($analysis_name) {
    $sql .= "AND A.name = '%s'";
    $vars[] = $analysis_name;
  }
  $num_bases_per_line = 50;
  $num_seqs = 0;
  $q = chado_query($sql, $vars);
  
  while ($feature = db_fetch_object($q)) {
    
    $feature_id = $feature->feature_id;
    
    // build the header for each FASTA entry
    $feature_name = "$feature->uniquename $feature->name $feature->feature_type ($feature->genus $feature->species)";
      
    // generate the sequence
    if ($upstream > 0 or $downstream > 0) {
      $derive_from_parent = 1;
    }
    $sequence = tripal_feature_get_formatted_sequence($feature_id, $feature_name, 
      $num_bases_per_line, $derive_from_parent, $aggregate, 'fasta_txt',
      $upstream, $downstream, $sub_features);
    
    // print the sequence
    print $sequence;
    $num_seqs++;
  }
  if ($num_seqs == 0) {    
    print "No Sequences Found";
  }
}


<?php 

/**
 * @ingroup mainlab_feature
 */
function mainlab_tripal_preprocess_mainlab_feature_relationships(&$variables) {
  // we want to provide a new variable that contains the matched features.
  $feature = $variables['node']->feature;
  // do not generate relationships for chromosome/scaffolds
  if ($feature->type_id->name == 'chromosome' || $feature->type_id->name == 'supercontig') {
    return;
  }
  if (!property_exists($feature, 'all_relationships')) {
    $feature->all_relationships = mainlab_tripal_feature_get_feature_relationships($feature);
  }
}

function mainlab_tripal_feature_get_feature_relationships($feature) {
  // expand the feature object to include the feature relationships.
  $options = array(
    'return_array' => 1,
    'order_by' => array('rank' => 'ASC'),
    // we don't want to fully recurse we only need information about the
    // relationship type and the object and subject features (including feature type
    // and organism)
    'include_fk' => array(
      'type_id' => 1,
      'object_id' => array(
        'type_id' => 1,
        'organism_id' => 1
      ),
      'subject_id'  => array(
        'type_id' => 1,
        'organism_id' => 1
      ),
    ),
  );
  $feature = chado_expand_var($feature, 'table', 'feature_relationship', $options);
  
  // get the subject relationships
  $srelationships = $feature->feature_relationship->subject_id;
  $orelationships = $feature->feature_relationship->object_id;
  
  // prepare the SQL statement to get the featureloc for the
  // feature in the relationships.
  $flrels_sql = "
    SELECT
      FL.featureloc_id, F.name as srcfeature_name, FL.srcfeature_id,
      FL.feature_id, FL.fmin, FL.fmax, FL.strand, FL.phase
    FROM {featureloc} FL
      INNER JOIN {feature} F ON F.feature_id = FL.srcfeature_id
    WHERE FL.feature_id = :feature_id and FL.srcfeature_id = :srcfeature_id
  ";
  
  // combine both object and subject relationshisp into a single array
  $relationships = array();
  $relationships['object'] = array();
  $relationships['subject'] = array();
  
  // iterate through the object relationships
  if ($orelationships) {
    foreach ($orelationships as $relationship) {
      $rel = new stdClass();
      // get locations where the child feature and this feature overlap with the
      // same landmark feature.
      $rel->child_featurelocs = array();
      $rel->record = $relationship;
      
      // get the relationship and child types
      $rel_type = t(preg_replace('/_/', " ", $relationship->type_id->name));
      $child_type = $relationship->subject_id->type_id->name;
      
      // get the node id of the subject
      $sql = "SELECT nid FROM {chado_feature} WHERE feature_id = :feature_id";
      $n = db_query($sql, array(':feature_id' => $relationship->subject_id->feature_id))->fetchObject();
      if ($n) {
        $rel->record->nid = $n->nid;
      }
      
      if (!array_key_exists($rel_type, $relationships['object'])) {
        $relationships['object'][$rel_type] = array();
      }
      if (!array_key_exists($child_type, $relationships['object'][$rel_type])) {
        $relationships['object'][$rel_type][$child_type] = array();
      }
      $relationships['object'][$rel_type][$child_type][] = $rel;
    }
  }
  
  // now add in the subject relationships
  if ($srelationships) {
    foreach ($srelationships as $relationship) {
      $rel = new stdClass();
      // get locations where this feature overlaps with the parent
      $rel->parent_featurelocs = array();
      $rel->record = $relationship;
      $rel_type = t(preg_replace('/_/', " ", $relationship->type_id->name));
      $parent_type = $relationship->object_id->type_id->name;
      
      // get the node id of the subject
      $sql = "SELECT nid FROM {chado_feature} WHERE feature_id = :feature_id";
      $n = db_query($sql, array(':feature_id' => $relationship->object_id->feature_id))->fetchObject();
      if ($n) {
        $rel->record->nid = $n->nid;
      }
      
      if (!array_key_exists($rel_type, $relationships['subject'])) {
        $relationships['subject'][$rel_type] = array();
      }
      if (!array_key_exists($parent_type, $relationships['subject'][$rel_type])) {
        $relationships['subject'][$rel_type][$parent_type] = array();
      }
      $relationships['subject'][$rel_type][$parent_type][] = $rel;
    }
  }
  return $relationships;
}

/**
 * @ingroup mainlab_feature
 */
function mainlab_tripal_preprocess_mainlab_feature_alignments(&$variables) {
  $limit = 100;
  // we want to provide a new variable that contains the matched features.
  $feature = $variables['node']->feature;
  // do not generate alignments for chromosome/scaffold
  if ($feature->type_id->name == 'chromosome' || $feature->type_id->name == 'supercontig') {
    return;
  }
  $options = array(
    'return_array' => 1,
    'include_fk' => array(
      'srcfeature_id' => array(
        'type_id' => 1,
      ),
      'feature_id' => array(
        'type_id' => 1
      ),
    )
  );
  $feature = chado_expand_var($feature, 'table', 'featureloc', $options);
  
  // get alignments as child
  $cfeaturelocs = $feature->featureloc->feature_id;
  if (!$cfeaturelocs) {
    $cfeaturelocs = array();
  }
  // get alignment as parent
  $pfeaturelocs = $feature->featureloc->srcfeature_id;
  if (!$pfeaturelocs) {
    $pfeaturelocs = array();
  }
  
  // get matched alignments (those with an itermediate 'match' or 'EST_match', etc
  $mfeaturelocs = tripal_feature_get_matched_alignments($feature);
  $feature->matched_featurelocs = $mfeaturelocs;
  
  // combine all three alignments into a single array for printing together in
  // a single list
  $alignments = array();
  $counter = 0;
  foreach ($pfeaturelocs as $featureloc) {
    if ($counter > $limit) {
      break;
    }
    // if type is a 'match' then ignore it. We will handle those below
    if (preg_match('/(^match$|^.*?_match|match_part)$/', $featureloc->feature_id->type_id->name)) {
      continue;
    }
    $alignment = new stdClass();
    $alignment->record = $featureloc;
    $alignment->name = $featureloc->feature_id->name;
    $alignment->type = $featureloc->feature_id->type_id->name;
    $alignment->fmin = $featureloc->fmin;
    $alignment->fmax = $featureloc->fmax;
    $alignment->phase = $featureloc->phase;
    $alignment->strand = $featureloc->strand;
    $alignments[] = $alignment;
    if (property_exists($featureloc->feature_id, 'nid')) {
      $alignment->nid = $featureloc->feature_id->nid;
    }
    $counter ++;
  }
  $counter = 0;
  foreach ($cfeaturelocs as $featureloc) {
    if ($counter > $limit) {
      break;
    }
    // if type is a 'match' then ignore it. We will handle those below
    if (preg_match('/(^match$|^.*?_match|match_part)$/', $featureloc->feature_id->type_id->name)) {
      continue;
    }
    $alignment = new stdClass();
    $alignment->record = $featureloc;
    $alignment->name = $featureloc->srcfeature_id->name;
    $alignment->type = $featureloc->srcfeature_id->type_id->name;
    $alignment->fmin = $featureloc->fmin;
    $alignment->is_fmin_partial = $featureloc->is_fmin_partial;
    $alignment->fmax = $featureloc->fmax;
    $alignment->is_fmax_partial = $featureloc->is_fmax_partial;
    $alignment->phase = $featureloc->phase;
    $alignment->strand = $featureloc->strand;
    $alignments[] = $alignment;
    if (property_exists($featureloc->srcfeature_id, 'nid')) {
      $alignment->nid = $featureloc->srcfeature_id->nid;
    }
    $counter ++;
  }
  // in matching features, the left feature is always the feature
  // provided to this function.
  $counter = 0;
  foreach ($mfeaturelocs as $featureloc) {
    if ($counter > $limit) {
      break;
    }
    // get more information about the right feature
    $select = array('feature_id' => $featureloc->right_srcfeature_id);
    $rfeature = chado_generate_var('feature', $select);
    // now add to the list
    $alignment = new stdClass();
    $alignment->record = $featureloc;
    $alignment->right_feature = $rfeature;
    $alignment->name = $rfeature->name;
    $alignment->type = $rfeature->type_id->name;
    $alignment->fmin = $featureloc->left_fmin;
    $alignment->is_fmin_partial = $featureloc->left_is_fmin_partial;
    $alignment->fmax = $featureloc->left_fmax;
    $alignment->is_fmax_partial = $featureloc->left_is_fmax_partial;
    $alignment->phase = $featureloc->left_phase;
    $alignment->strand = $featureloc->left_strand;
    $alignment->right_fmin = $featureloc->right_fmin;
    $alignment->right_is_fmin_partial = $featureloc->right_is_fmin_partial;
    $alignment->right_fmax = $featureloc->right_fmax;
    $alignment->right_is_fmax_partial = $featureloc->right_is_fmax_partial;
    $alignment->right_phase = $featureloc->right_phase;
    $alignment->right_strand = $featureloc->right_strand;
    $alignments[] = $alignment;
    if (property_exists($rfeature, 'nid')) {
      $alignment->nid = $rfeature->nid;
    }
    $counter ++;
  }
  $feature->all_featurelocs = $alignments;
}


/**
 * Implements preprocess hook for the tripal_feature_unigenes template.
 */
function mainlab_tripal_preprocess_mainlab_feature_unigenes(&$variables) {
  if (!chado_table_exists('organism_unigene_mview')) {
      return;
  }
  $node = $variables['node'];
  $feature = $node->feature;
  
  // first get all the unigene analyses for this organism
  $sql = "
    SELECT * FROM {organism_unigene_mview} OUM
      INNER JOIN {analysis} A  ON A.analysis_id = OUM.analysis_id
    WHERE OUM.organism_id = :organism_id
    ORDER BY A.timeexecuted DESC
  ";
  $results = chado_query($sql, array(':organism_id' => $feature->organism_id->organism_id));
  
  // iterate through the unigenes and find those that use this feature
  $unigenes = array();
  $i=0;
  $sql = "SELECT nid FROM {chado_analysis} WHERE analysis_id = :analysis_id";
  while ($unigene = $results->fetchObject()) {
    $analysis_id = $unigene->analysis_id;
    
    // check if this feature is present in the unigene
    $values = array(
      'feature_id' => $feature->feature_id,
      'analysis_id' => $analysis_id,
    );
    $analysisfeature = chado_select_record('analysisfeature', array('*'), $values);
    
    // if the feature is present then get information about it
    if (sizeof($analysisfeature) > 0) {
      // see if there is a drupal node for this unigene
      $c_node = db_query($sql, array(':analysis_id' => $analysis_id))->fetchObject();
      if ($c_node) {
        $unigene->nid = $c_node->nid;
      }
      // add in the properties
      $unigene_name = chado_get_property(array('table' => 'analysis', 'id' => $analysis_id), array('type_name' => 'analysis_unigene_name', 'cv_name' => 'tripal'));
      $singlet = chado_get_property(array('table' => 'analysisfeature', 'id' => $analysisfeature[0]->analysisfeature_id), array('type_name' => 'singlet', 'cv_name' => 'tripal'));
      
      if ($unigene_name) {
        $unigene->unigene_name = $unigene_name->value;
      }
      if ($singlet) {
        $unigene->singlet = $singlet->value;
      }
      
      $unigenes[$i++] = $unigene;
    }
  }
  $tripal_anlaysis_unigene = new stdClass();
  $tripal_anlaysis_unigene->unigenes = $unigenes;
  $node->feature->tripal_analysis_unigene = $tripal_anlaysis_unigene;
}
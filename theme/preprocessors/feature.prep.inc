<?php 

/**
 * @ingroup mainlab_featuremap
 */
function mainlab_tripal_preprocess_mainlab_feature_alignments(&$variables) {
  // we want to provide a new variable that contains the matched features.
  $feature = $variables['node']->feature;
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
  foreach ($pfeaturelocs as $featureloc) {
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
  }
  foreach ($cfeaturelocs as $featureloc) {
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
  }
  // in matching features, the left feature is always the feature
  // provided to this function.
  foreach ($mfeaturelocs as $featureloc) {
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
  }
  $feature->all_featurelocs = $alignments;
}

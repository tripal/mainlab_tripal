<?php 

/**
 * @ingroup tripal_feature
 */
function mainlab_tripal_preprocess_mainlab_feature_haplotype_block_base(&$variables) {
  $feature = $variables['node']->feature;

  // Get stocks
  $sql = "SELECT S.*, CS.nid FROM {feature_genotype} FG
      INNER JOIN {nd_experiment_genotype} NEG ON NEG.genotype_id = FG.genotype_id
      INNER JOIN {nd_experiment_stock} NES ON NEG.nd_experiment_id = NES.nd_experiment_id
      INNER JOIN {stock} S ON S.stock_id = NES.stock_id
      LEFT JOIN chado_stock CS ON CS.stock_id = S.stock_id
      WHERE feature_id =:feature_id";
  $result = chado_query($sql, array(':feature_id' => $feature->feature_id));
  $stocks = array();
  while ($stock = $result->fetchObject()) {
    array_push($stocks, $stock);
  }
  $feature->stocks = $stocks;

  // Get haplotypes
  $sql =
  "SELECT
    (SELECT replace (uniquename, HB.uniquename || '_', '') FROM {feature} WHERE feature_id = FR.subject_id) AS haplotype,
    FR2P.value,
    (SELECT feature_id FROM {feature} WHERE feature_id = FR2.object_id) AS marker_feature_id,
    (SELECT nid FROM chado_feature WHERE feature_id = FR2.object_id) AS marker_nid,
    (SELECT uniquename FROM {feature} WHERE feature_id = FR2.object_id) AS marker
  FROM {feature} HB
  LEFT JOIN {feature_relationship} FR ON HB.feature_id = FR.object_id AND FR.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'variant_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))
  LEFT JOIN {feature_relationship} FR2 ON FR2.subject_id = FR.subject_id AND FR2.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'contains' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))
  LEFT JOIN {feature_relationshipprop} FR2P ON FR2.feature_relationship_id = FR2P.feature_relationship_id
  WHERE feature_id = :feature_id ORDER BY marker";
  $result = chado_query($sql, array(':feature_id' => $feature->feature_id));
  $markers = array();
  while ($record = $result->fetchObject()) {
    $marker_feature_id = $record->marker_feature_id;
    $marker = $record->marker;
    $marker_nid = $record->marker_nid;
    $h = $record->haplotype;
    $v = $record->value;
    // Exisiting marker, add haplotype
    if (array_key_exists($marker_feature_id, $markers)) {
      $oldm = $markers[$marker_feature_id];
      // If haplotype exists, add new value
      if (array_key_exists($h, $oldm->haplotypes)) {
        $oldv = $oldm->haplotypes[$h];
        $oldm->haplotypes[$h] = $oldv . '; ' . $v;
      } // create new haplotype
      else {
        $oldm->haplotypes[$h] = $v;
      }
      $markers[$marker_feature_id] = $oldm;
      // New marker, create haplotype
    }
    else {
      $m = new stdClass();
      $m->name = $marker;
      $m->nid = $marker_nid;
      $m->haplotypes = array ();
      $m->haplotypes [$h]= $v;
      $markers[$marker_feature_id] = $m;
    }
  }
  $feature->haplotypes = $markers;
}
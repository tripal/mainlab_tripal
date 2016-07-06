<?php 

/**
 * @ingroup mainlab_featuremap
 */
function mainlab_tripal_preprocess_mainlab_featuremap_base(&$variables) {
  $featuremap = $variables['node']->featuremap;
  $num_loci = chado_query(
      "SELECT count (F.uniquename)
        FROM {featurepos} FP
        INNER JOIN {feature} F ON F.feature_id = FP.feature_id
        WHERE F.type_id = (SELECT cvterm_id
                                            FROM {cvterm}
                                            WHERE name = 'marker_locus' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))
        AND featuremap_id = :featuremap_id", array(':featuremap_id' => $featuremap->featuremap_id))->fetchField();
  $num_lg = chado_query(
      "SELECT count (distinct F.uniquename)
        FROM {featurepos} FP
        INNER JOIN {feature} F ON F.feature_id = FP.map_feature_id
        WHERE F.type_id = (SELECT cvterm_id
                                            FROM {cvterm}
                                            WHERE name = 'linkage_group' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))
        AND featuremap_id = :featuremap_id", array('featuremap_id' => $featuremap->featuremap_id))->fetchField();
  $cmap_r = chado_query(
      "SELECT db.urlprefix, X.accession
       FROM {featuremap_dbxref} FD
       INNER JOIN {dbxref} X ON FD.dbxref_id = X.dbxref_id
       INNER JOIN {db} ON db.db_id = X.db_id
       WHERE featuremap_id = :featuremap_id
      ", array(':featuremap_id' => $featuremap->featuremap_id));
  $cmap = $cmap_r->fetchObject();
  $cmap_url = is_object($cmap) ? $cmap->urlprefix . $cmap->accession : NULL;

  $featuremap->num_loci = $num_loci;
  $featuremap->num_lg = $num_lg;
  $featuremap->cmap_url = $cmap_url;
}

function mainlab_tripal_preprocess_mainlab_featuremap_map_loci(&$variables) {
  $featuremap = $variables['node']->featuremap;
  $sql = "
      SELECT 
        F.uniquename AS LG, 
        (SELECT first (object_id) FROM {feature_relationship} WHERE subject_id = F2.feature_id AND type_id = (SELECT cvterm_id FROM cvterm WHERE name = 'instance_of' AND cv_id = (SELECT cv_id FROM cv WHERE name = 'relationship'))) AS feature_id, 
        F2.uniquename AS marker, 
        (SELECT name FROM {cvterm} WHERE cvterm_id = F2.type_id) AS type,
        FPP.value AS position 
      FROM {featurepos} FP 
      INNER JOIN {featuremap} FM ON FM.featuremap_id = FP.featuremap_id
      INNER JOIN {feature} F ON F.feature_id = FP.map_feature_id
      INNER JOIN {feature} F2 ON F2.feature_id = FP.feature_id
      INNER JOIN {featureposprop} FPP ON FP.featurepos_id = FPP.featurepos_id AND FPP.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'start' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
      WHERE FM.featuremap_id = :featuremap_id";
  $results = chado_query ($sql, array(':featuremap_id' => $featuremap->featuremap_id));
  $positions = array();
  while ($pos = $results->fetchObject()) {
    $positions [] = $pos;
  }
  $featuremap->positions = $positions;
}
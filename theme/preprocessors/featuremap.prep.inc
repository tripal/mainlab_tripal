<?php 

/**
 * @ingroup mainlab_featuremap
 */
function mainlab_tripal_preprocess_mainlab_featuremap_base(&$variables) {
  $featuremap = $variables['node']->featuremap;
  $num_loci = chado_query(
      "SELECT count (distinct F.uniquename)
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

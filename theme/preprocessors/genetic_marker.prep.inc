<?php 
/**
 *
 *
 * @ingroup tripal_feature
 */
function mainlab_tripal_preprocess_mainlab_feature_genetic_marker_image(&$variables) {
  $feature = $variables['node']->feature;
  // We want to show maps for this stock and maps of its children
  $results = chado_query(
      "SELECT eimage_type, image_uri, value as legend
       FROM {feature_image} FI
       INNER JOIN {eimage} I ON FI.eimage_id = I.eimage_id
       INNER JOIN {eimageprop} IP ON I.eimage_id = IP.eimage_id
       WHERE IP.type_id = (SELECT cvterm_id
                                            FROM {cvterm}
                                            WHERE name = 'legend'
                                            AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
                                          )
      AND feature_id = :feature_id",
      array (':feature_id' => $feature->feature_id));
  $images = NULL;
  $counter = 0;
  while ($img = $results->fetchObject()) {
    $images[$counter] = $img;
    $counter ++;
  }
  $feature->images = $images;
}

/**
 *
 *
 * @ingroup tripal_feature
 */
function mainlab_tripal_preprocess_mainlab_feature_genetic_marker_map_positions(&$variables) {
  $feature = $variables['node']->feature;
  // get map positions
  $results = chado_query(
      "SELECT
      FM.featuremap_id,
      CF.nid AS nid,
      FM.name AS name,
       X.accession,
       DB.urlprefix,
      LG.name AS linkage_group,
      BIN.name AS bin,
      LGP.value AS chr,
      FPP.value AS locus_start,
      LOCUS.uniquename AS locus_name
    FROM {feature} LOCUS
    INNER JOIN {feature_relationship} FR ON FR.subject_id = LOCUS.feature_id
    INNER JOIN {featurepos} FP ON LOCUS.feature_id = FP.feature_id
    INNER JOIN {featuremap} FM ON FP.featuremap_id = FM.featuremap_id
     LEFT JOIN {featuremap_dbxref} FD ON FP.featuremap_id = FD.featuremap_id
     LEFT JOIN {dbxref} X ON FD.dbxref_id = X.dbxref_id
     LEFT JOIN {db} ON db.db_id = X.db_id
    INNER JOIN {feature} LG ON FP.map_feature_id = LG.feature_id
    LEFT JOIN
     (SELECT * FROM {featureprop} WHERE type_id =
      (SELECT cvterm_id
       FROM {cvterm}
       WHERE name = 'chr_number'
       AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
      )
     )LGP ON LG.feature_id = LGP.feature_id
    INNER JOIN {featureposprop} FPP ON FP.featurepos_id = FPP.featurepos_id
    LEFT JOIN
     (SELECT F2.name, FR2.subject_id FROM {feature} F2
      INNER JOIN {feature_relationship} FR2 ON FR2.object_id = F2.feature_id
     WHERE FR2.type_id =
       (SELECT cvterm_id FROM {cvterm} WHERE name = 'located_in' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'relationship'))
     ) BIN ON LOCUS.feature_id = BIN.subject_id
    LEFT JOIN chado_featuremap CF ON FM.featuremap_id = CF.featuremap_id
    WHERE FR.type_id =
     (SELECT cvterm_id
      FROM {cvterm}
      WHERE name = 'instance_of'
     AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'relationship')
     )
    AND LOCUS.type_id =
     (SELECT cvterm_id
      FROM {cvterm}
      WHERE name = 'marker_locus'
     AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence')
     )
    AND FPP.type_id =
     (SELECT cvterm_id
      FROM {cvterm}
      WHERE name = 'start'
     AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
     )

    AND FR.object_id = :object_id", array(':object_id' => $feature->feature_id));
  $positions = array ();
  $counter = 0;
  while ($pos = $results->fetchObject()) {
     $positions [$counter] = $pos;
     $counter ++;
  }
  $feature->map_positions = $positions;
}
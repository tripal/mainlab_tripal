<?php 

/**
 * Provide more details about the QTL
 *
 * @param $variables
 */
function mainlab_tripal_preprocess_mainlab_feature_QTL_base(&$variables) {
  $feature = $variables['node']->feature;
  $feature_id = $feature->feature_id;
  $qtl = new stdClass();
  // Get Trait Study
  $sql =
  "SELECT P.name FROM {project} P
          INNER JOIN {feature_project} FP ON FP.project_id = P.project_id
          WHERE feature_id = :feature_id";
  $qtl->study = chado_query($sql, array(':feature_id' => $feature_id))->fetchField();
  
  // Get Population (Stock) and its parents
  $sql =
  "SELECT
          POP.nid AS pop_nid,
          POP.uniquename,
          POP.m_nid,
          POP.maternal,
          POP.p_nid,
          POP.paternal
       FROM {feature} QTL
       LEFT JOIN
          (SELECT feature_id, FM.name, FM.featuremap_id
           FROM {featuremap} FM
             INNER JOIN {featurepos} FP ON FM.featuremap_id = FP.featuremap_id
          ) MAP ON MAP.feature_id = QTL.feature_id
       LEFT JOIN
          (SELECT
             S.stock_id,
             featuremap_id,
             S.uniquename,
             CS.nid,
             MAT.uniquename as maternal,
             MAT.nid as m_nid,
             PAT.uniquename as paternal,
             PAT.nid as p_nid
           FROM {stock} S
             INNER JOIN {featuremap_stock} FS ON S.stock_id = FS.stock_id
             LEFT JOIN public.chado_stock CS ON CS.stock_id = S.stock_id
             LEFT JOIN (
               SELECT uniquename, object_id, CS2.nid
               FROM {stock} MP
                 INNER JOIN {stock_relationship} SR ON MP.stock_id = SR.subject_id
                 LEFT JOIN public.chado_stock CS2 ON CS2.stock_id = MP.stock_id
               WHERE SR.type_id = (
                 SELECT cvterm_id
                 FROM {cvterm}
                 WHERE name = 'is_a_maternal_parent_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'
               )
             )
           ) MAT ON MAT.object_id = S.stock_id
           LEFT JOIN
             (SELECT uniquename, object_id, CS3.nid
              FROM {stock} PP
                INNER JOIN {stock_relationship} SR2 ON PP.stock_id = SR2.subject_id
                LEFT JOIN public.chado_stock CS3 ON CS3.stock_id = PP.stock_id
              WHERE SR2.type_id = (
                SELECT cvterm_id
                FROM {cvterm}
                WHERE name = 'is_a_paternal_parent_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
             )
           ) PAT ON PAT.object_id = S.stock_id
          ) POP ON POP.featuremap_id = MAP.featuremap_id
       WHERE QTL.feature_id = :feature_id
   ";
  $qtl->population = chado_query($sql, array(':feature_id' => $feature_id))->fetchObject();
  
  // Get colocalizing markers
  $sql = "
     SELECT
        CO_LOC_M.nid AS coloc_marker_nid,
        CO_LOC_M.uniquename AS colocalizing_marker
     FROM {feature} QTL
     INNER JOIN
       (SELECT MKR.uniquename, FR.object_id, MKR.feature_id, nid
        FROM {feature} MKR
          INNER JOIN {feature_relationship} FR ON MKR.feature_id = FR.subject_id
          INNER JOIN {cvterm} V ON V.cvterm_id = FR.type_id
          LEFT JOIN public.chado_feature CF ON CF.feature_id = MKR.feature_id
        WHERE V.name = 'located_in' AND V.cv_id = (SELECT cv_id FROM {cv} WHERE name = 'relationship')
       ) CO_LOC_M ON CO_LOC_M.object_id = QTL.feature_id
     WHERE QTL.feature_id = :feature_id
   ";
  $qtl->colocalizing_marker = mainlab_tripal_dbresult_to_array(chado_query($sql, array(':feature_id' => $feature_id)));
   
  // Get neighboring markers
  $sql = "
     SELECT
        CO_LOC_M.nid AS neighboring_marker_nid,
        CO_LOC_M.uniquename AS neighboring_marker
     FROM {feature} QTL
     INNER JOIN
       (SELECT MKR.uniquename, FR.object_id, MKR.feature_id, nid
        FROM {feature} MKR
          INNER JOIN {feature_relationship} FR ON MKR.feature_id = FR.subject_id
          INNER JOIN {cvterm} V ON V.cvterm_id = FR.type_id
          LEFT JOIN public.chado_feature CF ON CF.feature_id = MKR.feature_id
        WHERE V.name = 'adjacent_to' AND V.cv_id = (SELECT cv_id FROM {cv} WHERE name = 'relationship')
       ) CO_LOC_M ON CO_LOC_M.object_id = QTL.feature_id
     WHERE QTL.feature_id = :feature_id
   ";
  $qtl->neighboring_marker = mainlab_tripal_dbresult_to_array(chado_query($sql, array(':feature_id' => $feature_id)));
   
  // Get aliases
  $sql = "
    SELECT name
    FROM {synonym} S
      INNER JOIN {feature_synonym} FS ON S.synonym_id = FS.synonym_id
    WHERE FS.feature_id = :feature_id
  ";
  $qtl->synonyms = mainlab_tripal_dbresult_to_array(chado_query($sql, array(':feature_id' => $feature_id)));
  $feature->mainlab_qtl->qtl_details =  $qtl;
}

/**
 * Provide more details about the QTL
 *
 * @param $variables
 */
function mainlab_tripal_preprocess_mainlab_feature_QTL_map_positions(&$variables) {
  $feature = $variables['node']->feature;
  $sql = "
  SELECT
  CF.nid AS nid,
  FM.name AS name,
  X.accession,
  DB.urlprefix,
  LG.name AS linkage_group,
  BIN.name AS bin,
  LGP.value AS chr,
  START.value AS QTL_start,
  STOP.value AS QTL_stop,
  PEAK.value AS QTL_peak,
  QTL.uniquename AS QTL_name
  FROM {feature} QTL
  INNER JOIN {featurepos} FP ON QTL.feature_id = FP.feature_id
  INNER JOIN {featuremap} FM ON FP.featuremap_id = FM.featuremap_id
  LEFT JOIN {featuremap_dbxref} FD ON FP.featuremap_id = FD.featuremap_id
  LEFT JOIN {dbxref} X ON FD.dbxref_id = X.dbxref_id
  LEFT JOIN {db} ON db.db_id = X.db_id
  INNER JOIN {feature} LG ON FP.map_feature_id = LG.feature_id
  LEFT JOIN (
  SELECT FP.feature_id, FP.value
  FROM {featureprop} FP
  WHERE FP.type_id =
  (SELECT cvterm_id
  FROM {cvterm}
  WHERE name = 'chr_number' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
  ) LGP ON LG.feature_id = LGP.feature_id
  LEFT JOIN (
  SELECT featurepos_id, value
  FROM {featureposprop} FPP
  WHERE FPP.type_id =
  (SELECT cvterm_id
  FROM {cvterm}
  WHERE name = 'start' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
  ) START ON FP.featurepos_id = START.featurepos_id
  LEFT JOIN (
  SELECT featurepos_id, value
  FROM {featureposprop} FPP
  WHERE FPP.type_id =
  (SELECT cvterm_id
  FROM {cvterm}
  WHERE name = 'stop' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
  ) STOP ON FP.featurepos_id = STOP.featurepos_id
  LEFT JOIN (
  SELECT featurepos_id, value
  FROM {featureposprop} FPP
  WHERE FPP.type_id =
  (SELECT cvterm_id
  FROM {cvterm}
           WHERE name = 'qtl_peak' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
    ) PEAK ON FP.featurepos_id = PEAK.featurepos_id
    LEFT JOIN
      (SELECT F2.name, FR2.subject_id
       FROM {feature} F2
         INNER JOIN {feature_relationship} FR2 ON FR2.object_id = F2.feature_id
       WHERE FR2.type_id =
         (SELECT cvterm_id FROM {cvterm} WHERE name = 'located_in')
      ) BIN ON QTL.feature_id = BIN.subject_id
    LEFT JOIN public.chado_featuremap CF ON FM.featuremap_id = CF.featuremap_id
    WHERE
      QTL.type_id = (SELECT type_id FROM {feature} WHERE feature_id = :feature_id1) AND
      QTL.feature_id = :feature_id2
  ";
  $qtl_map_pos = mainlab_tripal_dbresult_to_array(chado_query($sql, array(':feature_id1' => $feature->feature_id,
    ':feature_id2' => $feature->feature_id)));
  
  $feature->mainlab_qtl->map_positions = $qtl_map_pos;
}

/**
 *
 * @param $feature_id
 */
function getQTLDetails ($feature_id) {
  $qtl = new stdClass();
  // Get Trait Study
  $sql =
  "SELECT P.name FROM {project} P
          INNER JOIN {feature_project} FP ON FP.project_id = P.project_id
          WHERE feature_id = :feature_id";
  $qtl->study = chado_query($sql, array(':feature_id' => $feature_id))->fetchField();

  // Get Population (Stock) and its parents
  $sql =
  "SELECT
          POP.nid AS pop_nid,
          POP.uniquename,
          POP.m_nid,
          POP.maternal,
          POP.p_nid,
          POP.paternal
       FROM {feature} QTL
       LEFT JOIN
          (SELECT feature_id, FM.name, FM.featuremap_id
           FROM {featuremap} FM
             INNER JOIN {featurepos} FP ON FM.featuremap_id = FP.featuremap_id
          ) MAP ON MAP.feature_id = QTL.feature_id
       LEFT JOIN
          (SELECT
             S.stock_id,
             featuremap_id,
             S.uniquename,
             CS.nid,
             MAT.uniquename as maternal,
             MAT.nid as m_nid,
             PAT.uniquename as paternal,
             PAT.nid as p_nid
           FROM {stock} S
             INNER JOIN {featuremap_stock} FS ON S.stock_id = FS.stock_id
             LEFT JOIN public.chado_stock CS ON CS.stock_id = S.stock_id
             LEFT JOIN (
               SELECT uniquename, object_id, CS2.nid
               FROM {stock} MP
                 INNER JOIN {stock_relationship} SR ON MP.stock_id = SR.subject_id
                 LEFT JOIN public.chado_stock CS2 ON CS2.stock_id = MP.stock_id
               WHERE SR.type_id = (
                 SELECT cvterm_id
                 FROM {cvterm}
                 WHERE name = 'is_a_maternal_parent_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'
               )
             )
           ) MAT ON MAT.object_id = S.stock_id
           LEFT JOIN
             (SELECT uniquename, object_id, CS3.nid
              FROM {stock} PP
                INNER JOIN {stock_relationship} SR2 ON PP.stock_id = SR2.subject_id
                LEFT JOIN public.chado_stock CS3 ON CS3.stock_id = PP.stock_id
              WHERE SR2.type_id = (
                SELECT cvterm_id
                FROM {cvterm}
                WHERE name = 'is_a_paternal_parent_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
             )
           ) PAT ON PAT.object_id = S.stock_id
          ) POP ON POP.featuremap_id = MAP.featuremap_id
       WHERE QTL.feature_id = :feature_id
   ";
  $qtl->population = chado_query($sql, array(':feature_id' => $feature_id))->fetchObject();

  // Get colocalizing markers
  $sql = "
     SELECT
        CO_LOC_M.nid AS coloc_marker_nid,
        CO_LOC_M.uniquename AS colocalizing_marker
     FROM {feature} QTL
     INNER JOIN
       (SELECT MKR.uniquename, FR.object_id, MKR.feature_id, nid
        FROM {feature} MKR
          INNER JOIN {feature_relationship} FR ON MKR.feature_id = FR.subject_id
          INNER JOIN {cvterm} V ON V.cvterm_id = FR.type_id
          LEFT JOIN public.chado_feature CF ON CF.feature_id = MKR.feature_id
        WHERE V.name = 'located_in' AND V.cv_id = (SELECT cv_id FROM {cv} WHERE name = 'relationship')
       ) CO_LOC_M ON CO_LOC_M.object_id = QTL.feature_id
     WHERE QTL.feature_id = :feature_id
   ";
  $qtl->colocalizing_marker = mainlab_tripal_dbresult_to_array(chado_query($sql, array(':feature_id' => $feature_id)));
   
  // Get neighboring markers
  $sql = "
     SELECT
        CO_LOC_M.nid AS neighboring_marker_nid,
        CO_LOC_M.uniquename AS neighboring_marker
     FROM {feature} QTL
     INNER JOIN
       (SELECT MKR.uniquename, FR.object_id, MKR.feature_id, nid
        FROM {feature} MKR
          INNER JOIN {feature_relationship} FR ON MKR.feature_id = FR.subject_id
          INNER JOIN {cvterm} V ON V.cvterm_id = FR.type_id
          LEFT JOIN public.chado_feature CF ON CF.feature_id = MKR.feature_id
        WHERE V.name = 'adjacent_to' AND V.cv_id = (SELECT cv_id FROM {cv} WHERE name = 'relationship')
       ) CO_LOC_M ON CO_LOC_M.object_id = QTL.feature_id
     WHERE QTL.feature_id = :feature_id
   ";
  $qtl->neighboring_marker = mainlab_tripal_dbresult_to_array(chado_query($sql, array(':feature_id' => $feature_id)));
   
  // Get aliases
  $sql = "
    SELECT name
    FROM {synonym} S
      INNER JOIN {feature_synonym} FS ON S.synonym_id = FS.synonym_id
    WHERE FS.feature_id = :feature_id
  ";
  $qtl->synonyms = mainlab_tripal_dbresult_to_array(chado_query($sql, array(':feature_id' => $feature_id)));
   
  // Return the data
  return $qtl;
}
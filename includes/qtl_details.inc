<?php
function getQTLDetails ($feature_id) {
   $qtl = new stdClass();
   // Get Trait Study
   $sql = 
      "SELECT P.name FROM {project} P 
   	   INNER JOIN {feature_project} FP ON FP.project_id = P.project_id
   	   WHERE feature_id = %d";
   $qtl->study = db_result(chado_query($sql, $feature_id));
   
   // Get Population
   $sql = 
      "SELECT 
          POP.nid AS pop_nid,
          POP.uniquename,
          POP.m_nid,
          POP.maternal,
          POP.p_nid,
          POP.paternal
       FROM feature QTL
       LEFT JOIN
          (SELECT feature_id, FM.name, FM.featuremap_id FROM featuremap FM
           INNER JOIN featurepos FP ON FM.featuremap_id = FP.featuremap_id
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
           FROM stock S
           INNER JOIN featuremap_stock FS ON S.stock_id = FS.stock_id
           LEFT JOIN chado_stock CS ON CS.stock_id = S.stock_id
           INNER JOIN (SELECT uniquename, object_id, CS2.nid FROM stock MP
                                  INNER JOIN stock_relationship SR ON MP.stock_id = SR.subject_id
                                  LEFT JOIN chado_stock CS2 ON CS2.stock_id = MP.stock_id
                                  WHERE SR.type_id = (SELECT cvterm_id FROM cvterm WHERE name = 'is_a_maternal_parent_of')
                                 ) MAT ON MAT.object_id = S.stock_id
           INNER JOIN (SELECT uniquename, object_id, CS3.nid FROM stock PP
                                  INNER JOIN stock_relationship SR2 ON PP.stock_id = SR2.subject_id
                                  LEFT JOIN chado_stock CS3 ON CS3.stock_id = PP.stock_id
                                  WHERE SR2.type_id = (SELECT cvterm_id FROM cvterm WHERE name = 'is_a_paternal_parent_of')
                                 ) PAT ON PAT.object_id = S.stock_id
          ) POP ON POP.featuremap_id = MAP.featuremap_id
       WHERE QTL.feature_id = %d";
   $qtl->population = db_fetch_object(db_query($sql, $feature_id)); // chado_query() wouldn't work.
   
   // Get colocalizing markers
   $sql = 
   "SELECT 
      CO_LOC_M.nid AS coloc_marker_nid,
      CO_LOC_M.uniquename AS colocalizing_marker
   FROM feature QTL
   INNER JOIN 
     (SELECT MKR.uniquename, FR.object_id, MKR.feature_id, nid FROM feature MKR
      INNER JOIN feature_relationship FR ON MKR.feature_id = FR.subject_id
      INNER JOIN cvterm V ON V.cvterm_id = FR.type_id
      LEFT JOIN chado_feature CF ON CF.feature_id = MKR.feature_id
      WHERE V.name = 'located_in' 
      AND V.cv_id = (SELECT cv_id FROM cv WHERE name = 'relationship')
     ) CO_LOC_M ON CO_LOC_M.object_id = QTL.feature_id
   WHERE QTL.feature_id = %d";
   $qtl->colocalizing_marker = mainlab_tripal_dbresult_to_array(db_query($sql, $feature_id));
   
   // Get neighboring markers
   $sql =
   "SELECT
      CO_LOC_M.nid AS neighboring_marker_nid,
      CO_LOC_M.uniquename AS neighboring_marker
   FROM feature QTL
   INNER JOIN
     (SELECT MKR.uniquename, FR.object_id, MKR.feature_id, nid FROM feature MKR
      INNER JOIN feature_relationship FR ON MKR.feature_id = FR.subject_id
      INNER JOIN cvterm V ON V.cvterm_id = FR.type_id
      LEFT JOIN chado_feature CF ON CF.feature_id = MKR.feature_id
      WHERE V.name = 'adjacent_to'
      AND V.cv_id = (SELECT cv_id FROM cv WHERE name = 'relationship')
        ) CO_LOC_M ON CO_LOC_M.object_id = QTL.feature_id
      WHERE QTL.feature_id = %d";
   $qtl->neighboring_marker = mainlab_tripal_dbresult_to_array(db_query($sql, $feature_id));
   
   return $qtl;
}

function mainlab_tripal_dbresult_to_array($dbresult) {
	$arr = array();
	while ($obj = db_fetch_object($dbresult)) {
		array_push($arr, $obj);
	}
	return $arr;
}

function mainlab_tripal_array_agg($arr, $separator) {
	$text = "";
	for ($i = 0; $i < count($arr); $i ++) {
		$text .= $arr[$i];
		if ($i != count($arr) - 1) {
			$text .= $separator;
		}
	}
	return $text;
}
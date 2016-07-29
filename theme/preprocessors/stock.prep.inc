<?php
function mainlab_tripal_preprocess_mainlab_stock_base(&$variables) {
  $node = $variables['node'];
  $stock = $node->stock;
  // Get libraries
  if (db_table_exists('chado.library_stock')) {
    $stock = chado_expand_var($node->stock, 'table', 'library_stock', array('return_array' => 1));
  }
  
  // Get in collection
  if (!db_table_exists('chado.stockcollection_db')) {
    $stock->in_collection = null;
  }  
  else {
    $results = chado_query(
        "SELECT
        max(SC.name) AS collection,
        max(db.name) AS db,
        max(db.urlprefix) AS urlprefix,
        string_agg(X.accession, '; ') AS accession,
                          string_agg(X.version, '; ') AS version
        FROM {stockcollection} SC
        INNER JOIN {stockcollection_stock} SS ON SS.stockcollection_id = SC.stockcollection_id
        INNER JOIN {stockcollection_db} SD ON SC.stockcollection_id = SD.stockcollection_id
        INNER JOIN {db} ON db.db_id = SD.db_id
        INNER JOIN {stock_dbxref} SX ON SX.stock_id = SS.stock_id
        INNER JOIN {dbxref} X ON SX.dbxref_id = X.dbxref_id AND X.db_id = db.db_id
        WHERE SS.stock_id = :stock_id
        GROUP BY SC.stockcollection_id
        ",  array(':stock_id' => $stock->stock_id));
    $coll = NULL;
    $counter = 0;
    while ($c = $results->fetchObject()) {
      $coll[$counter] = $c;
      $counter ++;
    }
    $stock->in_collection = $coll;
  }
  
  // Get Maternal Parent
  $results = chado_query(
      "SELECT S2.stock_id, S2.uniquename, S2.description, V.name AS type
       FROM {stock} S1
         INNER JOIN {stock_relationship} SR ON SR.subject_id = S1.stock_id
         INNER JOIN {stock} S2 ON S2.stock_id = SR.object_id
         INNER JOIN {cvterm} V ON V.cvterm_id = S2.type_id
         WHERE SR.type_id =
         (SELECT cvterm_id
          FROM {cvterm}
          WHERE name = 'is_a_maternal_parent_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
          AND S1.stock_id = :stock_id",  array(':stock_id' => $stock->stock_id));
  $maternal_parent = NULL;
  $counter = 0;
  while ($parent = $results->fetchObject()) {
    if (db_table_exists("chado_stock")) {
      $parent->nid = db_query("SELECT nid FROM chado_stock WHERE stock_id = :stock_id", array(':stock_id' => $parent->stock_id))->fetchField();
    }
    $maternal_parent[$counter] = $parent;
    $counter ++;
  }
  $stock->maternal_parent = $maternal_parent;
  // Get Paternal Parent
  $results = chado_query(
      "SELECT S2.stock_id, S2.uniquename, S2.description, V.name AS type
       FROM {stock} S1
         INNER JOIN {stock_relationship} SR ON SR.subject_id = S1.stock_id
         INNER JOIN {stock} S2 ON S2.stock_id = SR.object_id
         INNER JOIN {cvterm} V ON V.cvterm_id = S2.type_id
         WHERE SR.type_id =
         (SELECT cvterm_id
          FROM {cvterm}
          WHERE name = 'is_a_paternal_parent_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
          AND S1.stock_id = :stock_id", array(':stock_id' => $stock->stock_id));
  $paternal_parent = NULL;
  $counter = 0;
  while ($parent = $results->fetchObject()) {
    if (db_table_exists("chado_stock")) {
      $parent->nid = db_query("SELECT nid FROM chado_stock WHERE stock_id = :stock_id", array(':stock_id' => $parent->stock_id))->fetchField();
    }
    $paternal_parent[$counter] = $parent;
    $counter ++;
  }
  $stock->paternal_parent = $paternal_parent;
  
  // Get maps for this stock and maps of its children
  $results = chado_query(
      "SELECT
           M.featuremap_id,
        M.name
           FROM {featuremap} M
           INNER JOIN {featuremap_stock} MS ON M.featuremap_id = MS.featuremap_id
           WHERE MS.stock_id = :stock_id1
           OR MS.stock_id in
           (SELECT SR.object_id FROM {stock} S1
               INNER JOIN {stock_relationship} SR ON S1.stock_id = SR.subject_id
               WHERE SR.type_id IN
                  (SELECT cvterm_id
                   FROM {cvterm}
                   WHERE (name = 'is_a_maternal_parent_of'
                   OR name = 'is_a_paternal_parent_of') AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
               AND S1.stock_id = :stock_id2)",  array(':stock_id1' => $stock->stock_id,  ':stock_id2' => $stock->stock_id));
  $maps = NULL;
  $counter = 0;
  while ($map = $results->fetchObject()) {
    if (db_table_exists("chado_featuremap")) {
      $map->nid = db_query("SELECT nid FROM chado_featuremap WHERE featuremap_id = :featuremap_id", array(':featuremap_id' => $map->featuremap_id))->fetchField();
    }
    $map->ptype = chado_query("SELECT value FROM {featuremapprop} WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'population_type' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')) AND featuremap_id = :featuremap_id", array(':featuremap_id' => $map->featuremap_id))->fetchField();
    $map->ggroup = chado_query("SELECT value FROM {featuremapprop} WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'genome_group' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')) AND featuremap_id =  :featuremap_id", array(':featuremap_id' => $map->featuremap_id))->fetchField();
    $map->mtype = chado_query("SELECT value FROM {featuremapprop} WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'map_type' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')) AND featuremap_id =  :featuremap_id", array(':featuremap_id' => $map->featuremap_id))->fetchField();
  
    $maps[$counter] = $map;
    $counter ++;
  }
  $stock->population_map = $maps;

  // Get phenotypic data
  $results = chado_query(
      "SELECT
      P.uniquename, P.value, J.name AS project, GEO.nd_geolocation_id, GEO.environment, SP.value AS replications, V.name AS descriptor
      FROM {nd_experiment_phenotype} NEP
      INNER JOIN {phenotype} P ON NEP.phenotype_id = P.phenotype_id
      INNER JOIN {cvterm} V ON V.cvterm_id = P.attr_id
      INNER JOIN {nd_experiment_stock} NES ON NES.nd_experiment_id = NEP.nd_experiment_id
      LEFT JOIN {nd_experiment_project} NEJ ON NES.nd_experiment_id = NEJ.nd_experiment_id
      LEFT JOIN {project} J ON J.project_id = NEJ.project_id
      INNER JOIN {nd_experiment} NE ON NE.nd_experiment_id = NEP.nd_experiment_id
      LEFT JOIN
      (SELECT
         NG.nd_geolocation_id,
             NGP.value AS environment
       FROM {nd_geolocation} NG
       INNER JOIN {nd_geolocationprop} NGP ON NG.nd_geolocation_id = NGP.nd_geolocation_id
       AND NGP.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'site_code' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
      ) GEO ON GEO.nd_geolocation_id = NE.nd_geolocation_id
          LEFT JOIN {stockprop} SP ON NES.stock_id = SP.stock_id AND SP.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'rep' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
      WHERE NES.stock_id = :stock_id OR NES.stock_id IN (SELECT subject_id FROM {stock_relationship} WHERE object_id = :object_id
      AND type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'sample_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')))
          ORDER BY project, environment, replications, uniquename", array(':stock_id' => $stock->stock_id, ':object_id' => $stock->stock_id));
  $phenotypic_data = NULL;
  $counter = 0;
  while ($traits = $results->fetchObject()) {
    $phenotypic_data[$counter] = $traits;
    $counter ++;
  }
  $stock->phenotypic_data = $phenotypic_data;
  
  // Get genotypic data
  $results = chado_query(
      "SELECT
      G.uniquename, G.description, J.name AS project, F.organism_id,
            (SELECT nid FROM chado_feature WHERE feature_id = F.feature_id) AS marker_nid,
            (SELECT name FROM {cvterm} WHERE cvterm_id = F.type_id) AS marker_type
      FROM {nd_experiment_genotype} NEG
      INNER JOIN {genotype} G ON NEG.genotype_id = G.genotype_id
      INNER JOIN {feature_genotype} FG ON G.genotype_id = FG.genotype_id
      INNER JOIN {feature} F ON F.feature_id = FG.feature_id
      INNER JOIN {nd_experiment_stock} NES ON NES.nd_experiment_id = NEG.nd_experiment_id
      LEFT JOIN {nd_experiment_project} NEJ ON NES.nd_experiment_id = NEJ.nd_experiment_id
      LEFT JOIN {project} J ON J.project_id = NEJ.project_id
      WHERE stock_id IN
        (SELECT stock_id
         FROM {stock} S
         INNER JOIN {stock_relationship} SR ON S.stock_id = SR.subject_id
         WHERE SR.type_id =
            (SELECT cvterm_id FROM {cvterm} WHERE name = 'sample_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
         AND SR.object_id = :object_id)",  array(':object_id' => $stock->stock_id));
  $gdata = NULL;
  $counter = 0;
  while ($gtype = $results->fetchObject()) {
    $gdata[$counter] = $gtype;
    $counter ++;
  }
  $stock->genotypic_data = $gdata;
}

function mainlab_tripal_preprocess_mainlab_stock_image(&$variables) {
  $node = $variables['node'];
  $stock = $node->stock;
  // We want to show maps for this stock and maps of its children
  if (mainlab_tripal_get_site() == 'cottongen') { // CottonGen has image legend stored
    $results = chado_query(
        "SELECT eimage_type, image_uri, value as legend
       FROM {stock_image} SI
       INNER JOIN {eimage} I ON SI.eimage_id = I.eimage_id
       INNER JOIN {eimageprop} IP ON I.eimage_id = IP.eimage_id
       WHERE IP.type_id = (SELECT cvterm_id
                                            FROM {cvterm}
                                            WHERE name = 'legend'
                                            AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
                                          )
      AND stock_id = :stock_id",
        array(':stock_id' => $stock->stock_id));
  } else { // Do not pull legends by default (e.g. for GDR)
    $results = chado_query(
        "SELECT eimage_type, image_uri
       FROM {stock_image} SI
       INNER JOIN {eimage} I ON SI.eimage_id = I.eimage_id
       WHERE stock_id = :stock_id",
        array(':stock_id' => $stock->stock_id));
  }
  $images = NULL;
  $counter = 0;
  while ($img = $results->fetchObject()) {
    $images[$counter] = $img;
    $counter ++;
  }
  $stock->images = $images;
  $node->stock = $stock;
}
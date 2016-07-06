<?php 

/**
 * @ingroup tripal_stock
 */
function mainlab_tripal_pub_preprocess_mainlab_pub_image(&$variables) {
  $pub = $variables['node']->pub;
  if (!db_table_exists('chado.pub_image')) {
    $pub->images = array();
    return;
  }
  // We want to show maps for this pub and maps of its children
  $results = chado_query(
      "SELECT eimage_type, image_uri, value as legend
       FROM {pub_image} SI
       INNER JOIN {eimage} I ON SI.eimage_id = I.eimage_id
       INNER JOIN {eimageprop} IP ON I.eimage_id = IP.eimage_id
       WHERE IP.type_id = (SELECT cvterm_id
                                            FROM {cvterm}
                                            WHERE name = 'legend'
                                            AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
                                          )
      AND pub_id = :pub_id",
      array(':pub_id' => $pub->pub_id));
  $images = NULL;
  $counter = 0;
  while ($img = $results->fetchObject()) {
    $images[$counter] = $img;
    $counter ++;
  }
  $pub->images = $images;
}

/**
 * @ingroup tripal_natural_diversity
 */
function mainlab_tripal_pub_preprocess_mainlab_nd_geolocation_base(&$variables) {
  $nd_geolocation = $variables['node']->nd_geolocation;
  $sql =
  "SELECT nid, name FROM {project} P
       LEFT JOIN chado_project CP ON CP.project_id = P.project_id
       WHERE P.project_id IN
       (SELECT project_id
        FROM {nd_geolocation} NG
        INNER JOIN {nd_experiment} NE on NE.nd_geolocation_id = NG.nd_geolocation_id
        INNER JOIN {nd_experiment_project} NP ON NE.nd_experiment_id = NP.nd_experiment_id
        WHERE NG.nd_geolocation_id = :nd_geolocation_id
        GROUP BY project_id)";
  $results = chado_query($sql, array(':nd_geolocation_id' => $nd_geolocation->nd_geolocation_id));
  $projects = array();
  while ($d = $results->fetchObject()) {
    array_push($projects, $d);
  }
  $nd_geolocation->projects = $projects;
}

/**
 * @ingroup tripal_natural_diversity
 */
function mainlab_tripal_pub_preprocess_mainlab_nd_geolocation_associated_dataset(&$variables) {
  $nd_geolocation = $variables['node']->nd_geolocation;
  $sql =
  "SELECT nid, name, PP.value AS description FROM {project} P
       LEFT JOIN chado_project CP ON CP.project_id = P.project_id
       LEFT JOIN {projectprop} PP ON P.project_id = PP.project_id
         AND PP.type_id = (SELECT cvterm_id FROM {cvterm}
         WHERE name = 'description' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
       WHERE P.project_id IN
       (SELECT project_id
        FROM {nd_geolocation} NG
        INNER JOIN {nd_experiment} NE on NE.nd_geolocation_id = NG.nd_geolocation_id
        INNER JOIN {nd_experiment_project} NP ON NE.nd_experiment_id = NP.nd_experiment_id
        WHERE NG.nd_geolocation_id = :nd_geolocation_id
        GROUP BY project_id)";
  $results = chado_query($sql, array(':nd_geolocation_id' => $nd_geolocation->nd_geolocation_id));
  $projects = array();
  while ($d = $results->fetchObject()) {
    array_push($projects, $d);
  }
  $nd_geolocation->projects = $projects;
}

/**
 * @ingroup tripal_eimage
 */
function mainlab_tripal_pub_preprocess_mainlab_eimage_base(&$variables) {
  $eimage = $variables['node']->eimage;
  // Get Eimage legend
  $sql =
  "SELECT value
      FROM {eimage} E
      LEFT JOIN {eimageprop} EP ON E.eimage_id = EP.eimage_id
      AND type_id =
        (SELECT cvterm_id FROM {cvterm} WHERE name = 'legend'
         AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
        )
      WHERE E.eimage_id = :eimage_id";
  $results = chado_query($sql, array(':eimage_id' => $eimage->eimage_id));
  $legends = array();
  while ($result = $results->fetchObject()) {
    array_push($legends, $result->value);
  }
  $eimage->legends = $legends;
  // Get Eimage contact
  $sql =
  "SELECT C.contact_id, name, description
      FROM {contact} C
      INNER JOIN {contact_image} CI ON CI.contact_id = C.contact_id
      WHERE CI.eimage_id = :eimage_id";
  $results = chado_query($sql, array(':eimage_id' => $eimage->eimage_id));
  $contacts = array();
  while ($result = $results->fetchObject()) {
    $detail_contact = new stdClass();
    $detail_sql = "SELECT (SELECT name FROM {cvterm} WHERE cvterm_id = type_id) AS type, value FROM {contactprop} WHERE contact_id = :contact_id";
    $detail_results = chado_query($detail_sql, array(':contact_id' => $result->contact_id));
    while ($details = $detail_results->fetchObject()) {
      $type = $details->type;
      $value = $details->value;
      if (property_exists($detail_contact, $type)) {
        $detail_contact->$type .= ". " . $value;
      } else {
        $detail_contact->$type = $value;
      }
    }
    $detail_contact->name = $result->name;
    $detail_contact->description = $result->description;
    array_push($contacts, $detail_contact);
  }
  $eimage->contacts = $contacts;

  // Get image pubs
  $sql =
  "SELECT uniquename, nid, P.pub_id
      FROM {pub} P
      INNER JOIN {pub_image} PI ON PI.pub_id = P.pub_id
      LEFT JOIN chado_pub CP ON CP.pub_id = P.pub_id
      WHERE PI.eimage_id = :eimage_id";
  $results = chado_query($sql, array(':eimage_id' => $eimage->eimage_id));
  $pubs = array();
  while ($result = $results->fetchObject()) {
    array_push($pubs, $result);
  }
  $eimage->pubs = $pubs;
  // See if this image belong to a marker
  $sql =
  "SELECT uniquename, nid
      FROM {feature} F
      INNER JOIN {feature_image} FI ON FI.feature_id = F.feature_id
      LEFT JOIN chado_feature CF ON F.feature_id = CF.feature_id
      WHERE FI.eimage_id = :eimage_id
      AND F.type_id =
        (SELECT cvterm_id FROM {cvterm} WHERE name = 'genetic_marker'
         AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))";
  $marker = chado_query($sql, array(':eimage_id' => $eimage->eimage_id))->fetchObject();
  $eimage->marker = $marker;
  // See if this image belong to a stock
  $sql =
  "SELECT uniquename, nid, S.stock_id
      FROM {stock} S
      INNER JOIN {stock_image} SI ON SI.stock_id = S.stock_id
      LEFT JOIN chado_stock CS ON CS.stock_id = S.stock_id
      WHERE SI.eimage_id = :eimage_id";
  $stock = chado_query($sql, array(':eimage_id' => $eimage->eimage_id))->fetchObject();
  $eimage->stock = $stock;

  // Get associated projects
  $project = array();
  $sql =
  "SELECT value FROM {eimageprop}
     WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'project_id' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
     AND eimage_id = :eimage_id";
  $result_pj = chado_query($sql, array(':eimage_id' => $eimage->eimage_id));
  $sql_pj = "SELECT distinct name, nid FROM {project} P LEFT JOIN chado_project CP ON CP.project_id = P.project_id WHERE P.project_id = :project_id";
  while ($pj = $result_pj->fetchObject()) {
    $result = chado_query($sql_pj, array(':project_id' => $pj->value))->fetchObject();
    array_push($project, $result);
  }
  $eimage->project = $project;
}

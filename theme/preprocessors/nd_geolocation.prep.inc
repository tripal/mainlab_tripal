<?php
// The templates are loaded in alphabetic order so we load the $node->nd_geolocation in 
// the 'assiciated_dataset' template which can be used by all other templates
function mainlab_tripal_preprocess_mainlab_nd_geolocation_associated_dataset(&$variables) {
  $node = $variables['node'];
  $nd_geolocation_id = chado_get_id_from_nid('nd_geolocation', $node->nid);
  $values = array('nd_geolocation_id' => $nd_geolocation_id);
  $nd_geolocation = chado_generate_var('nd_geolocation', $values);
  // expand the nd_geolocation to include the properties.
  $nd_geolocation = chado_expand_var($nd_geolocation,'table', 'nd_geolocationprop', array('return_array' => 1));
  $sql =
  "SELECT nid, name, description FROM {project} P
       LEFT JOIN public.chado_project CP ON CP.project_id = P.project_id
       WHERE P.project_id IN
       (SELECT project_id
        FROM {nd_geolocation} NG
        INNER JOIN {nd_experiment} NE on NE.nd_geolocation_id = NG.nd_geolocation_id
        INNER JOIN {nd_experiment_project} NP ON NE.nd_experiment_id = NP.nd_experiment_id
        WHERE NG.nd_geolocation_id = :nd_geolocation_id
        GROUP BY project_id)";
  $results = chado_query($sql, array(':nd_geolocation_id' => $nd_geolocation_id));
  $projects = array();
  while ($d = $results->fetchObject()) {
    array_push($projects, $d);
  }
  $nd_geolocation->projects = $projects;
  $node->nd_geolocation = $nd_geolocation;
}
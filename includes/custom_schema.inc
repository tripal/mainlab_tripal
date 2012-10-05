<?php 

function mainlab_tripal_chado_schema_v1_2_feature(){

  $schema['referring_tables'] = array(
    'feature_stock',
  );
  return $schema;
	
}

function mainlab_tripal_chado_schema_v1_2_stock(){

  $schema['referring_tables'] = array(
    'featuremap_stock',
  );
  return $schema;
  
}

function mainlab_tripal_chado_schema_v1_2_featuremap(){

  $schema['referring_tables'] = array(
    'featuremap_stock',
  );
  return $schema;
  
}
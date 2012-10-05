<?php 

function mainlab_tripal_chado_schema_v1_2_feature(){
  $schema['referring_tables'] = array(
    'feature_stock',
  );
  return $schema;
}

function mainlab_tripal_chado_schema_v1_2_library(){
  $schema['referring_tables'] = array(
    'library_stock',
    'library_contact',
  );
  return $schema;
}

function mainlab_tripal_chado_schema_v1_2_stock(){
  $schema['referring_tables'] = array(
    'featuremap_stock',
    'library_stock',
    'feature_stock'
  );
  return $schema;
}

function mainlab_tripal_chado_schema_v1_2_featuremap(){
  $schema['referring_tables'] = array(
    'featuremap_stock',
    'featuremapprop'
  );
  return $schema;
}
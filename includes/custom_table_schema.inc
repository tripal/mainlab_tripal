<?php
/**
 * @file
* Describes the chado tables for the mainlab_tripal module
*/

/**
 * Implements hook_chado_schema_analysis_organism()
*
* Purpose: To describe the structure of 'analysis_organism' to tripal
* @see chado_insert_record()
* @see chado_update_record()
* @see chado_select_record()
* @see chado_generate_var()
* @see chado_expan_var()
*
* @return
*    An array describing the 'analysis_organism' table
*
* @ingroup tripal_chado_schema_api
*
*/
function mainlab_tripal_chado_schema_analysis_organism() {
  $description = array (
  'table' => 'analysis_organism',
  'fields' => 
  array (
    'analysis_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'organism_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'indexes' => 
  array (
    'networkmod_qtl_indx0' => 
    array (
      0 => 'analysis_id',
    ),
    'networkmod_qtl_indx1' => 
    array (
      0 => 'organism_id',
    ),
  ),
  'foreign keys' => 
  array (
    'analysis' => 
    array (
      'table' => 'analysis',
      'columns' => 
      array (
        'analysis_id' => 'analysis_id',
      ),
    ),
    'organism' => 
    array (
      'table' => 'organism',
      'columns' => 
      array (
        'organism_id' => 'organism_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_contact_image()
 *
 * Purpose: To describe the structure of 'contact_image' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'contact_image' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_contact_image() {
  $description = array(
    'description' => '',
    'table' => 'contact_image',
    'foreign keys' => array(
      'cvterm' => array(
        'table' => 'cvterm',
        'columns' => array(
          'type_id' => 'cvterm_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_contactprop()
 *
 * Purpose: To describe the structure of 'contactprop' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'contactprop' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_contactprop() {
  $description = array (
  'table' => 'contactprop',
  'fields' => 
  array (
    'contactprop_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'contact_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'type_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'value' => 
    array (
      'type' => 'text',
      'not null' => false,
    ),
    'rank' => 
    array (
      'type' => 'int',
      'not null' => true,
      'default' => 0,
    ),
  ),
  'primary key' => 
  array (
    0 => 'contactprop_id',
  ),
  'unique keys' => 
  array (
    'contactprop_c1' => 
    array (
      0 => 'contact_id',
      1 => 'type_id',
      2 => 'rank',
    ),
  ),
  'indexes' => 
  array (
    'contactprop_idx1' => 
    array (
      0 => 'contact_id',
    ),
    'contactprop_idx2' => 
    array (
      0 => 'type_id',
    ),
  ),
  'foreign keys' => 
  array (
    'cvterm' => 
    array (
      'table' => 'cvterm',
      'columns' => 
      array (
        'type_id' => 'cvterm_id',
      ),
    ),
    'contact' => 
    array (
      'table' => 'contact',
      'columns' => 
      array (
        'contact_id' => 'contact_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_eimageprop()
 *
 * Purpose: To describe the structure of 'eimageprop' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'eimageprop' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_eimageprop() {
  $description = array (
  'table' => 'eimageprop',
  'fields' => 
  array (
    'eimageprop_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'eimage_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'type_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'value' => 
    array (
      'type' => 'text',
      'not null' => false,
    ),
    'rank' => 
    array (
      'type' => 'int',
      'not null' => true,
      'default' => 0,
    ),
  ),
  'primary key' => 
  array (
    0 => 'eimageprop_id',
  ),
  'unique keys' => 
  array (
    'eimageprop_c1' => 
    array (
      0 => 'eimage_id',
      1 => 'type_id',
      2 => 'rank',
    ),
  ),
  'indexes' => 
  array (
    'eimageprop_idx1' => 
    array (
      0 => 'eimage_id',
    ),
    'eimageprop_idx2' => 
    array (
      0 => 'type_id',
    ),
  ),
  'foreign keys' => 
  array (
    'cvterm' => 
    array (
      'table' => 'cvterm',
      'columns' => 
      array (
        'type_id' => 'cvterm_id',
      ),
    ),
    'eimage' => 
    array (
      'table' => 'eimage',
      'columns' => 
      array (
        'eimage_id' => 'eimage_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_feature_contact()
 *
 * Purpose: To describe the structure of 'feature_contact' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'feature_contact' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_feature_contact() {
  $description = array (
  'table' => 'feature_contact',
  'fields' => 
  array (
    'feature_contact_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'feature_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'contact_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'feature_contact_id',
  ),
  'unique keys' => 
  array (
    'feature_contact_c1' => 
    array (
      0 => 'feature_id',
      1 => 'contact_id',
    ),
  ),
  'foreign keys' => 
  array (
    'feature' => 
    array (
      'table' => 'feature',
      'columns' => 
      array (
        'feature_id' => 'feature_id',
      ),
    ),
    'contact' => 
    array (
      'table' => 'contact',
      'columns' => 
      array (
        'contact_id' => 'contact_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_feature_image()
 *
 * Purpose: To describe the structure of 'feature_image' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'feature_image' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_feature_image() {
  $description = array(
    'description' => '',
    'table' => 'feature_image',
    'foreign keys' => array(
      'eimage' => array(
        'table' => 'eimage',
        'columns' => array(
          'eimage_id' => 'eimage_id',
        ),
      ),
      'feature' => array(
        'table' => 'feature',
        'columns' => array(
          'feature_id' => 'feature_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_featuremap_contact()
 *
 * Purpose: To describe the structure of 'featuremap_contact' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'featuremap_contact' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_featuremap_contact() {
  $description = array (
  'table' => 'featuremap_contact',
  'fields' => 
  array (
    'featuremap_contact_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'featuremap_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'contact_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'featuremap_contact_id',
  ),
  'unique keys' => 
  array (
    'featuremap_contact_c1' => 
    array (
      0 => 'featuremap_id',
      1 => 'contact_id',
    ),
  ),
  'foreign keys' => 
  array (
    'featuremap' => 
    array (
      'table' => 'featuremap',
      'columns' => 
      array (
        'featuremap_id' => 'featuremap_id',
      ),
    ),
    'contact' => 
    array (
      'table' => 'contact',
      'columns' => 
      array (
        'contact_id' => 'contact_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_featuremap_dbxref()
 *
 * Purpose: To describe the structure of 'featuremap_dbxref' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'featuremap_dbxref' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_featuremap_dbxref() {
  $description = array (
  'table' => 'featuremap_dbxref',
  'fields' => 
  array (
    'featuremap_dbxref_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'featuremap_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'dbxref_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'featuremap_dbxref_id',
  ),
  'unique keys' => 
  array (
    'featuremap_dbxref_c1' => 
    array (
      0 => 'featuremap_id',
      1 => 'dbxref_id',
    ),
  ),
  'indexes' => 
  array (
    'featuremap_dbxref_idx1' => 
    array (
      0 => 'featuremap_dbxref_id',
    ),
    'featuremap_dbxref_idx2' => 
    array (
      0 => 'dbxref_id',
    ),
  ),
  'foreign keys' => 
  array (
    'dbxref' => 
    array (
      'table' => 'dbxref',
      'columns' => 
      array (
        'dbxref_id' => 'dbxref_id',
      ),
    ),
    'featuremap' => 
    array (
      'table' => 'featuremap',
      'columns' => 
      array (
        'featuremap_id' => 'featuremap_id',
      ),
    ),
  ),
  'referring_tables' => NULL,
);
  return $description;
}
/**
 * Implements hook_chado_schema_featuremap_organism()
 *
 * Purpose: To describe the structure of 'featuremap_organism' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'featuremap_organism' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_featuremap_organism() {
  $description = array (
  'table' => 'featuremap_organism',
  'fields' => 
  array (
    'featuremap_organism_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'organism_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'featuremap_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'featuremap_organism_id',
  ),
  'unique keys' => 
  array (
    'organismprop_c1' => 
    array (
      0 => 'organism_id',
      1 => 'featuremap_id',
    ),
  ),
  'indexes' => 
  array (
    'organismprop_idx1' => 
    array (
      0 => 'organism_id',
    ),
    'organismprop_idx2' => 
    array (
      0 => 'featuremap_id',
    ),
  ),
  'foreign keys' => 
  array (
    'organism' => 
    array (
      'table' => 'organism',
      'columns' => 
      array (
        'organism_id' => 'organism_id',
      ),
    ),
    'featuremap' => 
    array (
      'table' => 'featuremap',
      'columns' => 
      array (
        'featuremap_id' => 'featuremap_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_featuremapprop()
 *
 * Purpose: To describe the structure of 'featuremapprop' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'featuremapprop' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_featuremapprop() {
  $description = array (
  'table' => 'featuremapprop',
  'fields' => 
  array (
    'featuremapprop_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'featuremap_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'type_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'value' => 
    array (
      'type' => 'text',
      'not null' => false,
    ),
    'rank' => 
    array (
      'type' => 'int',
      'not null' => true,
      'default' => 0,
    ),
  ),
  'primary key' => 
  array (
    0 => 'featuremapprop_id',
  ),
  'unique keys' => 
  array (
    'featuremapprop_c1' => 
    array (
      0 => 'featuremap_id',
      1 => 'type_id',
      2 => 'rank',
    ),
  ),
  'indexes' => 
  array (
    'featuremapprop_idx1' => 
    array (
      0 => 'featuremap_id',
    ),
    'featuremapprop_idx2' => 
    array (
      0 => 'type_id',
    ),
  ),
  'foreign keys' => 
  array (
    'cvterm' => 
    array (
      'table' => 'cvterm',
      'columns' => 
      array (
        'type_id' => 'cvterm_id',
      ),
    ),
    'featuremap' => 
    array (
      'table' => 'featuremap',
      'columns' => 
      array (
        'featuremap_id' => 'featuremap_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_featuremap_stock()
 *
 * Purpose: To describe the structure of 'featuremap_stock' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'featuremap_stock' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_featuremap_stock() {
  $description = array (
  'table' => 'featuremap_stock',
  'fields' => 
  array (
    'featuremap_stock_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'featuremap_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'stock_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'featuremap_stock_id',
  ),
  'unique keys' => 
  array (
    'featuremap_stock_c1' => 
    array (
      0 => 'featuremap_id',
      1 => 'stock_id',
    ),
  ),
  'foreign keys' => 
  array (
    'featuremap' => 
    array (
      'table' => 'featuremap',
      'columns' => 
      array (
        'featuremap_id' => 'featuremap_id',
      ),
    ),
    'stock' => 
    array (
      'table' => 'stock',
      'columns' => 
      array (
        'stock_id' => 'stock_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_featureposprop()
 *
 * Purpose: To describe the structure of 'featureposprop' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'featureposprop' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_featureposprop() {
  $description = array (
  'table' => 'featureposprop',
  'fields' => 
  array (
    'featureposprop_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'featurepos_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'type_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'value' => 
    array (
      'type' => 'text',
      'not null' => false,
    ),
    'rank' => 
    array (
      'type' => 'int',
      'not null' => true,
      'default' => 0,
    ),
  ),
  'primary key' => 
  array (
    0 => 'featureposprop_id',
  ),
  'unique keys' => 
  array (
    'featureposprop_id' => 
    array (
      0 => 'featurepos_id',
      1 => 'type_id',
      2 => 'rank',
    ),
  ),
  'indexes' => 
  array (
    'featureposprop_c1' => 
    array (
      0 => 'featurepos_id',
    ),
    'featureposprop_idx2' => 
    array (
      0 => 'type_id',
    ),
  ),
  'foreign keys' => 
  array (
    'cvterm' => 
    array (
      'table' => 'cvterm',
      'columns' => 
      array (
        'type_id' => 'cvterm_id',
      ),
    ),
    'featurepos' => 
    array (
      'table' => 'featurepos',
      'columns' => 
      array (
        'featurepos_id' => 'featurepos_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_feature_project()
 *
 * Purpose: To describe the structure of 'feature_project' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'feature_project' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_feature_project() {
  $description = array(
    'description' => '',
    'table' => 'feature_project',
    'foreign keys' => array(
      'feature' => array(
        'table' => 'feature',
        'columns' => array(
          'feature_id' => 'feature_id',
        ),
      ),
      'project' => array(
        'table' => 'project',
        'columns' => array(
          'project_id' => 'project_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_feature_stock()
 *
 * Purpose: To describe the structure of 'feature_stock' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'feature_stock' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_feature_stock() {
  $description = array (
  'table' => 'feature_stock',
  'fields' => 
  array (
    'feature_stock_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'feature_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'stock_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'feature_stock_id',
  ),
  'unique keys' => 
  array (
    'feature_stock_c1' => 
    array (
      0 => 'feature_id',
      1 => 'stock_id',
    ),
  ),
  'foreign keys' => 
  array (
    'feature' => 
    array (
      'table' => 'feature',
      'columns' => 
      array (
        'feature_id' => 'feature_id',
      ),
    ),
    'stock' => 
    array (
      'table' => 'stock',
      'columns' => 
      array (
        'stock_id' => 'stock_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_genotypeprop()
 *
 * Purpose: To describe the structure of 'genotypeprop' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'genotypeprop' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_genotypeprop() {
  $description = array(
    'description' => '',
    'table' => 'genotypeprop',
    'fields' => array(
      'genotypeprop_id' => array(
        'size' => 'big',
        'type' => 'serial',
        'not null' => TRUE,
      ),
      'genotype_id' => array(
        'size' => 'big',
        'type' => 'int',
        'not null' => TRUE,
      ),
      'type_id' => array(
        'size' => 'big',
        'type' => 'int',
        'not null' => TRUE,
      ),
      'value' => array(
        'size' => 'normal',
        'type' => 'text',
        'not null' => FALSE,
      ),
      'rank' => array(
        'size' => 'normal',
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
      ),
    ),
    'unique keys' => array(
      'genotypeprop_c1' => array(
        0 => 'genotype_id',
        1 => 'type_id',
        2 => 'rank',
      ),
    ),
    'indexes' => array(
      'genotypeprop_idx1' => array(
        0 => 'genotype_id',
      ),
      'genotypeprop_idx2' => array(
        0 => 'type_id',
      ),
    ),
    'primary key' => array(
      0 => 'genotypeprop_id',
    ),
    'foreign keys' => array(
      'genotype' => array(
        'table' => 'genotype',
        'columns' => array(
          'genotype_id' => 'genotype_id',
        ),
      ),
      'cvterm' => array(
        'table' => 'cvterm',
        'columns' => array(
          'type_id' => 'cvterm_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_library_contact()
 *
 * Purpose: To describe the structure of 'library_contact' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'library_contact' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_library_contact() {
  $description = array (
  'table' => 'library_contact',
  'fields' => 
  array (
    'library_contact_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'library_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'contact_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'library_contact_id',
  ),
  'unique keys' => 
  array (
    'library_contact_c1' => 
    array (
      0 => 'library_id',
      1 => 'contact_id',
    ),
  ),
  'foreign keys' => 
  array (
    'library' => 
    array (
      'table' => 'library',
      'columns' => 
      array (
        'library_id' => 'library_id',
      ),
    ),
    'contact' => 
    array (
      'table' => 'contact',
      'columns' => 
      array (
        'contact_id' => 'contact_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_library_stock()
 *
 * Purpose: To describe the structure of 'library_stock' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'library_stock' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_library_stock() {
  $description = array (
  'table' => 'library_stock',
  'fields' => 
  array (
    'library_stock_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'library_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'stock_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'library_stock_id',
  ),
  'unique keys' => 
  array (
    'library_stock_c1' => 
    array (
      0 => 'library_id',
      1 => 'stock_id',
    ),
  ),
  'foreign keys' => 
  array (
    'library' => 
    array (
      'table' => 'library',
      'columns' => 
      array (
        'library_id' => 'library_id',
      ),
    ),
    'stock' => 
    array (
      'table' => 'stock',
      'columns' => 
      array (
        'stock_id' => 'stock_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_organism_image()
 *
 * Purpose: To describe the structure of 'organism_image' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'organism_image' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_organism_image() {
  $description = array(
    'description' => '',
    'table' => 'organism_image',
    'foreign keys' => array(
      'organism' => array(
        'table' => 'organism',
        'columns' => array(
          'organism_id' => 'organism_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_organismprop()
 *
 * Purpose: To describe the structure of 'organismprop' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'organismprop' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_organismprop() {
  $description = array (
  'table' => 'organismprop',
  'fields' => 
  array (
    'organismprop_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'organism_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'type_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'value' => 
    array (
      'type' => 'text',
      'not null' => false,
    ),
    'rank' => 
    array (
      'type' => 'int',
      'not null' => true,
      'default' => 0,
    ),
  ),
  'primary key' => 
  array (
    0 => 'organismprop_id',
  ),
  'unique keys' => 
  array (
    'organismprop_c1' => 
    array (
      0 => 'organism_id',
      1 => 'type_id',
      2 => 'rank',
    ),
  ),
  'indexes' => 
  array (
    'organismprop_idx1' => 
    array (
      0 => 'organism_id',
    ),
    'organismprop_idx2' => 
    array (
      0 => 'type_id',
    ),
  ),
  'foreign keys' => 
  array (
    'cvterm' => 
    array (
      'table' => 'cvterm',
      'columns' => 
      array (
        'type_id' => 'cvterm_id',
      ),
    ),
    'organism' => 
    array (
      'table' => 'organism',
      'columns' => 
      array (
        'organism_id' => 'organism_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_organism_relationship()
 *
 * Purpose: To describe the structure of 'organism_relationship' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'organism_relationship' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_organism_relationship() {
  $description = array (
  'fields' => 
  array (
    'organism_relationship_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'type_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'subject_organism_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'object_organism_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'organism_relationship_id',
  ),
  'unique keys' => 
  array (
    'organism_relationship_c1' => 
    array (
      0 => 'subject_organism_id',
      1 => 'object_organism_id',
      2 => 'type_id',
    ),
  ),
  'indexes' => 
  array (
    'organism_relationship_idx1' => 
    array (
      0 => 'type_id',
    ),
    'organism_relationship_idx2' => 
    array (
      0 => 'subject_organism_id',
    ),
    'organism_relationship_idx3' => 
    array (
      0 => 'object_organism_id',
    ),
  ),
  'foreign keys' => 
  array (
    'cvterm' => 
    array (
      'table' => 'cvterm',
      'columns' => 
      array (
        'type_id' => 'cvterm_id',
      ),
    ),
    'organism' => 
    array (
      'table' => 'organism',
      'columns' => 
      array (
        'subject_organism_id' => 'organism_id',
        'object_organism_id' => 'organism_id',
      ),
    ),
  ),
  'table' => 'organism_relationship',
  'referring_tables' => NULL,
);
  return $description;
}
/**
 * Implements hook_chado_schema_phenstatementprop()
 *
 * Purpose: To describe the structure of 'phenstatementprop' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'phenstatementprop' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_phenstatementprop() {
  $description = array(
    'description' => '',
    'table' => 'phenstatementprop',
    'foreign keys' => array(
      'cvterm' => array(
        'table' => 'cvterm',
        'columns' => array(
          'type_id' => 'cvterm_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_pubauthor_contact()
 *
 * Purpose: To describe the structure of 'pubauthor_contact' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'pubauthor_contact' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_pubauthor_contact() {
  $description = array (
  'table' => 'pubauthor_contact',
  'fields' => 
  array (
    'pubauthor_contact_id' => 
    array (
      'type' => 'serial',
      'not null' => true,
    ),
    'contact_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
    'pubauthor_id' => 
    array (
      'type' => 'int',
      'not null' => true,
    ),
  ),
  'primary key' => 
  array (
    0 => 'pubauthor_contact_id',
  ),
  'unique keys' => 
  array (
    'pubauthor_contact_c1' => 
    array (
      0 => 'contact_id',
      1 => 'pubauthor_id',
    ),
  ),
  'foreign keys' => 
  array (
    'contact' => 
    array (
      'table' => 'contact',
      'columns' => 
      array (
        'contact_id' => 'contact_id',
      ),
    ),
    'pubauthor' => 
    array (
      'table' => 'pubauthor',
      'columns' => 
      array (
        'pubauthor_id' => 'pubauthor_id',
      ),
    ),
  ),
);
  return $description;
}
/**
 * Implements hook_chado_schema_pub_image()
 *
 * Purpose: To describe the structure of 'pub_image' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'pub_image' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_pub_image() {
  $description = array(
    'description' => '',
    'table' => 'pub_image',
    'foreign keys' => array(
      'cvterm' => array(
        'table' => 'cvterm',
        'columns' => array(
          'type_id' => 'cvterm_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_stockcollection_db()
 *
 * Purpose: To describe the structure of 'stockcollection_db' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'stockcollection_db' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_stockcollection_db() {
  $description = array(
    'description' => '',
    'table' => 'stockcollection_db',
    'fields' => array(
      'stockcollection_db_id' => array(
        'size' => 'big',
        'type' => 'serial',
        'not null' => TRUE,
      ),
      'stockcollection_id' => array(
        'size' => 'big',
        'type' => 'int',
        'not null' => TRUE,
      ),
      'db_id' => array(
        'size' => 'big',
        'type' => 'int',
        'not null' => TRUE,
      ),
    ),
    'unique keys' => array(
      'stockcollection_db_c1' => array(
        0 => 'stockcollection_id',
        1 => 'db_id',
      ),
    ),
    'indexes' => array(
      'stockcollection_db_idx1' => array(
        0 => 'stockcollection_id',
      ),
      'stockcollection_db_idx2' => array(
        0 => 'db_id',
      ),
    ),
    'primary key' => array(
      0 => 'stockcollection_db_id',
    ),
    'foreign keys' => array(
      'db' => array(
        'table' => 'db',
        'columns' => array(
          'db_id' => 'db_id',
        ),
      ),
      'stockcollection' => array(
        'table' => 'stockcollection',
        'columns' => array(
          'stockcollection_id' => 'stockcollection_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_stock_image()
 *
 * Purpose: To describe the structure of 'stock_image' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'stock_image' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_stock_image() {
  $description = array(
    'description' => '',
    'table' => 'stock_image',
    'foreign keys' => array(
      'eimage' => array(
        'table' => 'eimage',
        'columns' => array(
          'eimage_id' => 'eimage_id',
        ),
      ),
      'stock' => array(
        'table' => 'stock',
        'columns' => array(
          'stock_id' => 'stock_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Implements hook_chado_schema_stock_project()
 *
 * Purpose: To describe the structure of 'stock_project' to tripal
 * @see chado_insert_record()
 * @see chado_update_record()
 * @see chado_select_record()
 * @see chado_generate_var()
 * @see chado_expan_var()
 *
 * @return
 *    An array describing the 'stock_project' table
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_schema_stock_project() {
  $description = array(
    'description' => '',
    'table' => 'stock_project',
    'foreign keys' => array(
      'stock' => array(
        'table' => 'stock',
        'columns' => array(
          'stock_id' => 'stock_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
  );
  return $description;
}
/**
 * Lists the table names in the v1.3 chado schema
 *
 * @return
 *    An array containing all of the table names
 *
 * @ingroup tripal_chado_schema_api
 *
 */
function mainlab_tripal_chado_get_tables() {
  $tables = array(
    'analysis_organism',
    'contact_image',
    'contactprop',
    'eimageprop',
    'feature_contact',
    'feature_image',
    'featuremap_contact',
    'featuremap_dbxref',
    'featuremap_organism',
    'featuremapprop',
    'featuremap_stock',
    'featureposprop',
    'feature_project',
    'feature_stock',
    'genotypeprop',
    'library_contact',
    'library_stock',
    'organism_image',
    'organismprop',
    'organism_relationship',
    'phenstatementprop',
    'pubauthor_contact',
    'pub_image',
    'stockcollection_db',
    'stock_image',
    'stock_project',
  );
  return $tables;
}

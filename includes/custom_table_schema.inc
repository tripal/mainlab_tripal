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
  $description = array(
    'description' => '',
    'table' => 'analysis_organism',
    'foreign keys' => array(
      'analysis' => array(
        'table' => 'analysis',
        'columns' => array(
          'analysis_id' => 'analysis_id',
        ),
      ),
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
  $description = array(
    'description' => '',
    'table' => 'contactprop',
    'foreign keys' => array(
      'contact' => array(
        'table' => 'contact',
        'columns' => array(
          'contact_id' => 'contact_id',
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
  $description = array(
    'description' => '',
    'table' => 'eimageprop',
    'foreign keys' => array(
    ),
    'referring_tables' => array(
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
  $description = array(
    'description' => '',
    'table' => 'feature_contact',
    'foreign keys' => array(
      'contact' => array(
        'table' => 'contact',
        'columns' => array(
          'contact_id' => 'contact_id',
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
  $description = array(
    'description' => '',
    'table' => 'featuremap_contact',
    'foreign keys' => array(
      'contact' => array(
        'table' => 'contact',
        'columns' => array(
          'contact_id' => 'contact_id',
        ),
      ),
      'featuremap' => array(
        'table' => 'featuremap',
        'columns' => array(
          'featuremap_id' => 'featuremap_id',
        ),
      ),
    ),
    'referring_tables' => array(
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
  $description = array(
    'description' => '',
    'table' => 'featuremap_dbxref',
    'foreign keys' => array(
      'dbxref' => array(
        'table' => 'dbxref',
        'columns' => array(
          'dbxref_id' => 'dbxref_id',
        ),
      ),
      'featuremap' => array(
        'table' => 'featuremap',
        'columns' => array(
          'featuremap_id' => 'featuremap_id',
        ),
      ),
    ),
    'referring_tables' => array(
    ),
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
  $description = array(
    'description' => '',
    'table' => 'featuremap_organism',
    'foreign keys' => array(
      'featuremap' => array(
        'table' => 'featuremap',
        'columns' => array(
          'featuremap_id' => 'featuremap_id',
        ),
      ),
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
  $description = array(
    'description' => '',
    'table' => 'featuremapprop',
    'foreign keys' => array(
      'featuremap' => array(
        'table' => 'featuremap',
        'columns' => array(
          'featuremap_id' => 'featuremap_id',
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
  $description = array(
    'description' => '',
    'table' => 'featuremap_stock',
    'foreign keys' => array(
      'featuremap' => array(
        'table' => 'featuremap',
        'columns' => array(
          'featuremap_id' => 'featuremap_id',
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
  $description = array(
    'description' => '',
    'table' => 'featureposprop',
    'foreign keys' => array(
      'featurepos' => array(
        'table' => 'featurepos',
        'columns' => array(
          'featurepos_id' => 'featurepos_id',
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
  $description = array(
    'description' => '',
    'table' => 'feature_stock',
    'foreign keys' => array(
      'feature' => array(
        'table' => 'feature',
        'columns' => array(
          'feature_id' => 'feature_id',
        ),
      ),
      'stock' => array(
        'table' => 'stock',
        'columns' => array(
          'stock_id' => 'stock_id',
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
  $description = array(
    'description' => '',
    'table' => 'library_contact',
    'foreign keys' => array(
    ),
    'referring_tables' => array(
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
  $description = array(
    'description' => '',
    'table' => 'library_stock',
    'foreign keys' => array(
    ),
    'referring_tables' => array(
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
  $description = array(
    'description' => '',
    'table' => 'organismprop',
    'foreign keys' => array(
      'organism' => array(
        'table' => 'organism',
        'columns' => array(
          'organism_id' => 'organism_id',
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
  $description = array(
    'description' => '',
    'table' => 'organism_relationship',
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
  $description = array(
    'description' => '',
    'table' => 'pubauthor_contact',
    'foreign keys' => array(
      'contact' => array(
        'table' => 'contact',
        'columns' => array(
          'contact_id' => 'contact_id',
        ),
      ),
      'pubauthor' => array(
        'table' => 'pubauthor',
        'columns' => array(
          'pubauthor_id' => 'pubauthor_id',
        ),
      ),
    ),
    'referring_tables' => array(
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
    'foreign keys' => array(
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

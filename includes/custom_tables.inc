<?php 
require_once 'custom_table_schema.inc';

function mainlab_tripal_add_custom_tables () {
  // Table: chado.analysis_organism
  if (!db_table_exists('chado.analysis_organism')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.analysis_organism (
          analysis_id integer NOT NULL,
          organism_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.analysis_organism
          ADD CONSTRAINT analysis_organism_organism_id_fkey FOREIGN KEY (organism_id) REFERENCES chado.organism(organism_id) ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;
    ";
    db_query($sql);
  }
  // Table: chado.contact_image
  if (!db_table_exists('chado.contact_image')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.contact_image (
          contact_image_id integer NOT NULL,
          contact_id integer NOT NULL,
          eimage_id integer NOT NULL,
          type_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.contact_image
          ADD CONSTRAINT contact_image_pkey PRIMARY KEY (contact_image_id);
      ALTER TABLE ONLY chado.contact_image
          ADD CONSTRAINT contact_image_contact_id_eimage_id_type_id_key UNIQUE (contact_id, eimage_id, type_id);
      ALTER TABLE ONLY chado.contact_image
          ADD CONSTRAINT contact_image_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id);
    ";
    db_query($sql);
  }
  // Table: chado.contactprop
  if (!db_table_exists('chado.contactprop')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.contactprop (
          contactprop_id integer NOT NULL,
          contact_id integer NOT NULL,
          type_id integer NOT NULL,
          value text,
          rank integer DEFAULT 0 NOT NULL
      );
      ALTER TABLE ONLY chado.contactprop
          ADD CONSTRAINT contactprop_pkey PRIMARY KEY (contactprop_id);
      ALTER TABLE ONLY chado.contactprop
          ADD CONSTRAINT contactprop_c1 UNIQUE (contact_id, type_id, rank);
      ALTER TABLE ONLY chado.contactprop
          ADD CONSTRAINT contactprop_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.eimageprop
  if (!db_table_exists('chado.eimageprop')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.eimageprop (
          eimageprop_id integer NOT NULL,
          eimage_id integer NOT NULL,
          type_id integer NOT NULL,
          value text,
          rank integer DEFAULT 0
      );
      ALTER TABLE ONLY chado.eimageprop
          ADD CONSTRAINT eimageprop_pkey PRIMARY KEY (eimageprop_id);
      ALTER TABLE ONLY chado.eimageprop
          ADD CONSTRAINT eimageprop_eimage_id_type_id_rank_key UNIQUE (eimage_id, type_id, rank);
      ALTER TABLE ONLY chado.eimageprop
          ADD CONSTRAINT eimageprop_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.feature_contact
  if (!db_table_exists('chado.feature_contact')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.feature_contact (
          feature_contact_id integer NOT NULL,
          feature_id integer NOT NULL,
          contact_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.feature_contact
          ADD CONSTRAINT feature_contact_pkey PRIMARY KEY (feature_contact_id);
      ALTER TABLE ONLY chado.feature_contact
          ADD CONSTRAINT feature_contact_c1 UNIQUE (feature_id, contact_id);
      ALTER TABLE ONLY chado.feature_contact
          ADD CONSTRAINT feature_contact_feature_id_fkey FOREIGN KEY (feature_id) REFERENCES chado.feature(feature_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.feature_image
  if (!db_table_exists('chado.feature_image')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.feature_image (
          feature_image_id integer NOT NULL,
          feature_id integer NOT NULL,
          eimage_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.feature_image
          ADD CONSTRAINT feature_image_pkey PRIMARY KEY (feature_image_id);
      ALTER TABLE ONLY chado.feature_image
          ADD CONSTRAINT feature_image_feature_id_eimage_id_key UNIQUE (feature_id, eimage_id);
      ALTER TABLE ONLY chado.feature_image
          ADD CONSTRAINT feature_image_feature_id_fkey FOREIGN KEY (feature_id) REFERENCES chado.feature(feature_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.feature_project
  if (!db_table_exists('chado.feature_project')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.feature_project (
          feature_project_id integer NOT NULL,
          feature_id integer NOT NULL,
          project_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.feature_project
          ADD CONSTRAINT feature_project_pkey PRIMARY KEY (feature_project_id);
      ALTER TABLE ONLY chado.feature_project
          ADD CONSTRAINT feature_project_feature_id_project_id_key UNIQUE (feature_id, project_id);
      ALTER TABLE ONLY chado.feature_project
          ADD CONSTRAINT feature_project_project_id_fkey FOREIGN KEY (project_id) REFERENCES chado.project(project_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.feature_stock
  if (!db_table_exists('chado.feature_stock')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.feature_stock (
          feature_stock_id integer NOT NULL,
          feature_id integer NOT NULL,
          stock_id integer NOT NULL,
          type_id integer
      );
      ALTER TABLE ONLY chado.feature_stock
          ADD CONSTRAINT feature_stock_pkey PRIMARY KEY (feature_stock_id);
      ALTER TABLE ONLY chado.feature_stock
          ADD CONSTRAINT feature_stock_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.featuremap_contact
  if (!db_table_exists('chado.featuremap_contact')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.featuremap_contact (
          featuremap_contact_id integer NOT NULL,
          featuremap_id integer NOT NULL,
          contact_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.featuremap_contact
          ADD CONSTRAINT featuremap_contact_pkey PRIMARY KEY (featuremap_contact_id);
      ALTER TABLE ONLY chado.featuremap_contact
          ADD CONSTRAINT featuremap_contact_c1 UNIQUE (featuremap_id, contact_id);
      ALTER TABLE ONLY chado.featuremap_contact
          ADD CONSTRAINT featuremap_contact_feature_id_fkey FOREIGN KEY (featuremap_id) REFERENCES chado.featuremap(featuremap_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.featuremap_dbxref
  if (!db_table_exists('chado.featuremap_dbxref')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.featuremap_dbxref (
          featuremap_dbxref_id integer DEFAULT nextval('featuremap_dbxref_featuremap_dbxref_id'::regclass) NOT NULL,
          featuremap_id integer NOT NULL,
          dbxref_id integer NOT NULL,
          is_current boolean DEFAULT true NOT NULL
      );
    ";
    db_query($sql);
  }
  // Table: chado.featuremap_organism
  if (!db_table_exists('chado.featuremap_organism')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.featuremap_organism (
          featuremap_organism_id integer NOT NULL,
          featuremap_id integer NOT NULL,
          organism_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.featuremap_organism
          ADD CONSTRAINT featuremap_organism_pkey PRIMARY KEY (featuremap_organism_id);
      ALTER TABLE ONLY chado.featuremap_organism
          ADD CONSTRAINT featuremap_organism_c1 UNIQUE (featuremap_id, organism_id);
      ALTER TABLE ONLY chado.featuremap_organism
          ADD CONSTRAINT featuremap_organism_organism_id_fkey FOREIGN KEY (organism_id) REFERENCES chado.organism(organism_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.featuremap_stock
  if (!db_table_exists('chado.featuremap_stock')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.featuremap_stock (
          featuremap_stock_id integer NOT NULL,
          featuremap_id integer NOT NULL,
          stock_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.featuremap_stock
          ADD CONSTRAINT featuremap_stock_pkey PRIMARY KEY (featuremap_stock_id);
      ALTER TABLE ONLY chado.featuremap_stock
          ADD CONSTRAINT featuremap_stock_c1 UNIQUE (featuremap_id, stock_id);
      ALTER TABLE ONLY chado.featuremap_stock
          ADD CONSTRAINT featuremap_stock_stock_id_fkey FOREIGN KEY (stock_id) REFERENCES chado.stock(stock_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.featuremapprop
  if (!db_table_exists('chado.featuremapprop')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.featuremapprop (
          featuremapprop_id integer NOT NULL,
          featuremap_id integer NOT NULL,
          type_id integer NOT NULL,
          value text,
          rank integer DEFAULT 0 NOT NULL
      );
      ALTER TABLE ONLY chado.featuremapprop
          ADD CONSTRAINT featuremapprop_pkey PRIMARY KEY (featuremapprop_id);
      ALTER TABLE ONLY chado.featuremapprop
          ADD CONSTRAINT featuremapprop_c1 UNIQUE (featuremap_id, type_id, rank);
      ALTER TABLE ONLY chado.featuremapprop
          ADD CONSTRAINT featuremapprop_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.featureposprop
  if (!db_table_exists('chado.featureposprop')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.featureposprop (
          featureposprop_id integer NOT NULL,
          featurepos_id integer NOT NULL,
          type_id integer NOT NULL,
          value text,
          rank integer DEFAULT 0 NOT NULL
      );
      ALTER TABLE ONLY chado.featureposprop
          ADD CONSTRAINT featureposprop_pkey PRIMARY KEY (featureposprop_id);
      ALTER TABLE ONLY chado.featureposprop
          ADD CONSTRAINT featureposprop_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.genotypeprop
  if (!db_table_exists('chado.genotypeprop')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.genotypeprop (
          genotypeprop_id integer NOT NULL,
          genotype_id integer NOT NULL,
          type_id integer NOT NULL,
          value text,
          rank integer DEFAULT 0 NOT NULL
      );
      ALTER TABLE ONLY chado.genotypeprop
          ADD CONSTRAINT genotypeprop_pkey PRIMARY KEY (genotypeprop_id);
      ALTER TABLE ONLY chado.genotypeprop
          ADD CONSTRAINT genotypeprop_c1 UNIQUE (genotype_id, type_id, rank);
      ALTER TABLE ONLY chado.genotypeprop
          ADD CONSTRAINT genotypeprop_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;
    ";
    db_query($sql);
  }
  // Table: chado.library_contact
  if (!db_table_exists('chado.library_contact')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.library_contact (
          library_contact_id integer NOT NULL,
          library_id integer NOT NULL,
          contact_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.library_contact
          ADD CONSTRAINT library_contact_pkey PRIMARY KEY (library_contact_id);
      ALTER TABLE ONLY chado.library_contact
          ADD CONSTRAINT library_contact_library_contact_c1_key UNIQUE (library_id, contact_id);
    ";
    db_query($sql);
  }
  // Table: chado.library_stock
  if (!db_table_exists('chado.library_stock')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.library_stock (
          library_stock_id integer NOT NULL,
          library_id integer NOT NULL,
          stock_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.library_stock
          ADD CONSTRAINT library_stock_pkey PRIMARY KEY (library_stock_id);
      ALTER TABLE ONLY chado.library_stock
          ADD CONSTRAINT library_stock_library_stock_c1_key UNIQUE (library_id, stock_id);
    ";
    db_query($sql);
  }
  // Table: chado.organism_image
  if (!db_table_exists('chado.organism_image')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.organism_image (
          organism_image_id integer NOT NULL,
          organism_id integer NOT NULL,
          eimage_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.organism_image
          ADD CONSTRAINT organism_image_pkey PRIMARY KEY (organism_image_id);
      ALTER TABLE ONLY chado.organism_image
          ADD CONSTRAINT organism_image_organism_id_eimage_id_key UNIQUE (organism_id, eimage_id);
      ALTER TABLE ONLY chado.organism_image
          ADD CONSTRAINT organism_image_organism_id_fkey FOREIGN KEY (organism_id) REFERENCES chado.organism(organism_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.organism_relationship
  if (!db_table_exists('chado.organism_relationship')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.organism_relationship (
          organism_relationship_id integer NOT NULL,
          subject_organism_id integer NOT NULL,
          object_organism_id integer NOT NULL,
          type_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.organism_relationship
          ADD CONSTRAINT organism_relationship_pkey PRIMARY KEY (organism_relationship_id);
      ALTER TABLE ONLY chado.organism_relationship
          ADD CONSTRAINT organism_relationship_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.organismprop
  if (!db_table_exists('chado.organismprop')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.organismprop (
          organismprop_id integer NOT NULL,
          organism_id integer NOT NULL,
          type_id integer NOT NULL,
          value text,
          rank integer DEFAULT 0 NOT NULL
      );
      ALTER TABLE ONLY chado.organismprop
          ADD CONSTRAINT organismprop_pkey PRIMARY KEY (organismprop_id);
      ALTER TABLE ONLY chado.organismprop
          ADD CONSTRAINT organismprop_c1 UNIQUE (organism_id, type_id, rank);
      ALTER TABLE ONLY chado.organismprop
          ADD CONSTRAINT organismprop_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;
    ";
    db_query($sql);
  }
  // Table: chado.phenstatementprop
  if (!db_table_exists('chado.phenstatementprop')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.phenstatementprop (
          phenstatementprop_id integer NOT NULL,
          phenstatement_id integer NOT NULL,
          type_id integer NOT NULL,
          value text,
          rank integer DEFAULT 0 NOT NULL
      );
      ALTER TABLE ONLY chado.phenstatementprop
          ADD CONSTRAINT phenstatementprop_pkey PRIMARY KEY (phenstatementprop_id);
      ALTER TABLE ONLY chado.phenstatementprop
          ADD CONSTRAINT phenstatementprop_phenstatement_id_type_id_rank_key UNIQUE (phenstatement_id, type_id, rank);
      ALTER TABLE ONLY chado.phenstatementprop
          ADD CONSTRAINT phenstatementprop_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.pub_image
  if (!db_table_exists('chado.pub_image')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.pub_image (
          pub_image_id integer NOT NULL,
          pub_id integer NOT NULL,
          eimage_id integer NOT NULL,
          type_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.pub_image
          ADD CONSTRAINT pub_image_pkey PRIMARY KEY (pub_image_id);
      ALTER TABLE ONLY chado.pub_image
          ADD CONSTRAINT pub_image_type_id_fkey FOREIGN KEY (type_id) REFERENCES chado.cvterm(cvterm_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.pubauthor_contact
  if (!db_table_exists('chado.pubauthor_contact')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.pubauthor_contact (
          pubauthor_contact_id integer NOT NULL,
          contact_id integer NOT NULL,
          pubauthor_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.pubauthor_contact
          ADD CONSTRAINT pubauthor_contact_pkey PRIMARY KEY (pubauthor_contact_id);
      ALTER TABLE ONLY chado.pubauthor_contact
          ADD CONSTRAINT pubauthor_contact_pubauthor_contact_c1_key UNIQUE (contact_id, pubauthor_id);
    ";
    db_query($sql);
  }
  // Table: chado.stock_image
  if (!db_table_exists('chado.stock_image')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.stock_image (
          stock_image_id integer NOT NULL,
          stock_id integer NOT NULL,
          eimage_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.stock_image
          ADD CONSTRAINT stock_image_pkey PRIMARY KEY (stock_image_id);
      ALTER TABLE ONLY chado.stock_image
          ADD CONSTRAINT stock_image_stock_id_fkey FOREIGN KEY (stock_id) REFERENCES chado.stock(stock_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  // Table: chado.stock_project
  if (!db_table_exists('chado.stock_project')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.stock_project (
          stock_project_id integer NOT NULL,
          stock_id integer NOT NULL,
          project_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.stock_project
          ADD CONSTRAINT stock_project_pkey PRIMARY KEY (stock_project_id);
      ALTER TABLE ONLY chado.stock_project
          ADD CONSTRAINT stock_project_stock_id_fkey FOREIGN KEY (stock_id) REFERENCES chado.stock(stock_id);
    ";
    db_query($sql);
  }
  // Table: chado.stockcollection_db
  if (!db_table_exists('chado.stockcollection_db')) {
    $sql = "
      CREATE TABLE IF NOT EXISTS chado.stockcollection_db (
          stockcollection_db_id integer NOT NULL,
          stockcollection_id integer NOT NULL,
          db_id integer NOT NULL
      );
      ALTER TABLE ONLY chado.stockcollection_db
          ADD CONSTRAINT stockcollection_db_pkey PRIMARY KEY (stockcollection_db_id);
      ALTER TABLE ONLY chado.stockcollection_db
          ADD CONSTRAINT stockcollection_db_stockcollection_id_db_id_key UNIQUE (stockcollection_id, db_id);
      ALTER TABLE ONLY chado.stockcollection_db
          ADD CONSTRAINT stockcollection_db_stockcollection_id_fkey FOREIGN KEY (stockcollection_id) REFERENCES chado.stockcollection(stockcollection_id) ON DELETE CASCADE;
    ";
    db_query($sql);
  }
  
  // Describe the custom tables to Drupal
  mainlab_tripal_describe_custom_tables ();
}

function mainlab_tripal_describe_custom_tables () {
  $tables = mainlab_tripal_chado_get_tables();
  foreach ($tables AS $table) {
    $schema = chado_get_schema($table);
    if (!is_array($schema) || !key_exists('table', $schema)) {
      $func = 'mainlab_tripal_chado_schema_' . $table;
      $desc = $func();
      chado_create_custom_table($table, $desc);
    }
  }
}

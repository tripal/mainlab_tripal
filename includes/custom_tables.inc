<?php 
require_once 'custom_table_schema.inc';

function mainlab_tripal_add_custom_tables () {
  $tables = mainlab_tripal_chado_get_tables();
  foreach ($tables AS $table) {
    $func = 'mainlab_tripal_chado_schema_' . $table;
    $desc = $func();
    chado_create_custom_table($table, $desc);
  }
}
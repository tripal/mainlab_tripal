<?php
/*
 * Create a polymorphism page that lists all genotypes (i.e. polymorphisms) by using a marker's feature_id (i.e. $feature_id)
 */
function mainlab_show_polymorphism ($feature_id) {  
  return theme('mainlab_polymorphism', array('feature_id' => $feature_id));
}
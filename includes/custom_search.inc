<?php
// A function to rewrite the SQL statement before submitting the query
// Force GROUP BY feature_id and return only one row

function  mainlab_tripal_views_pre_execute(&$view){
	if ($view->name == 'qtl_search') {
      $query = $view->build_info['query'];
      $count_query = $view->build_info['count_query'];
      
      // Replace the query for searching aliases in the Trait name field
      $exactly = "UPPER(qtl_search.aliases) = UPPER('%s')";
      $startsw = "UPPER(qtl_search.aliases) LIKE UPPER('%s%%')";
      $endsw = "UPPER(qtl_search.aliases) LIKE UPPER('%%%s')";
      
      $rp_exactly = "UPPER(qtl_search.aliases) LIKE UPPER('%%::%s::%%')";
      $rp_startsw = "UPPER(qtl_search.aliases) LIKE UPPER('%%::%s%%::%%')";
      $rp_endsw = "UPPER(qtl_search.aliases) LIKE UPPER('%%::%%%s::%%')";
      
      $query = str_replace($exactly, $rp_exactly, $query);
      $count_query = str_replace($exactly, $rp_exactly, $query);
		
      $query = str_replace($startsw, $rp_startsw, $query);
      $count_query = str_replace($startsw, $rp_startsw, $query);
      
      $query = str_replace($endsw, $rp_endsw, $query);
      $count_query = str_replace($endsw, $rp_endsw, $query);
      
      // Replace the query for the category filter
      $select = "(qtl_search.category) = ('%s')";
      $rp_select = "(qtl_search.category_filter) LIKE ('%%::%s::%%')";
      
      $query = str_replace($select, $rp_select, $query);
      $count_query = str_replace($select, $rp_select, $query);
      
      // Now return the query to Drupal Views
      $view->build_info['query'] = $query;
      $view->build_info['count_query'] = $count_query;
      
		//drupal_set_message($view->build_info['query']);
		//drupal_set_message($view->build_info['count_query']);
		
    // Adding alias search for stock name
	} else if ($view->name == 'search_by_collection') {
      $query = $view->build_info['query'];
      $count_query = $view->build_info['count_query'];

      $data = $view->exposed_data;
      $stock_name = $data['stock_name'];
      if ($stock_name) {
        preg_match('/(WHERE .+) AND .+/', $query, $matches);
        $where = $matches[1];
        if (!$where) {
        	preg_match('/(WHERE .+)/', $query, $matches);
        	$where = $matches[1];
        }
        $where = str_replace('WHERE', 'WHERE (', $where);
        $where .= 
        " OR (stock_id 
          IN (SELECT distinct stock_id FROM stockprop 
                WHERE type_id = 
                (SELECT cvterm_id FROM cvterm 
                 WHERE name = 'alias' AND cv_id = 
                  (SELECT cv_id FROM cv WHERE name = 'MAIN'))
                AND UPPER(value) = UPPER('$stock_name'))))";
        $query = str_replace($matches[1], $where, $query);
        $count_query = str_replace($matches[1], $where, $count_query);
        // Now return the query to Drupal Views
        $view->build_info['query'] = $query;
        $view->build_info['count_query'] = $count_query;
      }
	}
}
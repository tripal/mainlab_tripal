<?php
/*
 * Create a table that lists all stocks filtering by the ogramisn (i.e. $organism in 'genus-species' format),
 * The table is returned using AJAX so it can be incorporated in a Tripal TOC content box
*/
function mainlab_stock_listing ($organism, $page) {
	$num_per_page = 20;
	$offset = ($page - 1) * $num_per_page;
	$fullname = explode("-", $organism);
	$genus = $fullname [0];
	$species = $fullname [1];
	$sql =
	"SELECT 
		uniquename, 
		(SELECT name FROM {cvterm} WHERE cvterm_id = type_id) AS type,
		(SELECT nid FROM public.chado_stock WHERE stock_id = stock.stock_id) AS nid
	FROM {stock}
	WHERE organism_id = (SELECT organism_id FROM {organism} WHERE genus = '%s' AND species = '%s')
	AND 
	(type_id <> 
		(SELECT cvterm_id FROM {cvterm} WHERE name = 'sample' AND cv_id = 
			(SELECT cv_id FROM {cv} WHERE name = 'MAIN')
		)
	)";
	$count_sql = "SELECT count(*) FROM (" . $sql . ") A";
	$total = db_result(db_query($count_sql, $genus, $species));
	$pages = $total / $num_per_page;
	if ($total % $num_per_page != 0) {
		$pages ++;
	}
	$sql .= " LIMIT $num_per_page OFFSET $offset";
	$result = chado_query($sql, $genus, $species);
	
	// Make the table
	$rows = array();
	$row_count = 0;
	while ($row = db_fetch_object($result)) {
		$class = '';
		if ($row_count % 2 == 0) {
			$class = 'tripal-table-even-row';
		} else {
			$class = 'tripal-table-odd-row';
		}
		if ($row->nid) {
			$rows[] = array('data' => array("<a href=\"/node/$row->nid\">" . $row->uniquename . "</a>", $row->type)
					, 'class' =>$class);
		} else {
			$rows[] = array('data' => array($row->uniquename, $row->type)
					, 'class' =>$class);
		}
		$row_count ++;
	}
	$header = array('Name', 'Type');
	$attribute = array('class' => 'tripal-table tripal-table-horz');
	$table = theme_table($header, $rows, $attribute);
	$pager = "</br><div style=\"float:right;\"><select onChange=\"mainlab_tripal_stock_listing_change_page(this.selectedIndex + 1)\">";
	for ($i = 1; $i <= $pages; $i ++) {
		if ($i == $page) {
			$pager .= "<option selected=\"selected\">$i</option>";
		} else {
			$pager .= "<option>$i</option>";
		}
	}
	$pager .= "</select></div>";
	$stock->total_number = $total;
	$stock->table = "<div id=\"mainlab_tripal-stock_listing-table\">" . $table . $pager . "</div>";
	global $base_url;
	$ajax_link = $base_url . "/stock_listing_page/$organism/";
	$stock->js = 
	"<script type=\"text/javascript\">
   // Switch pages for stock listing
   function mainlab_tripal_stock_listing_change_page (page) {
	   var link = '$ajax_link' + page; 
	   $.ajax({
		   url: link,
		   dataType: 'json',
		   type: 'POST',
		   success: function(data){
				$('#mainlab_tripal-stock_listing-table').html(data);
		   }
		   });
   }
	</script>";
	return $stock;
}

function mainlab_stock_listing_switch_page ($organism, $page) {
	$stock = mainlab_stock_listing  ($organism, $page);
	drupal_json($stock->table);
}
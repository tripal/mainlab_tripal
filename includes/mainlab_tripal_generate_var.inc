<?php
/**
 * @ingroup tripal_stock
 */
function mainlab_tripal_generate_stock_image_var($node) {
  $stock = $node->stock;
  // We want to show maps for this stock and maps of its children
  if (mainlab_tripal_get_site() == 'cottongen') { // CottonGen has image legend stored
    $results = chado_query(
        "SELECT eimage_type, image_uri, value as legend
			 FROM {stock_image} SI
			 INNER JOIN {eimage} I ON SI.eimage_id = I.eimage_id
			 INNER JOIN {eimageprop} IP ON I.eimage_id = IP.eimage_id
			 WHERE IP.type_id = (SELECT cvterm_id
			                                      FROM {cvterm}
			                                      WHERE name = 'legend'
			                                      AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
			                                    )
			AND stock_id = :stock_id",
        array(':stock_id' => $stock->stock_id));
  } else { // Do not pull legends by default (e.g. for GDR)
    $results = chado_query(
        "SELECT eimage_type, image_uri
			 FROM {stock_image} SI
			 INNER JOIN {eimage} I ON SI.eimage_id = I.eimage_id
			 WHERE stock_id = :stock_id",
        array(':stock_id' => $stock->stock_id));
  }
  $images = NULL;
  $counter = 0;
  while ($img = $results->fetchObject()) {
    $images[$counter] = $img;
    $counter ++;
  }
  $stock->images = $images;
  $node->stock = $stock;
  return $node;
}

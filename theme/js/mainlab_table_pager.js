/* 
 * A function to create a pager for an HTML  table
 */
function tripal_table_make_pager(table_id, page, rowsPerPage) {
  var table = document.getElementById(table_id);
    var trs = table.getElementsByTagName("tr");
  if (trs.length > rowsPerPage) {
    var noRows = 0;
    // Count the number of rows <td> (not including header <th>)
    for (var i = 0; i < trs.length; i ++) {
         if (trs[i].getElementsByTagName('th')[0] != null) {
         } else {
             noRows ++;  
         }
       }
    var addPage = noRows % rowsPerPage == 0 ? 0 : 1;
    var noPages = parseInt (noRows / rowsPerPage + addPage);    
    var counter = 0;
    for (var i = 0; i < trs.length; i ++) {
       // Header
         if (trs[i].getElementsByTagName('th')[0] != null) {
       // Rows
         } else {
           var belongsToPage = parseInt(counter / rowsPerPage);
        if (noPages == page || belongsToPage == page) {
          $(trs[i]).show();
        } else {
          $(trs[i]).hide();
        }
           counter ++;  
         }
       }
       // Pager
       var pager_id = table_id + "-pager";
       var pager = document.getElementById(pager_id);
       if (!pager && noPages > 1) {
         var pager = document.createElement('div');
         pager.id = pager_id;
         var select = "<i>Page</i> <select onChange=\"tripal_table_make_pager('" + table_id + "', this.selectedIndex," + rowsPerPage + ");\">";
         for (var i = 0; i < noPages; i ++) {
        select += "<option>" + (i +1) + "</option>";
         }
         select += "<option>All</option>";
         select += "</select>";
         pager.innerHTML = select;
         pager.style.textAlign = "right";
         $(table).after(pager);
       }
  }
}
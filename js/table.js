function loadTable() {
 	document.getElementById('sometext').innerHTML = _ifpritxt.tableview;
	document.getElementById('content').innerHTML = '<table class="table" style="text-align:left;width:100%;font-size:18px;height:300px;font-family:\'PT Sans Narrow\', sans-serif;" data-filtering="true" data-paging="true" data-sorting="true"></table>';

	jQuery(function($){
		$('.table').footable({
			"columns": $.get('data/table/columns.json'),
			"rows": $.get('data/table/articles.json')
		});
	});
}
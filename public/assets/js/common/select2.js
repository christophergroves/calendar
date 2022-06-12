function initSelect2(selectID,width) {

	
	$(selectID).select2({
		  // placeholder: "Name Search",
		//dropdownAutoWidth:true,
		width:width,	
		
	});
}

function initTimetableSelect2(selectName,width){
	var names = [{"id":"0","name":"loading"}];

	function format(item) { return item.name; };
    $("#nameSearchSel").select2({
    		placeholder: "Name Search",
            data:{ results: names, text: 'name' },
			formatSelection: format,
			formatResult: format
    }).on("change", function() { 
		var serviceUsrID = $(this).select2('data').id;
		var serviceUsrName = $(this).select2('data').name;
		nameSearchTimetable("#gridTimetable",serviceUsrID,serviceUsrName);
	});
	
}

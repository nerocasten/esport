var neroDatatables={
	init:function(p){
		this.datePicker(p.datePicker || false);
		this.dataTable();
	},
	datePicker:function(use){
		if(use){
			$('.date-picker').datepicker({
	            autoclose: true
	        });
		}
	},
	handle:function(){
		
	},
	dataTable:function(){
		if (!$().dataTable) {
            return;
        }
        var the=this;
        table = $('#datatable_ajax');
        // initialize a datatable
        dataTable = table.DataTable({
	        "ajax": 'http://localhost/gss-game/out/esport.lc/data.txt',
	        "lengthMenu": [
                [10, 20, 50, 100, 150, -1],
                [10, 20, 50, 100, 150, "All"] // change per page values here
            ],
            loadingMessage: 'Loading...',
	    });

	    the.formFilter(table);
	},
	formFilter: function(table) { //.form-filter
		table.find('select.form-filter',function(){
			
		})
        var tagName=inElm[0].nodeName.toLowerCase();
        switch(tagName){
        	case 'select':
        		inElm.on('change',function(){
        			console.log($(this).val())
        		});
        	break;
        }
    }
}

jQuery(document).ready(function() {
	neroDatatables.init({
		datePicker:true
	});
});
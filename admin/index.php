<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Enrollment test appointment system</title>
<link rel="stylesheet" type="text/css" href="css/flexigrid.css" />
<link rel="stylesheet" type="text/css" href="css/jquery-impromptu.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script type="text/javascript" src="js/jquery.mask.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/flexigrid.js"></script>
<script type="text/javascript" src="js/jquery-impromptu.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	
	$("#flex1").flexigrid
			(
			{
			url: 'read.php',
			dataType: 'json',
			colModel : [
				{display: 'ID', name : 'id', width : 40, sortable : true, align: 'center'},
				{display: 'Date', name : 'date', width : 100, sortable : true, align: 'center'},
				{display: 'Name', name : 'name', width : 70, sortable : true, align: 'center'},
				{display: 'Last name', name : 'lname', width : 100, sortable : true, align: 'center'},
				{display: 'Phone #', name : 'phone', width : 100, sortable : true, align: 'center'},
				{display: 'City', name : 'City', width : 100, sortable : true, align: 'center'},
				{display: 'State', name : 'State', width : 40, sortable : true, align: 'center'},
				{display: 'Zip', name : 'ZIP', width : 60, sortable : true, align: 'center'},
				{display: 'Score', name : 'score', width : 60, sortable : true, align: 'center'},
				{display: 'Test#', name : 'test_num', width : 30, sortable : true, align: 'center'},
				{display: 'Unix date', name : 'app_date', width : 30, sortable : true, align: 'center'}
				],
			buttons : [
				{name: 'Add', bclass: 'add', onpress : test},
				{name: 'Edit', bclass: 'edit', onpress : test},
				{name: 'Delete', bclass: 'delete', onpress : test},
			],
			searchitems : [
				{display: 'Name', name : 'name', isdefault: false},
				{display: 'Last Name', name : 'lname', isdefault: true},
				{display: 'Phone #', name : 'phone', isdefault: false},
				{display: 'Date', name : 'date', isdefault: false}
				],
			sortname: "date",
			sortorder: "asc",
			usepager: true,
			title: 'Enrollment test',
			useRp: true,
			rp: 10,
			showTableToggleBtn: true,
			width: 830,
			height: 550
			}
			);   
	
});

function test(com,grid,value)
{

    if (com=='Delete')
        {
           if($('.trSelected',grid).length>0){
		   if(confirm('Delete ' + $('.trSelected',grid).length + ' items?')){
            var items = $('.trSelected',grid);
            var itemlist ='';
        	for(i=0;i<items.length;i++){
				itemlist+= items[i].id.substr(3)+",";
			}
			$.ajax({
			   type: "POST",
			   dataType: "json",
			   url: "delete.php",
			   data: "items="+itemlist,
			   success: function(data){
			   	alert("Total affected rows: "+data.total);
			   	$("#flex1").flexReload();
			   }
			 });
			}
			} else {
				return false;
			} 
        }
    else if (com=='Save')
        {
		if(grid) {
			$.ajax({
			   type: "POST",
			   dataType: "json",
			   url: "update.php",
			   data: {id: grid, value: value},
			 });
		}           
        }       
	else if(com == 'Add') {

		var prompt_addnew = {
			state0: {
				title: 'Add new ',
				html:'<label>First name<br><input type="text" name="name" value=""></label><br />'+
					'<label>Last name<br><input type="text" name="lname" value=""></label><br />' +
					'<label>Phone #<br><input type="text" name="phone" value="" id="add_phone"></label><br />' +
					'<label>Zip<br><input type="text" name="zip" value="" id="add_zip"></label><br />' +
					'<label>Score<br><input type="text" name="score" value="" id="add_score"></label><br />' +
					'<label>Date<br><input type="text" name="date" value="" id="add_date"></label><br />'
					,
				buttons: { Close: 0 , Add: 1 },
				submit:function(e,v,m,f){ 
					e.preventDefault();
					if(v == 0) {
						$("#flex1").flexReload();
						$.prompt.close();
					}
					else {
						$.ajax({
							type: "POST",
							dataType: "json",
							url: "add.php",
							data: {
								name: f.name,
								lname: f.lname,
								phone: f.phone,
								zip: f.zip,
								score: f.score,
								date: f.date
							},
							success: function(data){
						   		if(data.err == 1) {
									alert("Some of the fields are missing");
								}
								else if(data.err == 2) {
									alert("Double record");
								}
								else {
									alert("Record added");
									$("#flex1").flexReload();
									$.prompt.close();
								}
							}
						 });
					}
				}
			}
		};
		$.prompt(prompt_addnew);
	} 
	else if(com == "Edit") {

		if($('.trSelected',grid).length == 1){
			var items = $('.trSelected',grid);
			//for(i=0;i<items.length;i++) {
				var p_html = $.parseHTML($(items[0]).html());

				var id = $(p_html[0]).text();
				var date = $(p_html[1]).text();
				var name = $(p_html[2]).text();
				var lname = $(p_html[3]).text();
				var phone = $(p_html[4]).text();
				var zip = $(p_html[7]).text();
				var score = $(p_html[8]).text();
				var test_num = $(p_html[9]).text();

				if(test_num > 0) {
					var prompt_change = {
						state0: {
								title: 'Edit record',
								html:'<label>First name<br><input type="hidden" name="id" value="'+ id +'"><input type="text" name="name" value="'+name+'"></label><br />'+
									'<label>Last name<br><input type="text" name="lname" value="'+ lname +'"></label><br />' +
									'<label>Phone #<br><input type="text" name="phone" value="'+phone+'" id="change_phone"></label><br />' +
									'<label>Zip<br><input type="text" name="zip" value="'+ zip +'" id="change_zip"></label><br />' +
									'<label>Score<br><input type="text" name="score" value="'+ score +'" id="change_score"></label><br />' +
									'<label>Date<br><input type="text" name="date" value="'+date+'" id="change_date"></label><br />'
									,
								buttons: { Close: 0 , Update: 1 },
								submit:function(e,v,m,f){ 
									e.preventDefault();
									if(v == 0) {
										$("#flex1").flexReload();
										$.prompt.close();
									}
									else {

										$.ajax({
											type: "POST",
											dataType: "json",
											url: "add.php",
											data: {
												name: f.name,
												lname: f.lname,
												phone: f.phone,
												zip: f.zip,
												score: f.score,
												date: f.date,
												update: 1,
												row_id: f.id
											},
											success: function(data){
										   		if(data.err == 1) {
													alert("DB error, cannot save data.");
												}
										   		else if(data.err == 2) {
													alert("Cannot update date of test.");
												}
										   		else if(data.err == 3) {
													alert("Cannot merge user. Check test date");
												}
												else {
													alert("Record updated");
													$("#flex1").flexReload();
													$.prompt.close();
												}
											}
										 });
									}
								}							
							}
						};
					$.prompt(prompt_change);
				}
				else {
					alert('You cannot change the record. Your view mode.');
					$("#flex1").flexReload();
				}

			//}
		}
		else {
			alert('Simultaneous changing is not available');
			$("#flex1").flexReload();
		}
	}
} 

function changeSmask(type) {
	if(type == 'date') {
		$("#q").mask('00.00.0000', {placeholder: "mm.dd.yyyy", clearIfNotMatch: true});
	}
	else if(type == 'phone') {
		$("#q").mask('9999999999');
	}
	else {
		$("#q").unmask();
	}
}

$("#add_phone").mask('0000000000', {clearIfNotMatch: true});
$("#add_zip").mask('00000', {clearIfNotMatch: true});
$("#add_date").mask('00/00/0000 00:00', {placeholder: "mm/dd/yyyy hh:mi", clearIfNotMatch: true});
$("#add_score").mask('00');

$("#change_phone").mask('0000000000', {clearIfNotMatch: true});
$("#change_zip").mask('00000', {clearIfNotMatch: true});
$("#change_date").mask('00/00/0000 00:00', {placeholder: "mm/dd/yyyy hh:mi", clearIfNotMatch: true});
$("#change_score").mask('00');

</script>
</head>

<body>



<h1></h1>

<table id="flex1" style="display:none"></table>

<br>

<a href="print.php" target="_blank">Print appointment list</a>

</body>
</html>
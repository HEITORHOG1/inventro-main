
var baseurl=$("#mainsiteurl").val();
var CSRF_TOKEN = $('#csrf_token').val();
"use strict";
	function delEteemployee(employee_id)
	{

		if(confirm('Deseja excluir este registro?')===false){
			return false;
		}


			var submit_url = baseurl+"hrm/employee/delete_employee";
			var dataString = "employee_id="+employee_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					$("#employeetbl").load(" #employeetbl > *");
				   

				},error: function() {

				}
			});


	}

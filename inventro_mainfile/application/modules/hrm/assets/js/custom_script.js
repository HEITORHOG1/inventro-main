
	var baseurl=$("#mainsiteurl").val();
	var CSRF_TOKEN = $('#csrf_token').val();
	"use strict";
	function addSalary()
	{
	    var save_method = 'save_department';
	    $("#dip").attr("action", baseurl+"hrm/salary/save_salary");
	    $('#salary_form').modal('show'); 
	    $('.modal-title').text('Add new salary'); 
	}

	function editSalary(salary_id)
	{

			var submit_url = baseurl+"hrm/salary/edit_salary";
			var dataString = "salary_id="+salary_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					var data = JSON.parse(res);

					$("#salary_id").val(data.salary_id);
					$("#salary_amount").val(data.salary_amount);
					$("#employee_id").val(data.employee_id);

				    $("#dip").attr("action", baseurl+"hrm/salary/update_salary");
				    $('#salary_form').modal('show'); 
				    $('.modal-title').text('Edit Salary'); 
				    $('.dbtn').text('Update Salary'); 


				},error: function() {

					Toast.fire({
				        type: 'error',
				        title: 'Wops! Thre have some problems please try again'
				    });
					
				}
			});


	}

"use strict";
	function deleteSalary(salary_id)
	{

		if(confirm('Do you want to delete')===false){
			return false;
		}


			var submit_url = baseurl+"hrm/salary/delete_salary";
			var dataString = "salary_id="+salary_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					$("#salarytbl").load(" #salarytbl > *");

					if(res){
						Toast.fire({
					        type: 'success',
					        title: 'Delete Successfull'
					    });

					}else{
						Toast.fire({
					        type: 'error',
					        title: 'Wops! Thre have some problems please try again'
					    });
					}
				   

				},error: function() {

				}
			});


	}



"use strict";
	function salaryGenerate()
	{

		if(confirm('Do you want to payment salary')===false){
			return false;
		}

			var submit_url = baseurl+"hrm/salary/salary_generat";
			

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: {'csrf_test_name': CSRF_TOKEN},
				success: function(res) {

					var data = JSON.parse(res);

					if(data===1){
						Toast.fire({
					        type: 'success',
					        title: data.message
					    });

					}else{

						Toast.fire({
					        type: 'error',
					        title: data.message
					    });

					}

					$("#salaryGenerateList").load(" #salaryGenerateList > *");
				   

				},error: function() {

				}
			});


	}


"use strict";

	function paymentSalary(generate_id)
	{

			var submit_url = baseurl+"hrm/salary/salary_paid";

			var dataString = "generat_id="+generate_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					var data = JSON.parse(res);

					if(data){

						$("#salary_amount").val(data.salary_amount);
						$("#employee_name").val(data.employee_name);
						$("#employee_id").val(data.employee_id);
						$("#generate_id").val(data.generat_id);

					    $("#dip").attr("action", baseurl+"hrm/salary/save_paid_salary");
					    $('#salary_payment').modal('show'); 
					    $('.modal-title').text('Payment Salary'); 
					    $('.dbtn').text('Payment'); 
						

					}else{

						Toast.fire({
					        type: 'error',
					        title: data.message
					    });
					}

				   

				},error: function() {

					Toast.fire({
				        type: 'error',
				        title: 'Wops! Thre have some problems please try again'
				    });
					
				}
			});

	}

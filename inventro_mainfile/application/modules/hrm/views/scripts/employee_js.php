<script type="text/javascript">
   var CSRF_TOKEN = $('#csrf_token').val();
'use strict';
	function delEteemployee(employee_id)
	{

		if(confirm('Do you want to delete')===false){
			return false;
		}


			var submit_url = "<?php echo base_url('hrm/employee/delete_employee'); ?>";
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


</script>
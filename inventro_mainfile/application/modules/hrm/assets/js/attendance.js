
var baseurl=$("#mainsiteurl").val();
"use strict";
var CSRF_TOKEN = $('#csrf_token').val();
	function addAttendance()
	{
	    $("#dip").attr("action", baseurl+"hrm/attendance/save_attendance");
	    $('#attendance_form').modal('show'); 
	    $('.modal-title').text('Add new attendance'); 
	}


    "use strict";
	function deleteAttendance(attendance_id){

		if(confirm('Do you want to delete')===false){
			return false;
		}

			var submit_url = baseurl+"hrm/attendance/delete_attendance";
			var dataString = "attendance_id="+attendance_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					if(res==='1'){
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

					$("#attendancetbl").load(location.href + " #attendancetbl");

				  
				},error: function() {

				}
			});

	}

"use strict";
	function addOutTime(attendance_id)
	{

		if(confirm('Do you want to live')===false){
			return false;
		}

			var submit_url = baseurl+"hrm/attendance/add_out_time";
			var dataString = "attendance_id="+attendance_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					if(res===1){
						
						Toast.fire({
					        type: 'success',
					        title: 'Out Time Successfull Saved'
					    })

					}else{
						Toast.fire({
					        type: 'error',
					        title: 'Wops! Thre have some problems please try again'
					    })
					}

					$("#attendancetbl").load(" #attendancetbl > *");
				  
				},error: function() {

				}
			});

	}

"use strict";
	function editAttendance(attendance_id)
	{

		if(confirm('Do you want to update this')===false){
			return false;
		}

			var submit_url = baseurl+"hrm/attendance/edit_attendance";
			var dataString = "attendance_id="+attendance_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					var data = JSON.parse(res);

					$("#in_time").val(data.in_time);
					$("#out_time").val(data.out_time);
					$("#stay_time").val(data.staytime);
					$("#attendance_id1").val(data.attandence_id);

				    $("#edit_at").attr("action", baseurl+"hrm/attendance/update_attendance");
				    $('#attendance_edit').modal('show'); 
				    $('.modal-title').text('Update Attendance'); 
				    $('.dbtn').text('Update Attendance'); 


				},error: function() {

					Toast.fire({
				        type: 'error',
				        title: 'Wops! Thre have some problems please try again'
				    })
				}
			});

	}


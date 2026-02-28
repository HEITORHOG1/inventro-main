<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends MX_Controller {

    
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();

        $this->load->model(array(
            'employee_model',
            'attendance_model'
        ));

    }



    public function index(){

        $this->permission->check_label('attendance')->read()->redirect(); 

        $data['employees'] = $this->employee_model->get_all_employee();
        $data['attendances'] = $this->attendance_model->get_all_attendance();

    	$data['title']        =  makeString(['attendance']);
        $data['module']       = "hrm";  
        $data['page']         = "__attendance";   
        echo Modules::run('template/layout', $data); 

    }


    public function save_attendance(){
        $this->permission->check_label('attendance')->create()->redirect(); 

        $attendanceData = array(
            'date'         => date('Y-m-d'),
            'in_time'         => date('h:m a'),
            'employee_id'         => $this->input->post('employee_id')
        );

        $this->db->insert('attendance_tbl',$attendanceData);
        $this->session->set_flashdata('message', makeString(['save_successfully']));
        redirect('hrm/attendance/index');

    }


    public function add_out_time(){

        $attendance_id = $this->input->post('attendance_id',TRUE);

        $row = $this->db->select('in_time')->where('attandence_id',$attendance_id)->get('attendance_tbl')->row();


           $out_time =  date("h:i:s a", time());
           $in_time = $row->in_time;
          
           $in=new DateTime($in_time);
           $Out=new DateTime($out_time);
           $interval=$in->diff($Out);
           $stay =  $interval->format('%H:%I:%S');

            $attendanceData = [
                'out_time'             =>  $out_time,
                'staytime'             => $stay
            ]; 

            $result = $this->db->where('attandence_id',$attendance_id)->update('attendance_tbl',$attendanceData);

        if($result){
            echo 1;
        }


    }



    public function update_attendance(){

        $this->permission->check_label('attendance')->update()->redirect(); 

        $attendance_id = $this->input->post('attendance_id');

        $attendanceData = array(
            'in_time'             =>  $this->input->post('in_time',TRUE),
            'out_time'            =>  $this->input->post('out_time',TRUE),
            'staytime'            =>  $this->input->post('stay_time',TRUE),
            'employee_id'         =>  $this->input->post('employee_id',TRUE)
        ); 

        $this->db->where('attandence_id',$attendance_id)->update('attendance_tbl',$attendanceData);
        
        $this->session->set_flashdata('message', makeString(['update_successfully']));
        redirect('hrm/attendance/index');

    }


    public function delete_attendance(){

        $this->permission->check_label('attendance')->delete()->redirect(); 

        $attendance_id = $this->input->post('attendance_id',TRUE);

        $result = $this->db->where('attandence_id',$attendance_id)->delete('attendance_tbl');
        
        if($result){
           echo 1;
        }else{
            echo 0;
        }

    }





    public function edit_attendance(){

        $this->permission->check_label('attendance')->update()->redirect(); 

        $attendance_id = $this->input->post('attendance_id',TRUE);

        $row = $this->db->select('*')->where('attandence_id',$attendance_id)->get('attendance_tbl')->row();

        if($row){
           echo json_encode($row);
        }else{
            echo json_encode(array('status'=>'error'));
        }


    }



    public function report(){

        $this->permission->check_label('attendance_report')->read()->redirect(); 

        $data['employees'] = $this->employee_model->get_all_employee();
        $data['attendances'] = $this->attendance_model->get_all_attendance();

        $data['title']        =  makeString(['attendance_report']);
        $data['module']       = "hrm";  
        $data['page']         = "__attendance_report";   
        echo Modules::run('template/layout', $data);         
    }



}
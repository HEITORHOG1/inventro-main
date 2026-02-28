<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salary extends MX_Controller {

    
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();

        $this->load->model(array(
            'employee_model'
        ));

    }



    public function paid_recept($generat_id){

        $data['salary'] = $this->db->select("
            salary_generat_tbl.*,

            CONCAT_WS(' ', em_first_name,em_last_name) as employee_name,
            CONCAT_WS(' ', user.firstname,user.lastname) as fullname,
            salary_generat_tbl.salary_amount,
            salary_payment_history.paid_amount,
            salary_payment_history.payment_note,
            salary_payment_history.payment_date,
            department_tbl.department_name,
            designation_tbl.designation_name
        ")
        ->join('employee_tbl','employee_tbl.employee_id=salary_generat_tbl.employee_id')
        ->join('user','user.id=salary_generat_tbl.generate_by')
        ->join('salary_payment_history','salary_payment_history.generate_id=salary_generat_tbl.generat_id')
        ->join('department_tbl','department_tbl.department_id=employee_tbl.em_department','left')
        ->join('designation_tbl','designation_tbl.designation_id=employee_tbl.em_designation','left')
        ->where('generat_id',$generat_id)
        ->get('salary_generat_tbl')->row();

        $data['setting'] = $this->db->select("*")->from('setting')->get()->row();


        $data['title']        = "Payment recept";
        $data['module']       = "hrm";  
        $data['page']         = "__salary_payment_recept";   
        echo Modules::run('template/layout', $data); 

    }



    public function save_paid_salary(){

        $generate_id = $this->input->post('generate_id',TRUE);
        $employee_id = $this->input->post('employee_id',TRUE);
        $payment_note = $this->input->post('payment_note',TRUE);
        $paid_salary_amount = $this->input->post('paid_salary_amount',TRUE);
        $salary_amount = $this->input->post('salary_amount',TRUE);
        $employee_name = $this->input->post('employee_name',TRUE);

        $paidData = array(
            'generate_id'   => $this->input->post('generate_id',TRUE),
            'employee_id'   => $this->input->post('employee_id',TRUE),
            'paid_amount'   => $this->input->post('paid_salary_amount',TRUE),
            'payment_note'  => $this->input->post('payment_note',TRUE),
            'payment_date'  => date('Y-m-d')
        );

        if($this->db->insert('salary_payment_history',$paidData)){

            $update = array('status'=>1);

            $this->db->where('generat_id',$generate_id)->update('salary_generat_tbl',$update);


            $transaction_id = "T" . date('d') . $this->generators->generator(15);

            $employee_debit = array(
                'transaction_id' => $transaction_id,
                'ledger_id'      => 'emp_'.$employee_id,
                'invoice_no'     => NULL,
                'receipt_no'     => NULL,
                'amount'         => $paid_salary_amount,
                'description'    => "Salary paid to employee",
                'payment_type'   => "AN",
                'date'           => date("d-m-Y"),
                'created_by'     => $this->session->userdata('id'),
                'is_capital'     => NULL,
                'd_c'            =>  'd',
            );
            $this->db->insert('ledger_tbl', $employee_debit);


            $employee_cradit = array(
                'transaction_id' => $transaction_id,
                'ledger_id'      => 'emp_'.$employee_id,
                'invoice_no'     => NULL,
                'receipt_no'     => NULL,
                'amount'         => $salary_amount,
                'description'    => "Salary paid to employee",
                'payment_type'   => "AN",
                'date'           => date("d-m-Y"),
                'created_by'     => $this->session->userdata('id'),
                'is_capital'     => NULL,
                'd_c'            =>  'c'
            );
            $this->db->insert('ledger_tbl', $employee_cradit);


        }

       

        $this->session->set_flashdata('message', makeString(['save_successfully']));
        redirect('hrm/salary/salary_generat_list');

    }




    public function salary_paid(){

        $generat_id = $this->input->post('generat_id',TRUE);

        $salary = $this->db->select("
            salary_generat_tbl.*,
            CONCAT_WS(' ', em_first_name,em_last_name) as employee_name,
            CONCAT_WS(' ', user.firstname,user.lastname) as fullname
        ")
        ->join('employee_tbl','employee_tbl.employee_id=salary_generat_tbl.employee_id')
        ->join('user','user.id=salary_generat_tbl.generate_by')
        ->where('generat_id',$generat_id)
        ->get('salary_generat_tbl')->row();

        echo json_encode($salary);

    }



    public function salary_generat(){


        
        $salary_month = strtotime(date('Y-m'));

        $exit = $this->db->where('salary_month',$salary_month)->get('salary_generat_tbl')->num_rows();

        $array = array();

        if($exit>0){

            $array = array('status'=>0,'message'=>'You already generate salary in this month');

        }else{

            $result = $this->db->select("*")
            ->get('salary_tbl')->result();

            $generateData = array();

            foreach ($result as $key => $value) {

                $generateData[] = array(
                    'employee_id'   =>$value->employee_id,
                    'salary_amount' =>$value->salary_amount,
                    'salary_month'  =>$salary_month,
                    'generate_date' =>date('Y-m-d'),
                    'generate_by'   =>$this->session->userdata('id')
                );
            }

            $this->db->insert_batch('salary_generat_tbl',$generateData);

            $array = array('status'=>1,'message'=>'You already generate salary in this month');

        }

        echo json_encode($array); 
    
    }



    public function salary_generat_list(){

        $this->permission->check_label('salary_generat_list')->read()->redirect(); 

        $data['salary_generat'] = $this->db->select("
            salary_generat_tbl.*,
            CONCAT_WS(' ', em_first_name,em_last_name) as employee_name,
            CONCAT_WS(' ', user.firstname,user.lastname) as fullname
        ")
        ->join('employee_tbl','employee_tbl.employee_id=salary_generat_tbl.employee_id')
        ->join('user','user.id=salary_generat_tbl.generate_by')
        ->get('salary_generat_tbl')->result();

       

        $data['title']        =  makeString(['employee','salary_generat_list']);
        $data['module']       = "hrm";  
        $data['page']         = "__salary_generat_list";   
        echo Modules::run('template/layout', $data); 

    }


    public function salary_setup(){

       $this->permission->check_label('salary_setup')->read()->redirect(); 

        $data['salaryes'] = $this->db->select("salary_tbl.*,CONCAT_WS(' ', em_first_name,em_last_name) as employee_name")
        ->join('employee_tbl','employee_tbl.employee_id=salary_tbl.employee_id')
        ->get('salary_tbl')->result();
        $data['employees'] = $this->employee_model->get_all_employee();

        $data['title']        =  makeString(['employee','salary_setup']);
        $data['module']       = "hrm";  
        $data['page']         = "__add_salary";   
        echo Modules::run('template/layout', $data); 

    }


    public function save_salary(){
        
        $this->permission->check_label('salary_setup')->create()->redirect(); 

        $salaryData = array(
            'salary_amount'=>$this->input->post('salary_amount',TRUE),
            'employee_id'=>$this->input->post('employee_id',TRUE)
        );

        $this->db->insert('salary_tbl',$salaryData);

        $this->session->set_flashdata('message', makeString(['save_successfully']));
        redirect('hrm/salary/salary_setup');
    }


    public function update_salary(){

        $this->permission->check_label('salary_setup')->update()->redirect();

        $salary_id = $this->input->post('salary_id',TRUE);

        $salaryData = array(
            'salary_amount'=>$this->input->post('salary_amount',TRUE),
            'employee_id'=>$this->input->post('employee_id',TRUE)
        );

        $this->db->where('salary_id',$salary_id)->update('salary_tbl',$salaryData);

        $this->session->set_flashdata('message', makeString(['update_successfully']));
        redirect('hrm/salary/salary_setup');
    
    }



    public function edit_salary(){
        $this->permission->check_label('salary_setup')->update()->redirect();

        $salary_id = $this->input->post('salary_id',TRUE);
        $data = $this->db->where('salary_id',$salary_id)->get('salary_tbl')->row();

        echo json_encode($data);

    }




    public function delete_salary(){
        $this->permission->check_label('salary_setup')->delete()->redirect();
        $salary_id = $this->input->post('salary_id',TRUE);
        $this->db->where('salary_id',$salary_id)->delete('salary_tbl');
        echo json_encode(array('id'=>1));
    }





}
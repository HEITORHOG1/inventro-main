<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_info extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'customer_model'
        ));
    }

    public function index($id = null) {
        $this->permission->method('customer', 'read')->redirect();
        $data['title'] = makeString(['bed_list']);
        #-------------------------------#       
        ##pagination starts#
        $config["base_url"] = base_url('customer/customer_info/index');
        $config["total_rows"] = $this->customer_model->countlist();
        $config["per_page"] = 25;
        $config["uri_segment"] = 4;
        /* This Application Must Be Used With BootStrap 4 * */
        $config['full_tag_open'] = '<ul class="pagination pagination-md">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['first_tag_open'] = '<li class="page-item disabled">';
        $config['first_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item"><a class="page-link active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = '<i class="ti-angle-right"></i>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tagl_close'] = '</a></li>';
        $config['prev_link'] = '<i class="ti-angle-left"></i>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tagl_close'] = '</li>';
        $config['last_link'] = false;
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tagl_close'] = '</a></li>';
        $config['attributes'] = array('class' => 'page-link');
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["customer_infolist"] = $this->customer_model->read($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();

        if (!empty($id)) {
            $data['title'] = makeString(['bed_edit']);
            $data['intinfo'] = $this->customer_model->findById($id);
        }
        #pagination ends
        #   
        $data['module'] = "customer";
        $data['page'] = "customerlist";
        echo Modules::run('template/layout', $data);
    }

    function _alpha_dash_space($str_in = '', $fields = '') {
        if (!preg_match("/^([-a-z0-9_. ])+$/i", $str_in)) {
            $this->form_validation->set_message('_alpha_dash_space', 'The ' . $fields . ' field may only contain alpha-numeric characters,Space,underscores, and dashes.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function create($id = null) {
        $this->load->library(array('my_form_validation'));
        $data['title'] = makeString(['add_new']);
        #-------------------------------#
        $this->form_validation->set_rules('customer_name', makeString(['customer_name']), 'required|xss_clean');
        $this->form_validation->set_rules('mobile', makeString(['mobile']), 'required|xss_clean|is_natural');
        $this->form_validation->set_rules('email', makeString(['email']), 'xss_clean|valid_email');
        $this->form_validation->set_rules('address', makeString(['address']), 'xss_clean');
        $saveid = $this->session->userdata('id');

        $data['intinfo'] = "";
        if ($this->form_validation->run($this)) {
            if (empty($this->input->post('id'))) {
                $lastid = $this->db->select("*")->from('customer_tbl')->order_by('id', 'desc')->get()->row();
                if (empty($lastid)) {
                    $sl = 0;
                } else {
                    $sl = $lastid->id;
                }

                $nextno = $sl + 1;
                $si_length = strlen((int) $nextno);

                $str = '000';
                $cutstr = substr($str, $si_length);
                $sino = "Cus_" . $cutstr . $nextno;

                $this->permission->method('customer', 'create')->redirect();
                $postData = array(
                    'id' => $this->input->post('id'),
                    'customerid' => $sino,
                    'name' => $this->input->post('customer_name', TRUE),
                    'mobile' => $this->input->post('mobile', TRUE),
                    'email' => $this->input->post('email', TRUE),
                    'address' => $this->input->post('address', TRUE),
                    'cpf' => $this->input->post('cpf', TRUE),
                    'cnpj' => $this->input->post('cnpj', TRUE),
                    'cep' => $this->input->post('cep', TRUE),
                    'cidade' => $this->input->post('cidade', TRUE),
                    'estado' => $this->input->post('estado', TRUE),
                    'tipo_pessoa' => $this->input->post('tipo_pessoa', TRUE),
                    'created_by' => $this->session->userdata('fullname'),
                    'created_date' => date('Y-m-d H:i:s'),
                    'status' => $this->input->post('status', TRUE)
                );
                $this->db->insert('customer_tbl', $postData);
                $customerid = $this->db->insert_id();

                $previous_balance = $this->input->post('previous_balance',TRUE);
                $transaction_id = "T" . date('d') . $this->generators->generator(15);
                if ($previous_balance == '' || $previous_balance) {
                    $is_capital = 1;
                } else {
                    $is_capital = 0;
                }
//        ============ its for supplier ledger data =========
                $supplier_ledger_data = array(
                    'transaction_id' => $transaction_id,
                    'ledger_id' => $sino,
                    'invoice_no' => "Adjustment",
                    'receipt_no' => NULL,
                    'amount' => $previous_balance,
                    'description' => "Previous adjustment with software",
                    'payment_type' => "NA",
                    'd_c' => $this->input->post('paytype',TRUE),
                    'date' => date("Y-m-d"),
                    'created_by' => $saveid = $this->session->userdata('id'),
                    'is_capital' => $is_capital,
                );
                if(!empty($previous_balance) && $previous_balance > 0){

                $this->db->insert('ledger_tbl', $supplier_ledger_data);
            }

                $this->session->set_flashdata('message', makeString(['save_successfully']));
                redirect('customer/customer_info/index');
            } else {
                $this->permission->method('customer', 'update')->redirect();

                $data['customer'] = (Object) $postData3 = array(
                    'id' => $this->input->post('id'),
                    'name' => $this->input->post('customer_name', TRUE),
                    'mobile' => $this->input->post('mobile', TRUE),
                    'email' => $this->input->post('email', TRUE),
                    'address' => $this->input->post('address', TRUE),
                    'created_by' => $this->session->userdata('fullname'),
                    'created_date' => date('Y-m-d H:i:s'),
                    'status' => $this->input->post('status', TRUE)
                );
                if ($this->customer_model->update($postData3)) {
                    $previous_balance = $this->input->post('previous_balance',TRUE);
                    if (!empty($this->input->post('lid'))) {
                        $transaction_id = $this->input->post('trsid', TRUE);
                    } else {
                        $transaction_id = "T" . date('d') . $this->generators->generator(15);
                    }
                    if ($previous_balance == '' || $previous_balance) {
                        $is_capital = 1;
                    } else {
                        $is_capital = 0;
                    }
//        ============ its for supplier ledger data =========
                    $supplier_ledger_data = array(
                        'id' => $this->input->post('lid', TRUE),
                        'transaction_id' => $transaction_id,
                        'ledger_id' => $this->input->post('customer_id', TRUE),
                        'invoice_no' => "Adjustment",
                        'receipt_no' => NULL,
                        'amount' => $previous_balance,
                        'description' => "Previous adjustment with software",
                        'payment_type' => "NA",
                        'd_c' => $this->input->post('paytype'),
                        'date' => date("Y-m-d"),
                        'created_by' => $saveid = $this->session->userdata('id'),
                        'is_capital' => $is_capital,
                    );
                    if (empty($this->input->post('lid'))) {
                        $this->db->insert('ledger_tbl', $supplier_ledger_data);
                    }
                    $this->customer_model->update_trns($supplier_ledger_data);
                    $this->session->set_flashdata('message', makeString(['update_successfully']));
                } else {
                    $this->session->set_flashdata('exception', makeString(['please_try_again']));
                }
                redirect("customer/customer_info/index");
            }
        } else {
            if (!empty($id)) {
                $data['title'] = makeString(['customer_update']);
                $data['intinfo'] = $this->customer_model->findById($id);
            }

            $data['module'] = "customer";
            $data['page'] = "customerlist";
            echo Modules::run('template/layout', $data);
        }
    }

    public function updateintfrm($id) {
        $this->permission->method('customer', 'update')->redirect();
        $data['title'] = makeString(['customer_edit']);
        $data['intinfo'] = $this->customer_model->findById($id);
        $data['module'] = "customer";
        $data['page'] = "customeredit";
        $this->load->view('customer/customeredit', $data);
    }

    public function delete($id = null) {
        $this->permission->module('customer', 'delete')->redirect();
        if ($this->customer_model->delete($id)) {
            #set success message
            $this->session->set_flashdata('message', makeString(['delete_successfully']));
        } else {
            #set exception message
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
        }
        redirect('customer/customer_info/index');
    }

    public function customerledger() {
        $data['title'] = makeString(['customer_ledger']);
        ;
        $data['module'] = "customer";
        $data['page'] = "customer_ledger";
        echo Modules::run('template/layout', $data);
    }

    public function ledgertotal() {
        $list = $this->customer_model->get_cusledger();
        
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $rowdata) {
            $no++;
            $row = array();

            $credit = 0;
            $debit = 0;
            $balance = 0;

            $totalc = $this->db->select("ledger_tbl.*,SUM(ledger_tbl.amount) as credit")->from('ledger_tbl')->where('ledger_id', $rowdata->customerid)->where('d_c', 'c')->get()->row();
            $credit = $totalc->credit;

            $totald = $this->db->select("ledger_tbl.*,SUM(ledger_tbl.amount) as debit")->from('ledger_tbl')->where('ledger_id', $rowdata->customerid)->where('d_c', 'd')->get()->row();
            $debit = $totald->debit;
            $balance = $credit - $debit;
            $name = '<a href="' . base_url() . 'customer/customer_info/singleledgerbycustomer/' . $rowdata->customerid . '">' . $rowdata->name . '</a>';

            $row[] = $no;
            $row[] = $name;
            $row[] = $credit;
            $row[] = $debit;
            $row[] = $balance;


            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->customer_model->count_allcusledger(),
            "recordsFiltered" => $this->customer_model->count_filtercusledger(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function singleledgerbycustomer($suplierid) {
        $data['title'] = makeString(['supplier_ledger']);
        $data['storeinfo'] = $this->customer_model->companyinfo($suplierid);
        $data['ledgerinfo'] = $this->customer_model->ledgerdetails($suplierid);
        $data['customer'] = $this->customer_model->customerinfo($suplierid);
        $data['module'] = "customer";
        $data['page'] = "ledgerdetails";
        echo Modules::run('template/layout', $data);
    }

//    ============= its for customer_csv_upload ===========
    public function customer_csv_upload() {
        $count = 0;
        $fp = fopen($_FILES['csv_file']['tmp_name'], 'r') or die("can't open file");
        if (($handle = fopen($_FILES['csv_file']['tmp_name'], 'r')) !== FALSE) {
            while ($csv_line = fgetcsv($fp, 1024)) {
//                =========its for customer id generate  ==========
                $lastid = $this->db->select("*")->from('customer_tbl')->order_by('id', 'desc')->get()->row();
                if (empty($lastid)) {
                    $sl = "1";
                } else {
                    $sl = $lastid->id;
                }

                $nextno = $sl + 1;
                $si_length = strlen((int) $nextno);

                $str = '000';
                $cutstr = substr($str, $si_length);
                $sino = "Cus_" . $cutstr . $nextno;

                //keep this if condition if you want to remove the first row
                for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                    $insert_csv = array();
                    $insert_csv['name'] = (!empty($csv_line[0]) ? $csv_line[0] : null);
                    $insert_csv['mobile'] = (!empty($csv_line[1]) ? $csv_line[1] : null);
                    $insert_csv['email'] = (!empty($csv_line[2]) ? $csv_line[2] : null);
                    $insert_csv['address'] = (!empty($csv_line[3]) ? $csv_line[3] : null);
                    $insert_csv['paytype'] = (!empty($csv_line[4]) ? $csv_line[4] : null);
                    $insert_csv['previous_balance'] = (!empty($csv_line[5]) ? $csv_line[5] : null);
                    $insert_csv['status'] = (!empty($csv_line[6]) ? $csv_line[6] : null);
                }


                $data = array(
                    'customerid' => $sino,
                    'name' => $insert_csv['name'],
                    'mobile' => $insert_csv['mobile'],
                    'email' => $insert_csv['email'],
                    'address' => $insert_csv['address'],
                    'created_by' => $this->session->userdata('id'), 
                    'created_date' => date('Y-m-d'),
                    'status' => $insert_csv['status'], 
                );
                if ($count > 0) {
                    $result = $this->db->select('*')
                                    ->from('customer_tbl')
                                    ->where('email', $data['email'])
                                    ->get()->num_rows();

                    if ($result == 0 && !empty($data['name'])) {
                        $this->db->insert('customer_tbl', $data);

                        $previous_balance = $insert_csv['previous_balance'];
                        $transaction_id = "T" . date('d') . $this->generators->generator(15);
                        if ($previous_balance == '' || $previous_balance) {
                            $is_capital = 1;
                        } else {
                            $is_capital = 0;
                        }
                        if($previous_balance){
                            $previous_balance = $previous_balance;
                        }else{
                            $previous_balance = 0;
                        }
                        if ($insert_csv['paytype'] == 'Received Amount') {
                            $paytype = 'c';
                        } elseif ($insert_csv['paytype'] == 'Payment Amount') {
                            $paytype = 'd';
                        }
//        ============ its for supplier ledger data =========
                        $customer_ledger_data = array(
                            'transaction_id' => $transaction_id,
                            'ledger_id' => $sino,
                            'invoice_no' => "Adjustment",
                            'receipt_no' => NULL,
                            'amount' => $previous_balance,
                            'description' => "Previous adjustment with software",
                            'payment_type' => "NA",
                            'd_c' => $paytype,
                            'date' => date("Y-m-d"),
                            'created_by' => $saveid = $this->session->userdata('id'),
                            'is_capital' => $is_capital,
                        );
                        $this->db->insert('ledger_tbl', $customer_ledger_data);
                    } else {
                        $data = array(
                            'name' => $insert_csv['name'],
                            'mobile' => $insert_csv['mobile'],
                            'email' => $insert_csv['email'],
                            'address' => $insert_csv['address'],
                            'created_by' => $this->session->userdata('id'), 
                            'created_date' => date('Y-m-d'), 
                            'status' => $insert_csv['status'], 
                        );
                        $this->db->where('email', $data['email']);
                        $this->db->update('customer_tbl', $data);

                    }
                }
                $count++;
            }
        }
        fclose($fp) or die("can't close file");
        $this->session->set_flashdata('message', makeString(['imported_successfully']));
        redirect(base_url('customer/customer_info/index'));
    }

}

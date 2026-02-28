<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Supplierlist extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'supplier_model'
        ));
    }

    public function index($id = null) {

        $this->permission->method('supplier', 'read')->redirect();
        $data['title'] = makeString(['supplier_list']);
    
        #pagination starts#
        $config["base_url"] = base_url('supplier/supplierlist/index');
        $config["total_rows"] = $this->supplier_model->countlist();
        $config["per_page"] = 25;
        $config["uri_segment"] = 4;
        $config["last_link"] = "Last";
        $config["first_link"] = "First";
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["supplierlist"] = $this->supplier_model->read($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();

        if (!empty($id)) {
            $data['title'] = makeString(['supplier_edit']);
            $data['intinfo'] = $this->supplier_model->findById($id);
        }
        #
        #pagination ends
        #   
        $data['module'] = "supplier";
        $data['page'] = "supplierlist";
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
        $data['title'] = makeString(['supplier_add']);

        $lastid = $this->db->select("*")->from('supplier_tbl')->order_by('id', 'desc')->get()->row();
        if (empty($lastid)) {
            $sl = 0;
        } else {
            $sl = $lastid->id;
        }

        $nextno = $sl + 1;
        $si_length = strlen((int) $nextno);

        $str = '000';
        $cutstr = substr($str, $si_length);
        $sino = "supp_" . $cutstr . $nextno;
        if (!empty($this->input->post('id', TRUE))) {
            $sino = $this->input->post('supplier_id', TRUE);
        }
        
        $this->form_validation->set_rules('suppliername', makeString(['supplier_name']), 'required|max_length[50]|xss_clean');
        $this->form_validation->set_rules('mobile', makeString(['mobile']), 'required|xss_clean|is_natural');
        $this->form_validation->set_rules('email', makeString(['email']), 'required|xss_clean|valid_email');
        $this->form_validation->set_rules('address', makeString(['address']), 'xss_clean');
        $saveid = $this->session->userdata('id');

        $c_name = $this->input->post('suppliername');
        $c_acc = $sino . '-' . $c_name;

        $data['supplier'] = (Object) $postData = array(
            'id' => $this->input->post('id', TRUE),
            'supplier_id' => $sino,
            'name' => $this->input->post('suppliername', TRUE),
            'mobile' => $this->input->post('mobile', TRUE),
            'email' => $this->input->post('email', TRUE),
            'address' => $this->input->post('address', TRUE),
            'created_by' => $this->session->userdata('fullname'),
            'created_date' => date('Y-m-d H:i:s'),
            'status' => $this->input->post('status', TRUE)
        );
        $data['intinfo'] = "";
        if ($this->form_validation->run()) {
            if (empty($this->input->post('id'))) {
                $this->permission->method('supplier', 'create')->redirect();
             
                if ($this->supplier_model->create($postData)) {

                    $previous_balance = $this->input->post('previous_balance');
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
                        'd_c' => $this->input->post('paytype'),
                        'date' => date("d-m-Y"),
                        'created_by' => $saveid = $this->session->userdata('id'),
                        'is_capital' => $is_capital,
                    );
                    if(!empty($previous_balance) && $previous_balance > 0){
                    $this->db->insert('ledger_tbl', $supplier_ledger_data);
                }

                    $this->session->set_flashdata('message', makeString(['save_successfully']));
                    redirect('supplier/supplierlist/index');
                } else {
                    $this->session->set_flashdata('exception', makeString(['please_try_again']));
                }
                redirect("supplier/supplierlist/index");
            } else {
                $this->permission->method('supplier', 'update')->redirect();
                if ($this->supplier_model->update($postData)) {
                    $previous_balance = $this->input->post('previous_balance');
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
                        'ledger_id' => $this->input->post('supplier_id', TRUE),
                        'invoice_no' => "Adjustment",
                        'receipt_no' => NULL,
                        'amount' => $previous_balance,
                        'description' => "Previous adjustment with software",
                        'payment_type' => "NA",
                        'd_c' => $this->input->post('paytype'),
                        'date' => date("d-m-Y"),
                        'created_by' => $saveid = $this->session->userdata('id'),
                        'is_capital' => $is_capital,
                    );
                    if (empty($this->input->post('lid'))) {
                        $this->db->insert('ledger_tbl', $supplier_ledger_data);
                    }
                    $this->supplier_model->update_trns($supplier_ledger_data);
                    $this->session->set_flashdata('message', makeString(['update_successfully']));
                } else {
                    $this->session->set_flashdata('exception', makeString(['please_try_again']));
                }
                redirect("supplier/supplierlist/index");
            }
        } else {
            if (!empty($id)) {
                $data['title'] = makeString(['supplier_edit']);
                $data['intinfo'] = $this->supplier_model->findById($id);
            }

            $data['module'] = "supplier";
            $data['page'] = "supplierlist";
            echo Modules::run('template/layout', $data);
        }
    }

    public function updateintfrm($id) {
        $this->permission->method('supplier', 'update')->redirect();
        $data['title'] = makeString(['supplier_edit']);
        $data['intinfo'] = $this->supplier_model->findById($id);
        $data['module'] = "supplier";
        $data['page'] = "supplieredit";
        $this->load->view('supplier/supplieredit', $data);
    }

    public function delete($id = null) {
        $this->permission->module('supplier', 'delete')->redirect();
        if ($this->supplier_model->delete($id)) {
            #set success message
            $this->session->set_flashdata('message', makeString(['delete_successfully']));
        } else {
            #set exception message
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
        }
        redirect('supplier/supplierlist/index');
    }

    public function supplierledger() {

        $data['title'] = makeString(['supplier_ledger']);
        ;
        $data['module'] = "supplier";
        $data['page'] = "suplier_ledger";
        echo Modules::run('template/layout', $data);
    }

    public function ledgertotal() {
        $list = $this->supplier_model->get_supledger();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $rowdata) {
            $no++;
            $row = array();

            $credit = 0;
            $debit = 0;
            $balance = 0;

            $totalc = $this->db->select("ledger_tbl.*,SUM(ledger_tbl.amount) as credit")->from('ledger_tbl')->where('ledger_id', $rowdata->supplier_id)->where('d_c', 'c')->get()->row();
            $credit = $totalc->credit;

            $totald = $this->db->select("ledger_tbl.*,SUM(ledger_tbl.amount) as debit")->from('ledger_tbl')->where('ledger_id', $rowdata->supplier_id)->where('d_c', 'd')->get()->row();
            $debit = $totald->debit;
            $balance = $credit - $debit;
            $name = '<a href="' . base_url() . 'supplier/Supplierlist/singleledgerbysupplier/' . $rowdata->supplier_id . '">' . $rowdata->name . '</a>';

            $row[] = $no;
            $row[] = $name;
            $row[] = $credit;
            $row[] = $debit;
            $row[] = $balance;


            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->supplier_model->count_allsupledger(),
            "recordsFiltered" => $this->supplier_model->count_filtersupledger(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function singleledgerbysupplier($suplierid) {
        $data['title'] = makeString(['supplier_ledger']);
        $data['storeinfo'] = $this->supplier_model->companyinfo($suplierid);
        $data['ledgerinfo'] = $this->supplier_model->ledgerdetails($suplierid);
        $data['supplier'] = $this->supplier_model->supplierinfo($suplierid);
        $data['module'] = "supplier";
        $data['page'] = "ledgerdetails";
        echo Modules::run('template/layout', $data);
    }
//============= its for supplier_csv_upload ===========
    public function supplier_csv_upload() {
        $count = 0;
        $fp = fopen($_FILES['csv_file']['tmp_name'], 'r') or die("can't open file");
        if (($handle = fopen($_FILES['csv_file']['tmp_name'], 'r')) !== FALSE) {
            while ($csv_line = fgetcsv($fp, 1024)) {
//                =========its for customer id generate  ==========
                $lastid = $this->db->select("*")->from('supplier_tbl')->order_by('id', 'desc')->get()->row();
                if (empty($lastid)) {
                    $sl = "1";
                } else {
                    $sl = $lastid->id;
                }

                $nextno = $sl + 1;
                $si_length = strlen((int) $nextno);

                $str = '000';
                $cutstr = substr($str, $si_length);
                $sino = "supp_" . $cutstr . $nextno;

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
                    'supplier_id' => $sino,
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
                                    ->from('supplier_tbl')
                                    ->where('email', $data['email'])
                                    ->get()->num_rows();
//               
                    if ($result == 0 && !empty($data['name'])) {
                        $this->db->insert('supplier_tbl', $data);

                        $previous_balance = $insert_csv['previous_balance'];
                        $transaction_id = "T" . date('d') . $this->generators->generator(15);
                        if ($previous_balance == '' || $previous_balance) {
                            $is_capital = 1;
                        } else {
                            $is_capital = 0;
                        }
                        if ($previous_balance) {
                            $previous_balance = $previous_balance;
                        } else {
                            $previous_balance = 0;
                        }
                        if ($insert_csv['paytype'] == 'Received Amount') {
                            $paytype = 'c';
                        } elseif ($insert_csv['paytype'] == 'Payment Amount') {
                            $paytype = 'd';
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
                            'd_c' => $paytype,
                            'date' => date("Y-m-d"),
                            'created_by' => $saveid = $this->session->userdata('id'),
                            'is_capital' => $is_capital,
                        );
                        $this->db->insert('ledger_tbl', $supplier_ledger_data);
                    } else {
                        $data = array(
//                            
                            'name' => $insert_csv['name'],
                            'mobile' => $insert_csv['mobile'],
                            'email' => $insert_csv['email'],
                            'address' => $insert_csv['address'],
                            'created_by' => $this->session->userdata('id'), 
                            'created_date' => date('Y-m-d'), 
                            'status' => $insert_csv['status'],
                        );
                        $this->db->where('email', $data['email']);
                        $this->db->update('supplier_tbl', $data);
                    }
                }
                $count++;
            }
        }

        fclose($fp) or die("can't close file");
        $this->session->set_flashdata('message', makeString(['imported_successfully']));
        redirect(base_url('supplier/supplierlist/index'));
    }

}

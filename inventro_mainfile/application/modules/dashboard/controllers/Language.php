<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language extends MX_Controller {

    private $table  = "language";
    private $phrase = "phrase";

    public function __construct()
    {
        parent::__construct();  
        $this->load->database();
        $this->load->dbforge(); 
        $this->load->helper('language');
        
        if (!$this->session->userdata('isAdmin')) 
            redirect('login');
        
    } 

    public function index()
    {
        $data['title']     = html_escape("Lista de Idiomas");
        $data['module']    = "dashboard";
        $data['page']      = "language/_main";
        $data['languages'] = $this->languages();

        echo modules::run('template/layout', $data);
    }

    public function phrase()
    {
        $this->load->library('pagination');
     
        $data['title']     = html_escape("Lista de Frases");
        $data['module']    = "dashboard";
        $data['page']      = "language/_phrase"; 
        #
        #pagination starts
        #

        $config["base_url"]       = base_url('dashboard/language/phrase/'); 
        $config["total_rows"]     = $this->db->count_all('language'); 
        $config["per_page"]       = 32;
        $config["uri_segment"]    = 4; 
        $config["num_links"]      = 5;  
        /* This Application Must Be Used With BootStrap 3 * */


        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';



        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["phrases"] = $this->phrases($config["per_page"], $page); 
        $data["links"] = $this->pagination->create_links(); 
        #
        #pagination ends
        # 
        echo modules::run('template/layout', $data);
    }
 

    public function languages()
    { 
        if ($this->db->table_exists($this->table)) { 

                $fields = $this->db->field_data($this->table);

                $i = 1;
                foreach ($fields as $field)
                {  
                    if ($i++ > 2)
                    $result[$field->name] = ucfirst($field->name);
                }

                if (!empty($result)) return $result;
 

        } else {
            return false; 
        }
    }


    public function addLanguage()
    { 


        $language = preg_replace('/[^a-zA-Z0-9_]/', '', $this->input->post('language',TRUE));
        $language = strtolower($language);

        if (!empty($language)) {

            if (!$this->db->field_exists($language, $this->table)) {
                $this->dbforge->add_column($this->table, [
                    $language => [
                        'type' => 'TEXT'
                    ]
                ]); 
                echo '1';
            } 

        } else {
            echo '0';
        }
        
    }


    public function editPhrase($language = null)
    { 
       
        $this->load->library('pagination');
     
        $data['title']     = html_escape("Editar Frase");
        $data['module']    = "dashboard";
        $data['language'] = $language;
        $data['page']      = "language/_phrase_edit";
        #
        #pagination starts
        #
        $config["base_url"]       = base_url('dashboard/language/editPhrase/'. $language); 
        $config["total_rows"]     = $this->db->count_all('language'); 
        $config["per_page"]       = 32;
        $config["uri_segment"]    = 5; 
        $config["num_links"]      = 5;  
        /* This Application Must Be Used With BootStrap 3 * */


$config['full_tag_open'] = "<ul class='pagination'>";
    $config['full_tag_close'] = '</ul>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';



    $config['prev_link'] = '<i class="fa fa-long-arrow-left"></i>Previous Page';
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';


    $config['next_link'] = 'Next Page<i class="fa fa-long-arrow-right"></i>';
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';


        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $data["phrases"] = $this->phrases($config["per_page"], $page); 
        $data["links"] = $this->pagination->create_links(); 
        #
        #pagination ends
        #  
        echo modules::run('template/layout', $data);

    }

    public function addPhrase() {  

        $lang = $this->input->post('phrase',TRUE); 

        if (sizeof($lang) > 0) {

            if ($this->db->table_exists($this->table)) {

                if ($this->db->field_exists($this->phrase, $this->table)) {

                    foreach ($lang as $value) {

                        $value = preg_replace('/[^a-zA-Z0-9_]/', '', $value);
                        $value = strtolower($value);

                        if (!empty($value)) {
                            $num_rows = $this->db->get_where($this->table,[$this->phrase => $value])->num_rows();

                            if ($num_rows == 0) { 
                                $this->db->insert($this->table,[$this->phrase => $value]); 
                                $this->session->set_flashdata('message', 'Frase adicionada com sucesso');
                            } else {
                                $this->session->set_flashdata('exception', 'A frase já existe!');
                            }
                        }   
                    }  

                    redirect('dashboard/language');
                }  

            }
        } 

        $this->session->set_flashdata('exception', 'Por favor, tente novamente');
        redirect('dashboard/language');
    }
 





    public function phrases($offset=null, $limit=null)
    {
        if ($this->db->table_exists($this->table)) {

            if ($this->db->field_exists($this->phrase, $this->table)) {

                return $this->db->order_by($this->phrase,'asc')
                    ->limit($offset, $limit)
                    ->get($this->table)
                    ->result();

            }  

        } 

        return false;
    }






    public function addLebel() { 

        $language = $this->input->post('language', TRUE);
        $phrase   = $this->input->post('phrase', TRUE);
        $lang     = $this->input->post('lang', TRUE);
        $id     = $this->input->post('ids', TRUE);

        if ($language!=NULL) {

            if ($this->db->table_exists($this->table)) {

                if ($this->db->field_exists($language, $this->table)) {

                        $this->db->where($this->phrase, $phrase)->where('id', $id)
                            ->set($language,$lang)
                            ->update($this->table); 

                    echo 1; exit;

                }  

            }
        } 

        echo 0;
    }


}



 
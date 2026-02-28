<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language1 extends CI_Controller {

    private $table  = "language";
    private $phrase = "phrase";

    public function __construct()
    {
        parent::__construct();  
        $this->load->database();
        $this->load->dbforge(); 
        $this->load->helper('language');
    } 

    public function index()
    {
        
        $data['languages']    = $this->languages(); 
        $this->load->view('admin/_header');
        $this->load->view('admin/_top_menu');
        $this->load->view('admin/language/__language_main',$data);
        $this->load->view('admin/_footer');

    }

    public function phrase()
    {
        $data['languages']    = $this->languages();
       


        #
        #pagination starts
        #
        $config["base_url"]       = base_url('admin/language/phrase'); 
        $config["total_rows"]     = $this->db->count_all('language'); 
        $config["per_page"]       = 25;
        $config["uri_segment"]    = 4; 
        $config["num_links"]      = 5;  
        /* This Application Must Be Used With BootStrap 3 * */
        $config['full_tag_open']  = "<ul class='pagination col-xs pull-right m-0'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open']   = '<li>';
        $config['num_tag_close']  = '</li>';
        $config['cur_tag_open']   = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close']  = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open']  = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open']  = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open']  = "<li>";
        $config['last_tagl_close'] = "</li>"; 
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["phrases"] = $this->phrases($config["per_page"], $page); 
        $data["links"] = $this->pagination->create_links(); 
        #
        #pagination ends
        # 

        $this->load->view('admin/_header',$data);
        $this->load->view('admin/_top_menu');
        $this->load->view('admin/language/phrase',$data);
        $this->load->view('admin/_footer');
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

                if ($result!=NULL) return $result;
 

        } else {
            return false; 
        }
    }

    public function addLanguage()
    { 
        $language = preg_replace('/[^a-zA-Z0-9_]/', '', $this->input->post('language',true));
        $language = strtolower($language);

        if ($language!=NULL) {
            if (!$this->db->field_exists($language, $this->table)) {
                $this->dbforge->add_column($this->table, [
                    $language => [
                        'type' => 'TEXT'
                    ]
                ]); 
                
                echo 1;
            } 
        } else {
            echo 0;
        }
    }



    public function editPhrase($language = null)
    { 
        $data['language'] = $language;


        #
        #pagination starts
        #
        $config["base_url"]       = base_url('admin/language/editPhrase/'. $language); 
        $config["total_rows"]     = $this->db->count_all('language'); 
        $config["per_page"]       = 25;
        $config["uri_segment"]    = 5; 
        $config["num_links"]      = 5;  
        /* This Application Must Be Used With BootStrap 3 * */
        $config['full_tag_open']  = "<ul class='pagination col-xs pull-right m-0'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open']   = '<li>';
        $config['num_tag_close']  = '</li>';
        $config['cur_tag_open']   = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close']  = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open']  = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open']  = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open']  = "<li>";
        $config['last_tagl_close'] = "</li>"; 
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $data["phrases"] = $this->phrases($config["per_page"], $page); 
        $data["links"] = $this->pagination->create_links(); 
        #
        #pagination ends
        #  

        $this->load->view('admin/_header',$data);
        $this->load->view('admin/_top_menu');
        $this->load->view('admin/language/__phrase_edit',$data);
        $this->load->view('admin/_footer');

    }

    public function addPhrase() {  

        $lang = $this->input->post('phrase',TRUE); 

        if (sizeof($lang) > 0) {

            if ($this->db->table_exists($this->table)) {

                if ($this->db->field_exists($this->phrase, $this->table)) {

                    foreach ($lang as $value) {

                        $value = preg_replace('/[^a-zA-Z0-9_]/', '', $value);
                        $value = strtolower($value);

                        if ($value!=NULL) {
                            $num_rows = $this->db->get_where($this->table,[$this->phrase => $value])->num_rows();

                            if ($num_rows == 0) { 
                                $this->db->insert($this->table,[$this->phrase => $value]); 
                                echo 1;
                            } else {
                                echo 0;
                            }
                        }   
                    }  

                }  

            }
        } 

        echo 0;
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

        $language = $this->input->post('language', true);
        $phrase   = $this->input->post('phrase', true);
        $lang     = $this->input->post('lang', true);
        $id     = $this->input->post('id', true);

        if ($language!=NULL) {

            if ($this->db->table_exists($this->table)) {

                if ($this->db->field_exists($language, $this->table)) {

                        $this->db->where($this->phrase, $phrase)->where('id', $id)
                            ->set($language,$lang)
                            ->update($this->table); 

                    echo 1;

                }  

            }
        } 

        echo 0;
    }

    public function switch_lang($lang=NULL)
    { 
        $data = array('language' => strtolower($lang));
        $this->db->update('lg_setting',$data);

        $this->db->set('status',0)->update('set_language');
        $this->db->set('status',1)->where('lang_name',$lang)->update('set_language');

        $this->session->set_flashdata('message', $lang .' is active successfully!');
        redirect('admin/language');
    }

}



 
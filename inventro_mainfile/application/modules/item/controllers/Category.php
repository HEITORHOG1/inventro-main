<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends MX_Controller
{

  public $data = [];

  public function __construct()
  {

    parent::__construct();
    $this->permission->module()->redirect();
    $this->load->model(array(
      'category_model',
    ));
  }

  public function category_form($id = null)
  {
    $data['title'] = makeString(['add_category']);

    $category_id = $this->generator(5);
    $this->form_validation->set_rules('categoryname', makeString(['category_name']), 'required|max_length[250]');

    $data['categorys']   = (object) $postData = [
      'id'             => $this->input->post('id'),
      'category_id'    => (!empty($this->input->post('category_id')) ? $this->input->post('category_id',TRUE) : $category_id),
      'name'           => $this->input->post('categoryname',TRUE),
      'parent_id'      => $this->input->post('parent_category',TRUE),
    ];


    if ($this->form_validation->run()) {

      if (empty($postData['id'])) {
        if ($this->category_model->create($postData)) {

          $this->session->set_flashdata('message', makeString(['save_successfully']));

          redirect('item/category/category_form');
        } else {
          $this->session->set_flashdata('exception',  makeString(['please_try_again']));
        }
        redirect("item/category/category_form");
      } else {
        if ($this->category_model->update($postData)) {
          $this->session->set_flashdata('message', makeString(['update_successfully']));
        } else {
          $this->session->set_flashdata('exception',  makeString(['please_try_again']));
        }
        redirect("item/category/category_form/" . $postData['id']);
      }
    } else {
      if (!empty($id)) {
        $data['title']    = makeString(['update_category']);
        $data['categorys'] = $this->category_model->findById($id);
      }
      $data['categorylist'] = $this->category_model->category_list();
      $data['module'] = "item";
      $data['page']   = "category_form";
      echo Modules::run('template/layout', $data);
    }
  }

  public function generator($lenth)
  {
    $number = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
    for ($i = 0; $i < $lenth; $i++) {
      $rand_value = rand(0, 8);
      $rand_number = $number["$rand_value"];

      if (empty($con)) {
        $con = $rand_number;
      } else {
        $con = "$con" . "$rand_number";
      }
    }
    return $con;
  }

  public function delete_category($id = null)
  {


    if ($this->category_model->delete($id)) {
      #set success message
      $this->session->set_flashdata('message', makeString(['delete_successfully']));
    } else {
      #set exception message
      $this->session->set_flashdata('exception', makeString(['please_try_again']));
    }
    redirect("item/category/category_form");
  }

  public function editfrm($id)
  {
    $this->permission->method('category', 'update')->redirect();
    $data['title'] = makeString(['category_edit']);
    $data['categorys'] = $this->category_model->findById($id);
    $data['categorylist'] = $this->category_model->category_list();
    $data['module'] = "item";
    $data['page']   = "category_editform";
    $this->load->view('item/category_editform', $data);
  }
}

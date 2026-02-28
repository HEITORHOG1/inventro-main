<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item extends MX_Controller {

    public $data = [];
    
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();
        $this->load->model(array(
            'item_model',
        ));

    }

public function item_list(){

   $data['title']    = makeString(['item_list']);
   $data['module']   = "item";
   $data['totalitem'] = $this->db->count_all('product_tbl');
   $data['page']     = "item_list"; 
   echo Modules::run('template/layout', $data); 
}

    public function CheckProductList(){
        $postData = $this->input->post();
        $data = $this->item_model->getProductList($postData);
        echo json_encode($data);
    } 
  
public function item_form($id = null){ 
  $data['title'] = makeString(['add_item']);
 
  $this->form_validation->set_rules('itemname', makeString(['item_name'])  ,'required|max_length[250]');
  $this->form_validation->set_rules('itemunit', makeString(['unit'])  ,'required|max_length[10]');
  $this->form_validation->set_rules('itemcategory', makeString(['category'])  ,'required|max_length[10]');
   $this->form_validation->set_rules('cartoonqty', makeString(['cartoonqty'])  ,'required|max_length[10]');
    $this->form_validation->set_rules('itemprice', makeString(['price'])  ,'required|max_length[15]');
  
  $product_id = (!empty($this->input->post('product_id'))?$this->input->post('product_id'):$this->generator(8));
  $image = $this->fileupload->do_upload(
      './application/modules/item/assets/images/', 
        'picture'
    );
    // if image is uploaded then resize the image
    if ($image !== false && $image != null) {
      $this->fileupload->do_resize(
        $image, 
        115,
        115
      );
    }
    //if image is not uploaded
    if ($image === false) {
      $this->session->set_flashdata('exception', makeString(['invalid_image']));
    }     
  
 
 $data['item']   = (Object) $test = [
   'product_id'     => $id,
   'name'           => $this->input->post('itemname',TRUE),
   'product_code'   => $this->input->post('itemcode',TRUE),
   'unit'           => $this->input->post('itemunit',TRUE),
   'category_id'    => $this->input->post('itemcategory',TRUE),
   'model'          => $this->input->post('itemmodel',TRUE),
   'price'          => $this->input->post('itemprice',TRUE),
   'cartoon_qty'    => $this->input->post('cartoonqty',TRUE),
   'supplier_id'    => $this->input->post('supplier_id',TRUE),
   'description'    => $this->input->post('itemdetails',TRUE),
   'purchase_price'  => $this->input->post('purchase_price',TRUE),

  ];


   $data['items']   = (Object) $postData = [
   'product_id'     => $product_id,
   'name'           => $this->input->post('itemname',TRUE),
   'product_code'   => $this->input->post('itemcode',TRUE),
   'unit'           => $this->input->post('itemunit',TRUE),
   'category_id'    => $this->input->post('itemcategory',TRUE),
   'model'          => $this->input->post('itemmodel',TRUE),
   'price'          => $this->input->post('itemprice',TRUE),
   'cartoon_qty'    => $this->input->post('cartoonqty',TRUE),
   'supplier_id'    => $this->input->post('supplier_id',TRUE),
   'description'    => $this->input->post('itemdetails',TRUE),
   'purchase_price'  => $this->input->post('purchase_price',TRUE),
   
  ];

$defaultimg = 'application/modules/item/assets/images/product.jpg';
if(empty($image)){
  if($product_id){
   $img=$this->input->post('old_picture');
  }else{
   $img=$defaultimg;
  }

}else{
 $img=$image;
}
  $imgdata['imgdata']  = (Object) $imgData = [
   'from_id'        => $product_id,
   'picture'        =>$img,
   'created_by'     => $this->session->userdata('id'),
   'status'         => 1,

  ];


  if ($this->form_validation->run()) { 

   if (empty($id)) {

    if ($this->item_model->create($postData)) { 
      $this->db->insert('picture_tbl',$imgData);
    
    $this->session->set_flashdata('message', makeString(['save_successfully']));

     redirect('item/Item/item_list');
    } else {
     $this->session->set_flashdata('exception',  makeString(['please_try_again']));
    }
    redirect("item/Item/item_form"); 

   } else {
    if ($this->item_model->update($postData)) { 
         $this->db->where('from_id', $imgData["from_id"])
      ->update("picture_tbl", $imgData);
     $this->session->set_flashdata('message', makeString(['update_successfully']));
     redirect('item/Item/item_list');
    } else {
     $this->session->set_flashdata('exception',  makeString(['please_try_again']));
    }
    redirect("item/item/item_form/".$id);  
   }

  } else { 
   if(!empty($id)) {
    $data['title']    = 'Update Item';
    $data['item'] = $this->item_model->findById($id);
   }
   $data['unitlist']  = $this->item_model->unit_list();
   $data['categorylist']  = $this->item_model->category_list();
   $data['supplier_list'] = $this->item_model->supplier_list();
   $data['module'] = "item";
   $data['page']   = "item_form"; 
   echo Modules::run('template/layout', $data); 
   }  
}
 
 public function generator($lenth) {
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

        $result = $this->item_model->product_id_check($con);

        if ($result === true) {
            $this->generator(8);
        } else {
            return $con;
        }
    }

public function delete($id = null) 
  { 

    if ($this->item_model->delete($id)) {
      #set success message
      $this->session->set_flashdata('message',makeString(['delete_successfully']));
    } else {
      #set exception message
      $this->session->set_flashdata('exception',makeString(['please_try_again']));
    }
    redirect("item/Item/item_list");
  }

    /**
     * Toggle disponibilidade no cardapio (AJAX)
     */
    public function toggle_disponivel($id = null) {
        header('Content-Type: application/json');

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID obrigatorio']);
            return;
        }

        $product = $this->db->where('id', (int)$id)->get('product_tbl')->row();
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Produto nao encontrado']);
            return;
        }

        $new_value = ($product->disponivel_cardapio ?? 1) ? 0 : 1;
        $this->db->where('id', (int)$id)->update('product_tbl', ['disponivel_cardapio' => $new_value]);

        echo json_encode([
            'success' => true,
            'disponivel' => $new_value,
            'message' => $new_value ? 'Produto visivel no cardapio' : 'Produto oculto no cardapio',
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
    }
}
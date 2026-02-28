 <?php $currency = $get_appsetting->currencyname;
    $position = $get_appsetting->position;
                        ?>
 <?php foreach ($get_products as $products) {?>
 <div class="col-md-2 p-1" onclick="onselectimage(<?php echo $products->product_id;?>)">
     <div class="single-product">
         <div class="img"><img src="<?php echo base_url().$products->picture;?>" class="img-fluid"></div>
         <div class="description">
             <p class="product-title"><strong><?php echo html_escape($products->name);?></strong></p>
             <div class="d-flex sku-price">
                 <div class="col-12 pl-0 pt-0"><span><?php echo html_escape($products->model);?></span></div>
             </div>
         </div>
         <div class="price"> <?php
                                        echo (($position == 0) ? "$currency $products->price" : "$products->price $currency");
                                        ?></div>
     </div>
 </div>
 <?php }?>
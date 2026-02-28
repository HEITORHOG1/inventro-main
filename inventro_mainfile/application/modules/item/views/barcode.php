<!-- Barcode print js -->
<script type="text/javascript">
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        w = window.open();
        w.document.write(printContents);
        w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');
        w.document.close(); // necessary for IE >= 15
        w.focus(); // necessary for IE >= 15
        return true;
    }
</script>

<!-- Barcode list start -->

        <!-- Product Barcode and QR code -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">    
                               
                        </div>
                    </div>
                    <?php echo form_open_multipart('item/item/insert_product') ?>
                    <div class="panel-body">
                        <?php
                        if (!empty($product_id) || !empty($qr_image)) {
                            ?>
                            <div style="float: center">
                                <a  class="btn btn-info" href="#" onclick="printDiv('printableArea')"><?php echo 'Print'; ?></a>
                                <a  class="btn btn-danger" href="<?php echo base_url('item/item/item_list'); ?>"><?php echo makeString(['cancel']); ?></a>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="table-responsive" style="margin-top: 10px">
                            <?php
                            if (isset($product_id)) {
                            ?>
                                <div id="printableArea">
                                    <table  id="" class="table table-bordered " style=" border-collapse: collapse;">
                                    <?php
                                    $counter = 0;
                                    for ($i=0; $i < 18 ; $i++) { 
                                    ?>
                                    <?php if($counter == 3) { ?>
                                    <tr> 
                                    <?php $counter = 0; ?>
                                    <?php } ?>
                                        <td style="border: 1px solid black ;padding: 5px">      
                                            
                                            <div class="barcode-inner" style="font-family: 'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;text-align: center; position: relative;">
                                                <div class="product-name" style="text-transform: uppercase;line-height: 10px;font-weight: 600;font-size: 12px;margin-bottom: 3px;">
                                                    <?php echo  html_escape($company_name);?>
                                                </div>
                                                <span class="model-name" style="font-weight: 600;
                                                    font-size: 8px;
                                                    position: absolute;
                                                    top: 0;
                                                    right: 0;"><?php echo html_escape($product_model);?></span>
                                                <img src="<?php echo base_url('item/barcode/barcode_generator/'.$product_id)?>" class="img-responsive center-block" alt="" style="display: block;margin-left: auto;margin-right: auto;height: 42px;width: 160px;">
                                                <div class="product-name-details" style="font-size: 11px;letter-spacing: 0.5px;font-weight: 600;text-transform: uppercase;line-height: 8px;"><?php echo  html_escape($product_name);?></div>
                                                <div class="price" style="font-weight: 500;line-height: 10px;margin-top: 5px;"><?php echo (($position==0)?"$currency $price":"$price $currency") ?> <small style="font-weight: 600;font-size: 9px;"><?php echo makeString(['incl_vat'])?> 
                                                    
                                                </div>
                                            </div>
                                            
                                        </td>
                                        <?php if($counter == 3) { ?>
                                            </tr> 
                                            <?php $counter = 0; ?>
                                        <?php } ?>
                                        <?php $counter++; ?>
                                    <?php
                                    }
                                    ?>
                                    </table>
                                </div>
                            <?php
                            }else{
                            ?>
                            <div id="printableArea">
                                <table class="table table-bordered"  style=" border-collapse: collapse;">
                                <?php
                                $counter = 0;
                                for ($i=0; $i < 9 ; $i++) { 
                                ?>
                                <?php if($counter == 3) { ?>
                                <tr> 
                                <?php $counter = 0; ?>
                                <?php } ?>
                                    <td style="border: 1px solid black ;padding: 5px">  
                                        <div class="barcode-inner" style="font-family: 'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;text-align: center; position: relative;">
                                            <div class="product-name" style="text-transform: uppercase;line-height: 10px;font-weight: 600;font-size: 12px;margin-bottom: 3px;">
                                                <?php echo  html_escape($company_name);?>
                                            </div>
                                            <span class="model-name" style="font-weight: 600;
                                                font-size: 8px;
                                                position: absolute;
                                                top: 0;
                                                right: 0;"><?php echo  html_escape($product_model);?></span>
                                            <img src="<?php echo base_url('application/modules/item/assets/images/qr/'.$qr_image)?>" class="img-responsive center-block" alt="" style="display: block;margin-left: auto;margin-right: auto;height:150px">
                                            <div class="product-name-details" style="font-size: 11px;letter-spacing: 0.5px;font-weight: 600;text-transform: uppercase;line-height: 8px;"><?php echo  html_escape($product_name) ;?></div>
                                            <div class="price" style="font-weight: 500;line-height: 10px;margin-top: 5px;"><?php echo (($position==0)?"$currency $price":"$price $currency") ?> <small style="font-weight: 600;font-size: 9px;"><?php echo makeString(['incl_vat'])?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <?php if($counter == 3) { ?>
                                        </tr> 
                                        <?php $counter = 0; ?>
                                    <?php } ?>
                                    <?php $counter++; ?>
                                <?php
                                }
                                ?>
                                </table>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>
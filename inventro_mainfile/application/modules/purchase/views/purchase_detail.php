<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>application/modules/purchase/assets/css/style.css">
<script src="<?php echo base_url() ?>application/modules/purchase/assets/js/purchase.js" type="text/javascript">
</script>
<div class="row">
    <div class="col-sm-12">
        <div class="card card">
            <div class="card-heading">
                <div class="card-title print_icon">
                    <h4><?php echo makeString(['purchase_details']) ?></h4>
                    <button class="btn btn-info" onclick="printDiv('printableArea')"><span
                            class="fas fa-print"></span></button>
                </div>
            </div>
            <div class="card-body" id="printableArea">
                <div class="supplier_info">
                    <span width="100%">

                        <?php echo makeString(['supplier_name']) ?> :
                        &nbsp;<span><?php echo  html_escape($supplier_name);?></span> <br />
                        <?php echo  makeString(['purchase_date'])?> :&nbsp;<?php echo  html_escape($final_date);?>
                        <br /><?php echo makeString(['chalan_no']) ?> :&nbsp; <?php echo  html_escape($chalan_no);?>
                    </span>

                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo makeString(['sl']); ?></th>
                                <th class="text-center"><?php echo makeString(['item_name']); ?></th>
                                <th class="text-center"><?php echo makeString(['qty']);?></th>
                                <th class="text-center"><?php echo makeString(['price']); ?></th>
                                <th class="text-center"><?php echo makeString(['total_amount']); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
									if ($purchase_all_data) {
								?>
                            <?php $sl=1;
							 foreach($purchase_all_data as $details){?>
                            <tr>
                                <td><?php echo  $sl;?></td>
                                <td>
                                    <?php echo $details['product_name'].'('.$details['product_model'].')';?>

                                </td>
                                <td class="text-right"><?php echo html_escape($details['quantity'])?></td>
                                <td class="text-right"><?php echo html_escape($details['rate'])?></td>
                                <td class="text-right total"><?php echo html_escape($details['total_amount'])?></td>
                            </tr>

                            <?php $sl++;}
									}
								?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right" colspan="4"><b><?php echo makeString(['discount']); ?>:</b></td>
                                <td class="text-right total"><b><?php echo html_escape($discount);?></b></td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="4"><b><?php echo makeString(['grand_total']); ?>:</b>
                                </td>
                                <td class="text-right total"><b><?php echo html_escape($sub_total_amount);?></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
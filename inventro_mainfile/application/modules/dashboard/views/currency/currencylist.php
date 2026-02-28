
<div id="add0" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <strong><?php echo makeString(['currency_add']); ?></strong>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="panel">

                            <div class="panel-body">

                                <?php echo form_open('dashboard/currency/create') ?>
                                <?php echo form_hidden('currencyid', (!empty($intinfo->currencyid) ? $intinfo->currencyid : null)) ?>
                                <div class="form-group row">
                                    <label for="currencyname" class="col-sm-4 col-form-label"><?php echo makeString(['currency_name']) ?> <span class="txt-color">*</span></label>
                                    <div class="col-sm-8">
                                        <input name="currencyname" class="form-control" type="text" placeholder="Add <?php echo makeString(['currency_name']) ?>" id="currencyname" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="icon" class="col-sm-4 col-form-label"><?php echo makeString(['currency_icon']) ?> <span class="txt-color">*</span></label>
                                    <div class="col-sm-8">
                                        <input name="icon" class="form-control" type="text" placeholder="<?php echo makeString(['currency_icon']) ?>" id="icon" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="rate" class="col-sm-4 col-form-label"><?php echo makeString(['currency_rate']) ?> <span class="txt-color">*</span></label>
                                    <div class="col-sm-8">
                                        <input name="rate" class="form-control" type="text" placeholder="<?php echo makeString(['currency_rate']) ?>" id="rate" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="position" class="col-sm-4 col-form-label"><?php echo makeString(['currency_position']) ?> <span class="txt-color">*</span></label>
                                    <div class="col-sm-8 customesl">
                                        <select name="position" class="form-control">
                                            <option value=""  selected="selected"><?php echo makeString(['select_one']); ?></option>
                                            <option value="1"><?php echo makeString(['left']); ?></option>
                                            <option value="2"><?php echo makeString(['right']); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo makeString(['reset']) ?></button>
                                    <button type="submit" class="btn btn-success w-md m-b-5"><?php echo makeString(['Ad']) ?></button>
                                </div>
                                <?php echo form_close() ?>

                            </div>  
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="modal-footer">

        </div>

    </div>

</div>

<div id="edit" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <strong><?php echo makeString(['currency_edit']); ?></strong>
                <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>
            <div class="modal-body editinfo">

            </div>

        </div>
        <div class="modal-footer">

        </div>

    </div>

</div>



<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="card card-primary card-outline">

            <div class="card-header">
                <button type="button" class="btn btn-primary btn-md float-right" data-target="#add0" data-toggle="modal"  ><i class="ti-plus" aria-hidden="true"></i>
                    <?php echo makeString(['currency_add']) ?></button>
                <h3 class="card-title"><?php echo html_escape($title); ?></h3>
            </div>

            <div class="card-body">

                <table width="100%" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo makeString(['Sl']) ?></th>
                            <th><?php echo makeString(['currency_name']) ?></th>
                            <th><?php echo makeString(['currency_icon']) ?></th>
                            <th><?php echo makeString(['currency_position']); ?></th>
                            <th><?php echo makeString(['currency_rate']) ?></th>
                            <th><?php echo makeString(['action']) ?></th> 

                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($currencylist)) { ?>
                            <?php $sl = 1; ?>
                            <?php foreach ($currencylist as $currency) { ?>
                                <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>">
                                    <td><?php echo $sl; ?></td>
                                    <td><?php echo html_escape($currency->currencyname); ?></td>
                                    <td><?php echo html_escape($currency->curr_icon); ?></td>
                                    <td><?php
                                        if ($currency->position == 1) {
                                            echo html_escape("Left");
                                        } else {
                                            echo html_escape("Right");
                                        }
                                        ?></td>
                                    <td><?php echo $currency->curr_rate; ?></td>
                                    <td class="center">
                                        <?php if ($this->permission->method('setting', 'update')->access()): ?>
                                            <input name="url" type="hidden" id="url_<?php echo $currency->currencyid; ?>" value="<?php echo base_url("dashboard/currency/updateintfrm") ?>" />
                                            <a onclick="editinfo('<?php echo $currency->currencyid; ?>')" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="fas fa-edit" aria-hidden="true"></i></a> 
                                        <?php
                                        endif;
                                        if ($this->permission->method('setting', 'delete')->access()):
                                            ?>
                                            <a href="<?php echo base_url("dashboard/currency/delete/$currency->currencyid") ?>" onclick="return confirm('<?php echo makeString(["are_you_sure"]) ?>')" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Delete "><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                <?php endif; ?>
                                    </td>

                                </tr>
        <?php $sl++; ?>
    <?php } ?> 
<?php } ?> 
                    </tbody>
                </table>  <!-- /.table-responsive -->

            </div>
        </div>
        <!-- /.card -->
    </div>
    <!--/.col (left) -->
</div>

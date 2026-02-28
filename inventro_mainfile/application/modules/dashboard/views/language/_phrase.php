
<div class="row">
    <div class="col-12">


        <div class="card card-primary card-outline">
            <div class="card-header">
                <a class="btn btn-primary btn-sm float-right" href="<?php echo base_url("dashboard/language") ?>"> <i class="fa fa-list"></i>  <?php echo html_escape('Language List');?> </a> 
                <h2 class="card-title"><?php echo html_escape($title) ?></h2>
            </div>

            <!-- /.card-header -->
            <div class="card-body">

                <?php echo form_open('dashboard/language/addPhrase', ' class="form-inline" ') ?> 
                <div class="form-group">
                    <label class="sr-only" for="addphrase"> <?php echo makeString(['phrase_name']); ?></label>
                    <input name="phrase[]" type="text" class="form-control" id="addphrase" placeholder="<?php echo makeString(['phrase_name']); ?>">
                </div>

                <button type="submit" class="btn btn-primary"><?php echo makeString(['save']); ?></button>
                <?php echo form_close(); ?>


                <table  class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th><i class="fa fa-th-list"></i> <?php echo makeString(['sl']); ?></th>
                            <th><?php echo makeString(['phrase']); ?></th> 
                        </tr>

                    </thead>

                    <tbody>


                        <?php if (!empty($phrases)) { ?>
                            <?php $sl = 1 ?>
                            <?php foreach ($phrases as $value) { ?>
                                <tr>
                                    <td><?php echo $sl++ ?></td>
                                    <td><?php echo html_escape($value->phrase) ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <?php echo html_escape(@$links); ?>
                </nav>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>





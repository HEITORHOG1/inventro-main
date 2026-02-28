<div class="row" id="lislt1">
    <div class="col-xs-12 col-sm-12 col-md-12">




        <nav aria-label="Page navigation example" class="language">
            <?php echo @$links ?>
        </nav>
    </div>

    <?php if (!empty($phrases)) { ?>
        <?php $sl = 1 ?>
        <?php foreach ($phrases as $value) { ?>
            <div class="col-md-3">
                <div class="card card-<?php echo (empty($value->$language) ? "danger" : "warning") ?>">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo html_escape($value->phrase);?></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" id="<?php echo $value->id ?>">
                        <?php echo form_open('', array('id' => 'addlebel' . $value->id)) ?>
                        <div class="form-group">
                            <?php echo form_hidden('language', $language) ?>
                            <input type="hidden" name="ids" id="id_<?php echo $value->id ?>" value="<?php echo $value->id ?>" >
                            <input type="hidden" name="phrase" value="<?php echo html_escape($value->phrase) ?>" class="form-control" readonly>
                            <input name="lang" type="text" value="<?php echo html_escape($value->$language) ?>" class="form-control" placeholder="<?php echo makeString(['add_phrase_name']); ?>">
                        </div>
                        <button type="button" onclick="SaveData('<?php echo $value->id ?>')" class="btn btn-success btn-sm rounded-0"><?php echo makeString(['save']); ?></button>
                        <?php echo form_close(); ?>
                        <input type="hidden" name="" id="languagebase_url" value="<?php echo base_url(); ?>">
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>


        <?php } ?>
    <?php } ?>

    <div class="col-xs-12 col-sm-12 col-md-12">
    <nav aria-label="Page navigation example" class="language">
            <?php echo @$links ?>
        </nav>
    </div>


</div>
<script src="<?php echo base_url() ?>application/modules/dashboard/assets/js/language_phase.js" type="text/javascript"></script>        









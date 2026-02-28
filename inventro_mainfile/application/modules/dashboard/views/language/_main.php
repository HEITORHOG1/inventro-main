<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo html_escape($title) ?></h3>
            </div>

            <div class="card-body" id="langlist">
                <div class="row">
                    <div class="col-4 col-sm-4">
                        <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-home" role="tab" aria-controls="vert-tabs-home" aria-selected="true"><?php echo html_escape('Lista de Idiomas')?></a>
                            <a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-profile" role="tab" aria-controls="vert-tabs-profile" aria-selected="false"><?php echo html_escape('Adicionar Frase')?></a>
                            <a class="nav-link" id="vert-tabs-messages-tab" data-toggle="pill" href="#vert-tabs-messages" role="tab" aria-controls="vert-tabs-messages" aria-selected="false"><?php echo html_escape('Adicionar Idioma')?></a>
                        </div>
                    </div>
                    <div class="col-8 col-sm-8">
                        <div class="tab-content" id="vert-tabs-tabContent">

                            <div class="tab-pane text-left fade show active" id="vert-tabs-home" role="tabpanel" aria-labelledby="vert-tabs-home-tab">
                                <table class="table table-striped table-bordered ">

                                    <thead>
                                        <tr>
                                            <th><i class="fa fa-th-list"></i></th>
                                            <th><?php echo makeString(['language']); ?></th>
                                            <th><i class="fa fa-cogs"></i> <?php echo makeString(['action']); ?></th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php if ($languages != NULL) { ?>
                                            <?php $sl = 1 ?>
                                            <?php
                                            foreach ($languages as $key => $language) {
                                                $l = strtolower($language);
                                                ?>
                                                <tr>
                                                    <td><?php echo $sl++ ?></td>
                                                    <td><?php echo html_escape($language) ?></td>
                                                    <td><a href="<?php echo base_url("dashboard/language/editPhrase/$key") ?>" class="btn btn-md btn-primary rounded-0"><i class="fa fa-edit"></i> <?php echo makeString(['edit_phrase']); ?></a>  
                                                </tr>
                                            <?php } ?>

                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade" id="vert-tabs-profile" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
                                <?php echo form_open('dashboard/language/addPhrase', array('id' => 'phraseForm1')) ?>

                                <div class="add_input">

                                    <div class="form-group ">
                                        <label for="country_name" class="col-form-label"><?php echo makeString(['add_new_phrase']); ?> *</label>
                                        <div class="input-group mb-3">
                                            <input name="phrase[]" type="text" class="form-control rounded-0" id="addphrase" placeholder="Adicionar nome da frase" required="">
                                            <button class="btn btn-info btn-flat" type="button" onclick="addInputFieldPhrash()" ><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div> 
                                <button type="submit" class="btn btn-success btn-md rounded-0"><?php echo makeString(['add_phrase']); ?></button>
                                <?php echo form_close(); ?>

                            </div>

                            <div class="tab-pane fade" id="vert-tabs-messages" role="tabpanel" aria-labelledby="vert-tabs-messages-tab">
                                <?php echo form_open('', array('id' => 'languageForm')) ?>
                                <div class="form-group">
                                    <label class="sr-only" for="addLanguage"> <?php echo makeString(['add_language_name']); ?></label>
                                    <input name="language" type="text" class="form-control rounded-0" id="addLanguage" placeholder="<?php echo makeString(['add_language_name']); ?>">
                                </div>
                                <button type="submit" class="btn btn-success btn-md rounded-0"><?php echo makeString(['add_language']); ?></button>
                                <?php echo form_close(); ?>
                                <input type="hidden" name="" id="languagebase_url" value="<?php echo base_url(); ?>">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>application/modules/dashboard/assets/js/language_main.js" type="text/javascript"></script> 


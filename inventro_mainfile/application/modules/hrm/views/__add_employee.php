<div class="row">
    <!-- left column -->
    <div class="col-md-10 offset-md-1">
        <!-- general form elements -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo html_escape($title); ?></h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <?php echo form_open_multipart('hrm/employee/save_employee/', 'class="form-inner"') ?>


            <div class="card-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="title"><?php echo makeString(['firstname']) ?> <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" class="form-control" id="firstname" value="<?php echo @$employee->em_first_name ?>" required>
                        </div> 
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="lastname"><?php echo makeString(['lastname']) ?> <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" class="form-control" id="lastname" value="<?php echo @$employee->em_last_name ?>" required >
                        </div>
                    </div>
                </div>



                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="email"><?php echo makeString(['email']) ?> <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" id="email" value="<?php echo @$employee->em_email ?>"  required>
                        </div>
                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">
                            <label for="password"><?php echo makeString(['password']) ?> <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" id="password" required="">
                        </div>

                    </div>
                </div>


                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">
                            <label for="department"><?php echo makeString(['department']) ?> <span class="text-danger">*</span></label>

                            <select class="form-control" name="department" required>
                                <option value=""><?php echo makeString(['select_one']); ?></option>
                                <?php foreach ($departments as $department) { ?>
                                    <option value="<?php echo $department->department_id ?>" <?php echo (@$employee->em_department = $department->department_id ? 'selected' : '') ?>><?php echo $department->department_name ?></option>
                                <?php } ?>
                            </select>

                        </div>
                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">
                            <label for="designation"><?php echo makeString(['designation']) ?> <span class="text-danger">*</span></label>
                            <select class="form-control" name="designation" required>
                                <option value=""><?php echo makeString(['select_one']); ?></option>
                                <?php foreach ($designations as $designation) { ?>
                                    <option value="<?php echo $designation->designation_id ?>" <?php echo (@$employee->em_designation = $designation->designation_id ? 'selected' : '') ?>><?php echo $designation->designation_name ?></option>
                                <?php } ?>
                            </select>

                        </div>
                    </div>


                </div>


                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">
                            <label for="country"><?php echo makeString(['country']) ?> <span class="text-danger">*</span></label>
                            <select class="form-control" name="country" required>
                                <option value="">--Select--</option>
                                <?php foreach ($countrys as $country) { ?>
                                    <option value="<?php echo $country->country_id ?>" <?php echo (@$employee->em_country = $country->country_id ? 'selected' : '') ?>><?php echo html_escape($country->country_name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">
                            <label for="city"><?php echo makeString(['city']) ?> <span class="text-danger">*</span></label>
                            <input type="text" name="city" value="<?php echo @$employee->em_city ?>" class="form-control" id="city">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">
                            <label for="zip"><?php echo makeString(['zip']) ?> <span class="text-danger">*</span></label>
                            <input type="text" name="zip" value="<?php echo @$employee->em_zip ?>" class="form-control" id="zip">
                        </div>
                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">
                            <label for="image"><?php echo makeString(['image']) ?></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="image" class="custom-file-input" id="image">
                                    <label class="custom-file-label" for="exampleInputFile"><?php echo makeString(['chose_file']) ?> </label>
                                    <input type="hidden" name="old_image" value="<?php echo @$employee->em_image ?>">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">
                            <label for="phone"><?php echo makeString(['phone']) ?> <span class="text-danger">*</span></label>
                            <input type="number" name="phone" value="<?php echo @$employee->em_phone ?>" class="form-control" id="phone" required>
                        </div>

                    </div>

                 
                </div>

                <div class="row">

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="address"><?php echo makeString(['address']) ?> </label>
                            <textarea name="address" class="form-control" maxlength="110" rows="2">
                                <?php echo @$employee->em_address ?>
                            </textarea>
                        </div>
                    </div>

                </div>

                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" name="status" <?php echo (@$employee->status == 1 ? 'checked' : '') ?> type="checkbox" id="statuscheck" value="1">
                    <label for="statuscheck" class="custom-control-label"><?php echo makeString(['status']) ?></label>
                </div>


            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><?php echo makeString(['add_employee']) ?></button>
            </div>

            <?php echo form_close() ?>
        </div>
        <!-- /.card -->


    </div>
    <!--/.col (left) -->

</div>
<div class="row">
    <div class="col-md-3">

        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="<?php echo base_url() . html_escape($user->image) ?>"
                         alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo html_escape($user->fullname) ?></h3>
                <p class="text-muted text-center"><?php echo html_escape(@$user->designation_name) ?></p>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- About Me Box -->
        <div class="card card-default">
            <div class="card-header">
                <h4><?php echo html_escape('Information')?></h4>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <strong><i class="fas fa-envelope mr-1"></i> <?php echo html_escape($user->email) ?></strong>
                <hr>
                <?php if ($user->em_phone) { ?>
                    <strong><i class="fas fa-phone mr-1"></i> <?php echo html_escape($user->em_phone) ?></strong>
                    <hr>
                <?php } ?>

                <?php if ($user->em_address) { ?>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> <?php echo html_escape($user->em_address) ?></strong>
                    <hr>
                <?php } ?>

                <strong><i class="fas fa-book mr-1"></i> <?php echo makeString(['about_me']); ?></strong>
                <p class="text-muted">
                    <?php echo html_escape($user->about); ?>
                </p>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <h4><?php echo makeString(['about_me']); ?></h4>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        <!-- Post -->
                        <div class="post">
                            <div class="user-block">
                                <img class="img-circle img-bordered-sm" src="<?php echo base_url() . html_escape($user->image) ?>" alt="user image">
                                <span class="username">
                                    <a href="#"><?php echo html_escape($user->fullname); ?></a>
                                </span>
                                <span class="description"><?php echo makeString(['last_login']); ?> - <?php echo html_escape($user->last_login) ?></span>
                            </div>
                            <!-- /.user-block -->
                            <p>
                                Lorem ipsum represents a long-held tradition for designers,
                                typographers and the like. Some people hate it and argue for
                                its demise, but others ignore the hate as they create awesome
                                tools to help create filler text for everyone from bacon lovers
                                to Charlie Sheen fans.
                            </p>

                        </div>
                        <!-- /.post -->

                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>
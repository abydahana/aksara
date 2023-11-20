<div class="container-fluid py-3">
    <form action="<?= current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-2">
                <img src="<?= get_image('users', $userdata->photo, 'thumb'); ?>" class="img-fluid rounded" alt="..." />
            </div>
            <div class="col-md-8">
                <div class="row">
                    <label class="col-4 col-md-3 text-muted">
                        <?= phrase('User ID'); ?>
                    </label>
                    <label class="col-8 col-md-9">
                        <?= $userdata->user_id; ?>
                    </label>
                </div>
                <div class="row">
                    <label class="col-4 col-md-3 text-muted">
                        <?= phrase('Username'); ?>
                    </label>
                    <label class="col-8 col-md-9">
                        <?= $userdata->username; ?>
                    </label>
                </div>
                <div class="row">
                    <label class="col-4 col-md-3 text-muted">
                        <?= phrase('Full Name'); ?>
                    </label>
                    <label class="col-8 col-md-9">
                        <?= $userdata->first_name . ' ' . $userdata->last_name; ?>
                    </label>
                </div>
                <div class="row">
                    <label class="col-4 col-md-3 text-muted">
                        <?= phrase('User Group'); ?>
                    </label>
                    <label class="col-8 col-md-9">
                        <?= $userdata->group_name; ?>
                    </label>
                </div>
                <?php if ($year): ?>
                <div class="row">
                    <label class="col-4 col-md-3 text-muted">
                        <?= phrase('Access Year'); ?>
                    </label>
                    <label class="col-8 col-md-4">
                        <?php
                            $options = null;

                            foreach ($year as $key => $val) {
                                $options .= '<option value="' . $val->year . '"' . (isset($field_data->access_year) && $field_data->access_year == $val->year ? ' selected' : null) . '>' . $val->year . '</option>';
                            }
                        ?>
                        <select name="access_year" class="form-control" placeholder="<?= phrase('Please choose'); ?>">
                            <?= $options; ?>
                        </select>
                    </label>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <hr class="row" />
        <div class="row">
            <div class="col-md-5">
                <div class="mb-3">
                    <label class="text-muted d-block" for="menus_input">
                        <?= phrase('Accessible Menus'); ?>
                    </label>
                    <?= $visible_menu; ?>
                </div>
            </div>
            <?php if ($sub_level_1): ?>
                <div class="col-md-5">
                    <div class="form-group mb-3">
                        <label class="text-muted d-block" for="kegiatan_input">
                            <?= phrase('The sub level can be accessed'); ?>
                        </label>
                        <?php
                            $option = null;
                            foreach($sub_level_1 as $key => $val) {
                                if (! isset($val->id) || ! isset($val->label)) continue;
                                
                                $option .= '<option value="' . $val->id . '"' . ($val->id == $userdata->sub_level_1 ? ' selected' : null) . '>' . $val->label . '</option>';
                            }
                        ?>
                        <select name="sub_level_1" class="form-control" id="sub_level_1_input" placeholder="<?= phrase('Please choose'); ?>">
                            <?= $option; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="opt-btn-overlap-fix"></div>
        <div class="row opt-btn">
            <div class="col-md-10">
                <a href="<?= $links->current_module; ?>" class="btn btn-link --xhr">
                    <i class="mdi mdi-arrow-left"></i>
                    &nbsp;
                    <?= phrase('Back'); ?>
                </a>
                <button type="submit" class="btn btn-primary float-end">
                    <i class="mdi mdi-check"></i>
                    &nbsp;
                    <?= phrase('Update'); ?>
                    <em class="text-sm">(ctrl+s)</em>
                </button>
            </div>
        </div>
    </form>
</div>

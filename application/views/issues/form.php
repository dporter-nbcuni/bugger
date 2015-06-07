<section class="content-header">
    <h1>
        <i class="fa fa-bug"></i> <?php echo ($issue->id ? 'Edit Ticket <br><small>' . $issue->trackingId() . '</small>' : 'Add New Ticket'); ?>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="boxbox-primary">
                <form data-parsley-validate action="<?php echo ($issue->id ? '/issues/edit/' . $issue->id : '/issues/add'); ?>" class="form-horizontal" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo Session::instance()->getToken(); ?>">
                    <input type="hidden" name="attachment_temp_dir" value="<?php echo $attachment_temp_dir; ?>">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Project<span class="required-field">*</span></label>
                            <div class="col-sm-7">
                                <select name="issue[project_id]" class="form-control" required>
                                    <option value="">&mdash; Choose Project &mdash;</option>
                                    <?php foreach(ORM::factory('Project')->find_all() as $project): ?>
                                        <option value="<?php echo $project->id; ?>"<?php echo ($issue->project_id == $project->id ? 'selected' : ''); ?>><?php echo $project->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Priority<span class="required-field">*</span></label>
                            <div class="col-sm-7">
                                <select name="issue[priority_id]" class="form-control">
                                    <?php
                                        foreach(ORM::factory('Issue_Priority')->find_all() as $priority):
                                            $selected = ($issue->priority_id == $priority->id) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $priority->id; ?>" <?php echo $selected; ?>><?php echo $priority->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Type<span class="required-field">*</span></label>
                            <div class="col-sm-7">
                                <select id="type_id" name="issue[type_id]" class="form-control" required>
                                    <option value="">&mdash; Choose Type &mdash;</option>
                                    <?php foreach(ORM::factory('Issue_Type')->find_all() as $type): ?>
                                        <option value="<?php echo $type->id; ?>" <?php echo ($issue->type_id == $type->id ? 'selected' : ''); ?>><?php echo $type->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <?php if ($issue->id): ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Status<span class="required-field">*</span></label>
                                <div class="col-sm-7">
                                    <select name="issue[status_id]" class="form-control" required>
                                        <?php foreach(ORM::factory('Issue_Status')->find_all() as $status): ?>
                                            <option value="<?php echo $status->id; ?>" <?php echo($issue->status_id == $status->id ? 'selected' : ''); ?>><?php echo $status->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div id="assignee" class="form-group">
                            <label class="col-sm-2 control-label">Assignee<span class="required-field">*</span></label>
                            <div class="col-sm-7">
                                <select id="assigned_department_id" name="issue[assigned_department_id]" class="form-control" required>
                                    <option value="">&mdash; Choose Department &mdash;</option>
                                    <?php foreach(ORM::factory('Department')->find_all() as $department): ?>
                                        <option value="<?php echo $department->id; ?>" <?php echo ($issue->assigned_department_id == $department->id ? 'selected' : ''); ?>><?php echo $department->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-2 control-label">Due Date / Time</label>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" name="issue[due_date]" class="form-control datepicker">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="issue[due_time]" class="form-control" data-parsley-id="3920">
                                    <option value=""></option>
                                    <option value="12:00 AM">12:00 AM</option>
                                    <option value="1:00 AM">1:00 AM</option>
                                    <option value="2:00 AM">2:00 AM</option>
                                    <option value="3:00 AM">3:00 AM</option>
                                    <option value="4:00 AM">4:00 AM</option>
                                    <option value="5:00 AM">5:00 AM</option>
                                    <option value="6:00 AM">6:00 AM</option>
                                    <option value="7:00 AM">7:00 AM</option>
                                    <option value="8:00 AM">8:00 AM</option>
                                    <option value="9:00 AM">9:00 AM</option>
                                    <option value="10:00 AM">10:00 AM</option>
                                    <option value="11:00 AM">11:00 AM</option>
                                    <option value="12:00 PM">12:00 PM</option>
                                    <option value="1:00 PM">1:00 PM</option>
                                    <option value="2:00 PM">2:00 PM</option>
                                    <option value="3:00 PM">3:00 PM</option>
                                    <option value="4:00 PM">4:00 PM</option>
                                    <option value="5:00 PM">5:00 PM</option>
                                    <option value="6:00 PM">6:00 PM</option>
                                    <option value="7:00 PM">7:00 PM</option>
                                    <option value="8:00 PM">8:00 PM</option>
                                    <option value="9:00 PM">9:00 PM</option>
                                    <option value="10:00 PM">10:00 PM</option>
                                    <option value="11:00 PM">11:00 PM</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Example URL <span class="required-field"></span></label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="255" name="issue[example_url]" class="form-control" value="<?php echo $issue->example_url; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Title<span class="required-field">*</span></label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="128" name="issue[summary]" placeholder="A brief description" class="form-control" value="<?php echo $issue->summary; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Description<span class="required-field">*</span></label>
                            <div class="col-sm-7">
                                <textarea name="issue[description]" class="form-control" rows="5" placeholder="" required><?php echo $issue->description; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Attachments</label>
                            <div class="col-sm-7">
                                <div class="dropzone">
                                    <div class="dz-message text-muted" data-dz-message><h3 class="text-center">Click or drop files to upload.</h3></div>
                                </div>
                            </div>
                        </div>

                        <div id="external_cms" class="form-group">
                            <label class="col-sm-2 control-label">External CMS</label>
                            <div class="col-sm-3">
                                <select id="pms_id" name="issue[pms_id]" class="form-control">
                                  <option value="">&mdash; Choose External CMS &mdash;</option>
                                  <?php foreach(ORM::factory('Pms')->find_all() as $pms): ?>
                                    <option value="<?php echo $pms->id; ?>" <?php echo ($issue->pms_id == $pms->id ? 'selected' : ''); ?>><?php echo $pms->name; ?></option>
                                  <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" maxlength="20" name="issue[external_cms_id]" placeholder="External CMS ID" class="form-control" value="<?php echo $issue->external_cms_id; ?>">
                            </div>
                        </div>

                        <div id="external_cms" class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="500" name="issue[external_cms_url]" placeholder="External CMS URL" class="form-control" value="<?php echo $issue->external_cms_url; ?>">
                            </div>
                        </div>

                    </div>

                    <div class="">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="/issues">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    // Toggle assignee based on type selection
    $('#type_id').change(function() {
        if ($(this).val() == '<?php echo Model_Issue_Type::SUPPORT_REQUEST; ?>') {
            $('#assignee').hide();
            $('#assigned_department_id').attr('required', false);
        }
        else {
            $('#assignee').show();
            $('#assigned_department_id').attr('required', true);
        }
    });

    // Bind datepicker
    $('.datepicker').datepicker();

    // Bind Dropzone
    Dropzone.autoDiscover = false; // Disabling autoDiscover, otherwise Dropzone will try to attach twice.
    var dropzone = new Dropzone('.dropzone', {
        url: '/issue_files/upload_temp/<?php echo $attachment_temp_dir; ?>',
        acceptedFiles: 'image/jpeg, image/jpg, image/png, image/gif',
        maxFilesize: 4, // MB
        maxFiles: 3,
        addRemoveLinks: true,
        autoProcessQueue: true,
        removedfile: function(file) {
            $.post('/issue_files/remove_temp', {filename: file.name, dir: '<?php echo $attachment_temp_dir; ?>'});
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        }
    });
});
</script>

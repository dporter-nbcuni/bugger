<section class="content-header">
    <h1>
        <i class="fa fa-bug"></i> <span id="summary"><?php echo Text::limit_chars($issue->summary, 120); ?></span>
    </h1>
</section>

<section class="content page-view-issue">
    <div class="row">
        <div class="col-xs-8">
            <h4 class="page-header">Description</h4>

            <div class="row">
                <div class="col-xs-12"><span id="description"><?php echo $issue->description; ?></span></div>
            </div>

            <?php include Kohana::find_file('views', 'issues/_view_attachments'); ?>

            <?php include Kohana::find_file('views', 'issues/_view_comments'); ?>
        </div>
        <div class="col-xs-4">
            <div class="box box-solid">
                <div class="box-header with-border">
                <h3 class="box-title">Details</h3>
                </div>
                <div class="box-body">
                    <div class="text-muted row"><div class="col-md-3">Key:</div> <div class="col-md-8"><?php echo $issue->trackingId(); ?></div></div>
                    <div class="text-muted row"><div class="col-md-3">Status:</div> <div class="col-md-8"> <span class="label" id="status_id" style="background: <?php echo $issue->status->color; ?>" data-value="<?php echo $issue->status_id; ?>"><?php echo $issue->status->name; ?></span></div></div>
                    <div class="text-muted row"><div class="col-md-3">Type:</div> <div class="col-md-8"> <span id="type_id" data-value="<?php echo $issue->type_id; ?>"><?php echo $issue->type->name; ?></span></div></div>
                    <div class="text-muted row"><div class="col-md-3">Priority:</div> <div class="col-md-8"> <span id="priority_id" data-value="<?php echo $issue->priority_id; ?>"><?php echo $issue->priority->name; ?></span></div></div>
                    <div class="text-muted row"><div class="col-md-3">Duplicate:</div> <div class="col-md-8"> <span id="duplicate_id" data-value="<?php echo $issue->duplicate_id; ?>"><?php echo ($issue->duplicate_id) ? $issue->duplicate->trackingId() : ''; ?></span></div></div>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="box box-solid">
                <div class="box-header with-border">
                <h3 class="box-title">People</h3>
                </div>
                <div class="box-body">
                    <div class="text-muted row"><div class="col-md-3">Reporter:</div> <div class="col-md-8"> <?php echo $issue->reporter->name; ?></div></div>
                    <div class="text-muted row"><div class="col-md-3">Assignee:</div> <div class="col-md-8"> <span id="assigned_department_id"><?php echo $issue->assigned_department->name; ?></span></div></div>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="box box-solid">
                <div class="box-header with-border">
                <h3 class="box-title">Dates</h3>
                </div>
                <div class="box-body">
                    <div class="text-muted row"><div class="col-md-3">Created:</div> <div class="col-md-8"> <?php echo $issue->created_at; ?></div></div>
                    <div class="text-muted row"><div class="col-md-3">Updated:</div> <div class="col-md-8"> <?php echo $issue->updated_at; ?></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="/assets/js/pages/issue/lazy-comments.js"></script>
<script src="/assets/js/page/issue/editable-fields.js"></script>

<script>
$(function() {
    var issueId = '<?php echo $issue->id; ?>';

    // Bind editable fields and lazy comments
    EditableFields.init(issueId);
    LazyComments.init(issueId);

    // Bind fancybox
    $('.fancybox').fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });

    // Bind dropzone
    Dropzone.autoDiscover = false; // Disabling autoDiscover, otherwise Dropzone will try to attach twice.
    var dropzone = new Dropzone('.dropzone', {
        url: '/issue_files/upload/<?php echo $issue->id; ?>',
        acceptedFiles: 'image/jpeg, image/jpg, image/png, image/gif',
        maxFilesize: 4, // MB
        maxFiles: 3,
        addRemoveLinks: false,
        autoProcessQueue: true,
        previewTemplate: '<div class="dz-preview dz-file-preview">' +
              '<div class="dz-details">' +
                '<div class="dz-filename"><span data-dz-name></span></div>' +
                '<div class="dz-size" data-dz-size></div>' +
                '<img data-dz-thumbnail />' +
              '</div>' +
              '<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>' +
              '<div class="dz-success-mark"><span>✔</span></div>' +
              '<div class="dz-error-message"><span data-dz-errormessage></span></div>' +
            '</div>'
    });

    // Bind attachment remove button click handler
    $('.btn-remove-attachment').click(function() {
        event.preventDefault();
        var $btn = $(this);
        if (confirm("Are you sure you want to delete this attachment?")) {
            $.post($btn.attr('href'));
            $btn.parent().parent().fadeOut('normal', function() {
                $(this).remove();
            });
        }
    });
});
</script>

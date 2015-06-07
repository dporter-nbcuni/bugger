/**
 * X-Editable Fields Class
 */
var EditableFields = {
    init: function(issueId) {
        var self = this;

        $.fn.editable.defaults.mode = 'inline';
        $.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit"><i class="fa fa-check"></i></button><button type="button" class="btn btn-default editable-cancel"><i class="fa fa-close"></i></button>';

        $('#summary').editable({
            url: '/issues/update_editable_field',
            type: 'textarea',
            pk: issueId,
            name: 'summary',
            placement: 'bottom',
            ajaxOptions: {
                type: 'POST'
            }
        });

        $('#description').editable({
            url: '/issues/update_editable_field',
            type: 'textarea',
            pk: issueId,
            name: 'description',
            ajaxOptions: {
                type: 'POST'
            }
        });

        $('#example_url').editable({
            url: '/issues/update_editable_field',
            type: 'textarea',
            pk: issueId,
            name: 'example_url',
            ajaxOptions: {
                type: 'POST'
            },
            emptytext: 'Click here to add URL'
        });

        $('#external_cms_id').editable({
            url: '/issues/update_editable_field',
            type: 'textarea',
            pk: issueId,
            name: 'external_cms_id',
            ajaxOptions: {
                type: 'POST'
            },
            emptytext: 'Click here to add ID'
        });

        $('#external_cms_url').editable({
            url: '/issues/update_editable_field',
            type: 'textarea',
            pk: issueId,
            name: 'external_cms_url',
            ajaxOptions: {
                type: 'POST'
            },
            emptytext: 'Click here to add URL'
        });

        $('#pms_id').editable({
            url: '/issues/update_editable_field',
            type: 'select',
            pk: issueId,
            name: 'pms_id',
            ajaxOptions: {
                type: 'POST'
            },
            source: '/issues/type_pms/' + issueId,
            sourceCache: true
        });

        $('#priority_id').editable({
            url: '/issues/update_editable_field',
            type: 'select',
            pk: issueId,
            name: 'priority_id',
            ajaxOptions: {
                type: 'POST'
            },
            source: '/issues/priority_options/' + issueId,
            sourceCache: true
        });

        $('#status_id').editable({
            url: '/issues/update_editable_field',
            type: 'select',
            pk: issueId,
            name: 'status_id',
            ajaxOptions: {
                type: 'POST'
            },
            source: '/issues/status_options/' + issueId,
            sourceCache: false
        });

        /*$('#assigned_department_id').editable({
            url: '/issues/update_editable_field',
            type: 'select',
            pk: issueId,
            name: 'assigned_department_id',
            ajaxOptions: {
                type: 'POST'
            },
            source: dataSources.departments
        });*/

        $('#type_id').editable({
            url: '/issues/update_editable_field',
            type: 'select',
            pk: issueId,
            name: 'type_id',
            ajaxOptions: {
                type: 'POST'
            },
            source: '/issues/type_options/' + issueId,
            sourceCache: true
        });

        $('#duplicate_id').editable({
            url: '/issues/update_editable_field',
            type: 'select',
            pk: issueId,
            name: 'duplicate_id',
            ajaxOptions: {
                type: 'POST'
            },
            source: '/issues/duplicate_options/' + issueId,
            sourceCache: true,
            emptytext: 'No'
        });
    }
}

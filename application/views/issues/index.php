<section class="content-header">
    <h1>
        <i class="fa fa-bug"></i> <?php echo $title; ?>
        <small><?php echo $subtitle; ?></small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <a href="/issues/new" class="btn btn-success">
                <i class="fa fa-plus"></i> Add New
            </a>
        </div>
    </div>
    
    <br>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Filter Tickets</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body">
                    <form id="filter-form" class="form-inline">
                        <label> Project</label>
                        <select name="project_id[]" class="col-xs-2" multiple>
                            <?php foreach($projects as $project): ?>
                                <option  value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
                            <?php endforeach; ?>
                        </select>

                       <label>Type</label>
                        <select name="type_id[]" class="col-xs-2" multiple>
                            <?php foreach($types as $type): ?>
                                <option  value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Status</label>
                        <select name="status_id[]" class="col-xs-2" multiple>
                            <?php foreach($statuses as $status): ?>
                                <option  value="<?php echo $status->id; ?>"><?php echo $status->name; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Priority</label>
                        <select name="priority_id[]" class="col-xs-2" multiple>
                            <?php foreach($priorities as $priority): ?>
                                <option value="<?php echo $priority->id; ?>"><?php echo $priority->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid box-primary">
                <div class="overlay loading" style="display: none;"></div><div class="loading loading-img" style="display: none;"></div>
                <div class="box-header">
                </div>
                <div class="box-body">
                    <div class="table-responsive"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(function() {
    FilterableTable.init();
});

var FilterableTable = {
    $form: null,

    init: function() {
        this.$form = $('#filter-form');
        this.initFilters();
        this.bindFilters();
        this.bindRowClickHanlder();
        this.reloadTable(window.location.hash.substr(1));
    },

    initFilters: function() {
        if (window.location.hash != '') {
            this.$form.deserialize(window.location.hash.substr(1));
        }
    },

    bindFilters: function() {
        var self = this;
        self.$form.find('select').multiselect({
            includeSelectAllOption: false,
            buttonClass: 'btn btn-primary',
            allSelectedText: 'All',
            nonSelectedText: 'All',
            //selectAllText: 'All',
            onChange: function(option, checked, select) {
                window.location.hash = self.$form.serialize();
                self.reloadTable(window.location.hash.substr(1));
            }
        });
    },

    bindRowClickHanlder: function() {
        $('.box').on('click', 'table tbody > tr.record', function() {
            window.location = $(this).find('td:first-child > a').attr('href');
        });
    },

    reloadTable: function(postData) {
        var $boxBody = $('.box-body'),
            $box = $('.box'),
            $loading = $('.loading');

        $loading.show();

        $.post('/issues/filter', postData, function(data) {
            $('.table-responsive').html(data);

            $('.table').DataTable({
                'aaSorting': [[9, 'desc']],
                'bLengthChange': false,
                'bFilter': false
            });
            
            $loading.hide();
        });
    }
}
</script>

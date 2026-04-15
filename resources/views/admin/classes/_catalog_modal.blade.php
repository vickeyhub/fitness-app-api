<div id="manageCatalogModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Manage session options</h4>
            </div>
            <div class="modal-body">
                <p class="text-muted small">These lists power the checkboxes when creating or editing a session. Changes apply after you save here; reload the page on the sessions list if a form is already open.</p>
                <form id="catalogAddForm" class="m-b-md">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>List</label>
                                <select name="type" id="catalog_new_type" class="form-control" required>
                                    <option value="muscle">Muscles</option>
                                    <option value="fitness_goal">Fitness goals</option>
                                    <option value="session_type">Session types</option>
                                    <option value="keyword">Keywords</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Label</label>
                                <input type="text" name="name" id="catalog_new_name" class="form-control" maxlength="255" required placeholder="e.g. Upper body">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Order</label>
                                <input type="number" name="sort_order" id="catalog_new_sort" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add option</button>
                </form>
                <hr>
                <div class="form-group">
                    <label>Show list</label>
                    <select id="catalog_list_type" class="form-control">
                        <option value="muscle">Muscles</option>
                        <option value="fitness_goal">Fitness goals</option>
                        <option value="session_type">Session types</option>
                        <option value="keyword">Keywords</option>
                    </select>
                </div>
                <div class="table-responsive" style="max-height:260px;overflow-y:auto;">
                    <table class="table table-condensed table-striped" id="catalogItemsTable">
                        <thead><tr><th>Name</th><th>Order</th><th></th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

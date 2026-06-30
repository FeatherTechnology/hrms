<!--holiday Creation Start-->
<div id="performance_creation_content">
    <form id="performance_analysis" name="performance_analysis" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" id="performance_id">
        <div class="row gutters">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">General Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="company_name" name="company_name" tabindex="1">
                                        <option value="">Select Company Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="criteria">Criteria</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="criteria" name="criteria" tabindex="2" placeholder="Enter Criteria">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="target_performance">Target Performance</label><span class="text-danger">(1-10)*</span>
                                    <input type="number" class="form-control" id="target_performance" name="target_performance" tabindex="3" min="1" max="10" oninput="if(this.value > 10) this.value = ''; if(this.value <= 0) this.value = '';" placeholder="Enter Target Performance">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="weightage">Weightage</label><span class="text-danger">(1%-100%) *</span>
                                    <input type="number" class="form-control" id="weightage" name="weightage" tabindex="4" min="1" max="100" oninput="if(this.value > 100) this.value = ''; if(this.value <= 0) this.value = '';"placeholder="Enter Weightage">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="effective_from">Effective From</label><span class="text-danger">*</span>
                                    <input type="month" class="form-control" id="effective_from" name="effective_from" tabindex="5">
                                </div>
                            </div>
                            <div class="col-md-3" style="display: flex; align-items: center;">
                                <button type="button" name="add_performance" id="add_performance" class="btn btn-primary" tabindex="6"></span>&nbsp;Add</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="performance_table_div">
                    <div class="card-header">
                        <div class="card-title"></div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="performance_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Criteria</th>
                                                <th>Target Performance</th>
                                                <th>Weightage</th>
                                                <th>Effective From</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align:right;">Total Weightage</th>
                                                <th id="total_weightage">0%</th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
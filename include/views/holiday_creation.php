<!--holiday Creation Start-->
<div id="holiday_creation_content">
    <form id="holiday_creation" name="holiday_creation" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" id="holiday_id">
        <div class="row gutters">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">General Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="company_name" name="company_name" tabindex="6">
                                        <option value="">Select Company Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: flex; align-items: center;">
                                <button type="button" name="search_holiday" id="search_holiday" class="btn btn-primary" tabindex="7"></span>&nbsp;Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="holiday_setup" style="display: none;">
                    <div class="card-header">
                        <div class="card-title">Holiday Calender Setup</div>
                    </div>
                    <div class="card-body">
                        <form id="holiday_creation_form">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="from_date"> From Date </label><span class="text-danger">*</span>
                                        <input type="date" class="form-control" id="from_date" name="from_date" tabindex="10">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="to_date"> To Date</label><span class="text-danger">*</span>
                                        <input type="date" class="form-control" id="to_date" name="to_date" tabindex="11">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="no_of_days">No of days</label><span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="no_of_days" name="no_of_days" tabindex="12" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="holiday_name">Holiday Name</label><span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="holiday_name" name="holiday_name" tabindex="12" placeholder="Enter Holiday Name">
                                    </div>
                                </div>
                                <div class="col-md-3" style="display: flex; align-items: center;">
                                    <button type="submit" name="submit_holiday_creation" id="submit_holiday_creation" class="btn btn-primary" value="Add" tabindex="18"> &nbsp;Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="holiday_creation_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>No of days</th>
                                                <th>Holiday Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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
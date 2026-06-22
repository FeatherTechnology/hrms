<div class="row gutters">
    <div class="col-12">

        <!--- Director Creation List Start --->
        <div class="text-right adddirectorbtn">
            <button type="button" class="btn btn-primary adddirectorbtn" id="add_director"><span class="fa fa-plus"></span>&nbsp; Add Director Creation</button>
        </div>
        <br>

        <div class="card director_table_content">
            <div class="card-body">
                <div class="col-12">

                    <table id="director_creation" class="table custom-table dtable">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Director ID</th>
                                <th>Director Name</th>
                                <th>State</th>
                                <th>District</th>
                                <th>Address</th>
                                <th>Mobile Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--- Director Creation List End --->

        <!--- Director Creation --->

        <div id="director_creation_content" style="display:none;">
            <div class="text-right backBtn">
                <button type="button" class="btn btn-primary backBtn" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
            </div>
            <br>
            <form id="director_creation" name="director_creation" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" id="directorID" value="0">
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
                                            <label for="director_id">Director ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="director_id" name="director_id" placeholder="Enter Director ID" tabindex="1" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="director_name">Director Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="director_name" name="director_name" placeholder="Enter Director Name" tabindex="2">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="state">State</label><span class="text-danger">*</span>
                                            <select type="text" class="form-control" id="state" name="state" tabindex="3">
                                                <option value="">Select State</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="district">District</label><span class="text-danger">*</span>
                                            <select type="text" class="form-control" id="district" name="district" tabindex="4">
                                                <option value="">Select District</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="address">Address</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" tabindex="5">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="mobile_number">Mobile Number</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="mobile_number" name="mobile_number" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter Mobile Number" tabindex="6">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-12 ">
                        <div class="text-right">
                            <button type="submit" name="submit_director_creation" id="submit_director_creation" class="btn btn-primary" value="Submit" tabindex="7"><span class="icon-check"></span>&nbsp;Submit</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <!----------------------------- CARD END  director CREATION FORM------------------------------>
    </div>
</div>
<!--Agent Creation List Start-->
<div class="text-right">
    <button type="button" class="btn btn-primary " id="add_agent"><span class="fa fa-plus"></span>&nbsp; Add Agent Creation</button>
    <button type="button" class="btn btn-primary" id="back_btn" style="display: none;"><span class="icon-arrow-left"></span>&nbsp; Back </button>
</div>
<br>
<div class="card agent_table_content">
    <div class="card-body">
        <div class="col-12">

            <table id="agent_create" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Agent ID</th>
                        <th>Agent Name</th>
                        <th>Area</th>
                        <th>Occupation</th>
                        <th>Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<!--Agent creation List End-->
<!--Agent Creation Start-->
<div id="agent_creation_content" style="display:none;">
    <form id="agent_creation" name="agent_creation" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" id="agent_id">
        <div class="row gutters">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Agent Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="agent_code"> Agent ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="agent_code" name="agent_code" placeholder="Enter Agent ID" disabled tabindex="1">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="agent_name"> Agent Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="agent_name" name="agent_name" pattern="[a-zA-Z\s]+" placeholder="Enter Agent Name" tabindex="2">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mobile1">Mobile No 1</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="mobile1" name="mobile1" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter Mobile No 1" tabindex="3">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mobile2">Mobile No 2</label>
                                    <input type="number" class="form-control" id="mobile2" name="mobile2" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter Mobile No 2" tabindex="4">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="area"> Area</label>
                                    <input type="text" class="form-control" id="area" name="area" pattern="[a-zA-Z\s]+" placeholder="Enter Area Name" tabindex="5">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="occupation"> Occupation</label>
                                    <input type="text" class="form-control" id="occupation" name="occupation" pattern="[a-zA-Z\s]+" placeholder="Enter occupation" tabindex="5">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 ">
                <div class="text-right">

                    <button type="submit" name="submit_agent_creation" id="submit_agent_creation" class="btn btn-primary" value="Submit" tabindex="6"><span class="icon-check"></span>&nbsp;Submit</button>
                    <button type="reset" class="btn btn-outline-secondary" tabindex="7">Clear</button>
                </div>
            </div>
        </div>
    </form>
</div>
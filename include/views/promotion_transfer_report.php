<style>

</style>
<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <select class="toggle-button" name='company_id' id='company_id'>
                <option value=''>Select Company</option>
            </select>
            <select class="toggle-button" name='department_id' id='department_id'>
                <option value=''>Select Department</option>
            </select>
            <select class="toggle-button" name='staff_id' id='staff_id'>
                <option value=''>Select Staff</option>
            </select>
            <input type="button" id='view_btn' name='view_btn' class="toggle-button" style="background-color: #f26b35;color:white" value='Search'>
        </div> <br />
        <!-- Promotion/Transfer report Start -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="careerChart"></div>
                    </div>
                 
                </div>
            </div>
        </div>
        <!--Promotion/Transfer report End-->
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
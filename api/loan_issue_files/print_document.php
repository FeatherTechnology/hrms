<style type="text/css">
    @media print {
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black;
        }

        td {
            padding: 10px;
            text-align: left;
        }
    }

    #printReceiptTable td.first-row {
        line-height: 2.5;
    }
    
    #printReceiptTable tr.last-row td {
        line-height: 3.5;
        /* margin-top: 30px; */
    }
</style>

<?php
require "../../ajaxconfig.php";
$cus_profile_id = $_POST['cus_profile_id'];
?>
<!---////////////////////////////////////////////////////////////////////////Personal Info start////////////////////////////////////////////////////////-->
<table class="table custom-table">
    <thead>
        <tr>
            <th colspan="7">Personal Info</th>
        </tr>
        <tr>
            <th width="20">S.NO</th>
            <th>Customer ID</th>
            <th>Customer Name</th>
            <th>Area</th>
            <th>Line</th>
            <th>Branch</th>
            <th>Mobile</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name , cp.mobile1   
        FROM customer_profile cp 
        LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
        LEFT JOIN area_name_creation anc ON cp.area = anc.id
        LEFT JOIN area_creation ac ON cp.line = ac.line_id
        LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
        LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
        WHERE cp.id= '$cus_profile_id'");
        if ($qry->rowCount() > 0) {
            $a = 1;
            while ($personal_info = $qry->fetchObject()) {
        ?>
                <tr>
                    <td><?php echo $a++; ?></td>
                    <td><?php echo $personal_info->cus_id; ?></td>
                    <td><?php echo $personal_info->cus_name; ?></td>
                    <td><?php echo $personal_info->area; ?></td>
                    <td><?php echo $personal_info->linename; ?></td>
                    <td><?php echo $personal_info->branch_name; ?></td>
                    <td><?php echo $personal_info->mobile1; ?></td>
                </tr>
            <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="7">
                    <center>No data available in table</center>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table> </br></br>
<!--////////////////////////////////////////////////////////////////////Personal Info End//////////////////////////////////////////////////////////////-->
<?php
/////////////////////////////////////////////////////////////////////////// Document Need START ////////////////////////////////////////////////////////
$qry = $pdo->query("SELECT document_name FROM document_need where cus_profile_id = '$cus_profile_id' ");
?>
<table class="table custom-table">
    <thead>
        <tr>
            <th colspan="2">Document Need</th>
        </tr>
        <tr>
            <th>S.No</th>
            <th>Document Name</th>
        </tr>
    </thead>
    <tbody>
<?php
if ($qry->rowCount() > 0) {
    $a = 1;
    while ($doc_need = $qry->fetchObject()) {
        ?>
        <tr>
            <td><?php echo $a++; ?></td>
            <td><?php echo $doc_need->document_name; ?></td>
        </tr>
        <?php
    }
}else{
    ?>
    <tr>
        <td colspan="2"><center>No data available in table</center></td>
    </tr>
<?php
}
?>
</tbody>
</table> </br></br>
<!-- /////////////////////////////////////////////////////////////////////////// Document Need END //////////////////////////////////////////////////////// -->


<!-- /////////////////////////////////////////////////////////////////////////// Cheque Info START //////////////////////////////////////////////////////// -->
<?php
$qry = $pdo->query("SELECT ci.* FROM cheque_info ci WHERE ci.cus_profile_id = $cus_profile_id GROUP BY ci.id  ");
if ($qry->rowCount() > 0) { // show table only if more than 1 row
?>
    <table class="table custom-table">
        <thead>
            <tr>
                <th colspan="6">Cheque Info</th>
            </tr>
            <tr>
                <th width="20">S.NO</th>
                <th>Holder Type</th>
                <th>Holder Name</th>
                <th>Relationship</th>
                <th>Bank Name</th>
                <th>Cheque Count</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $b = 1;
            while ($cheque_info = $qry->fetchObject()) {
                if ($cheque_info->holder_type == '1') {
                    $holder_type = 'Customer';
                } else if ($cheque_info->holder_type == '2') {
                    $holder_type = 'Guarantor';
                } else if ($cheque_info->holder_type == '3') {
                    $holder_type = 'Family Member';
                }
            ?>
                <tr>
                    <td><?php echo $b++; ?></td>
                    <td><?php echo $holder_type; ?></td>
                    <td><?php echo $cheque_info->holder_name; ?></td>
                    <td><?php echo $cheque_info->relationship; ?></td>
                    <td><?php echo $cheque_info->bank_name; ?></td>
                    <td><?php echo $cheque_info->cheque_cnt; ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    </br></br>
<?php
}
// if rows <= 1 -> do not show anything
?>

<!-- /////////////////////////////////////////////////////////////////////////// Cheque Info END //////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////////////// Document Info START //////////////////////////////////////////////////////// -->
<?php
$qry = $pdo->query("SELECT di.doc_name, di.doc_type, di.relationship,  CASE 
            WHEN di.holder_name = 0 THEN cp.cus_name 
            ELSE fi.fam_name 
        END as holder_name FROM document_info di LEFT JOIN family_info fi ON di.holder_name = fi.id  LEFT JOIN customer_profile cp ON di.cus_profile_id= cp.id WHERE di.cus_profile_id = '$cus_profile_id'  ");

if ($qry->rowCount() > 0) {
?>
    <table class="table custom-table">
        <thead>
            <tr>
                <th colspan="5">Document Info</th>
            </tr>
            <tr>
                <th width="20">S.NO</th>
                <th>Document Name</th>
                <th>Document Type</th>
                <th>Holder Name</th>
                <th>Relationship</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $c = 1;
            while ($doc_info = $qry->fetchObject()) {
            ?>
                <tr>
                    <td><?php echo $c++; ?></td>
                    <td><?php echo $doc_info->doc_name; ?></td>
                    <td><?php echo ($doc_info->doc_type == '1') ? 'Original' : 'Xerox'; ?></td>
                    <td><?php echo $doc_info->holder_name; ?></td>
                    <td><?php echo $doc_info->relationship; ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table> </br></br>
<?php
}
// if rows <= 1 -> do not show anything
?>
<!-- /////////////////////////////////////////////////////////////////////////// Document Info END //////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////////////// Mortgage Info START //////////////////////////////////////////////////////// -->
<?php
        $qry = $pdo->query("SELECT mi.relationship, mi.property_details, mi.mortgage_name, mi.designation, mi.mortgage_number, mi.reg_office, mi.mortgage_value, CASE 
            WHEN mi.property_holder_name = 0 THEN cp.cus_name 
            ELSE fi.fam_name 
        END as holder_name FROM mortgage_info mi LEFT JOIN family_info fi ON mi.property_holder_name = fi.id LEFT JOIN customer_profile cp ON mi.cus_profile_id= cp.id WHERE mi.cus_profile_id = '$cus_profile_id' ");
        if ($qry->rowCount() > 0) {
            ?>
<table class="table custom-table">
    <thead>
        <tr>
            <th colspan="9">Mortgage Info</th>
        </tr>
        <tr>
            <th width="20">S.No</th>
            <th>Property Holder Name</th>
            <th>Relationship</th>
            <th>Property Detail</th>
            <th>Mortgage Name</th>
            <th>Designation</th>
            <th>Mortgage Number</th>
            <th>Reg Office</th>
            <th>Mortgage Value</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $d = 1;
            while ($mortgage_info = $qry->fetchObject()) {
        ?>
                <tr>
                    <td><?php echo $d++; ?></td>
                    <td><?php echo $mortgage_info->holder_name; ?></td>
                    <td><?php echo $mortgage_info->relationship; ?></td>
                    <td><?php echo $mortgage_info->property_details; ?></td>
                    <td><?php echo $mortgage_info->mortgage_name; ?></td>
                    <td><?php echo $mortgage_info->designation; ?></td>
                    <td><?php echo $mortgage_info->mortgage_number; ?></td>
                    <td><?php echo $mortgage_info->reg_office; ?></td>
                    <td><?php echo $mortgage_info->mortgage_value; ?></td>
                </tr>
            <?php
            }
            ?>
    </tbody>
</table> </br></br>
<?php
}
// if rows <= 1 -> do not show anything
?>
<!-- /////////////////////////////////////////////////////////////////////////// Mortgage Info END //////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////////////// Endorsement Info START //////////////////////////////////////////////////////// -->
<?php
        $qry = $pdo->query("SELECT ei.relationship, ei.vehicle_details, ei.endorsement_name, ei.key_original, ei.rc_original,  CASE 
            WHEN ei.owner_name = 0 THEN cp.cus_name 
            ELSE fi.fam_name 
        END as holder_name FROM endorsement_info ei LEFT JOIN family_info fi ON ei.owner_name = fi.id LEFT JOIN customer_profile cp ON ei.cus_profile_id= cp.id WHERE ei.cus_profile_id = '$cus_profile_id'");
        if ($qry->rowCount() > 0) {
            ?>
<table class="table custom-table">
    <thead>
        <tr>
            <th colspan="7">Endorsement Info</th>
        </tr>
        <tr>
            <th width="20">S.NO</th>
            <th>Owner Name</th>
            <th>Relationship</th>
            <th>Vehicle Details</th>
            <th>Endorsement Name</th>
            <th>Key Original</th>
            <th>RC Original</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $e = 1;
            while ($endorsement_info = $qry->fetchObject()) {
        ?>
                <tr>
                    <td><?php echo $e++; ?></td>
                    <td><?php echo $endorsement_info->holder_name; ?></td>
                    <td><?php echo $endorsement_info->relationship; ?></td>
                    <td><?php echo $endorsement_info->vehicle_details; ?></td>
                    <td><?php echo $endorsement_info->endorsement_name; ?></td>
                    <td><?php echo $endorsement_info->key_original; ?></td>
                    <td><?php echo $endorsement_info->rc_original; ?></td>
                </tr>
            <?php
            }
        ?>
    </tbody>
</table> </br></br>
<?php
}
// if rows <= 1 -> do not show anything
?>
<!-- /////////////////////////////////////////////////////////////////////////// Endorsement Info END //////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////////////// Gold Info START //////////////////////////////////////////////////////// -->
<?php
        $qry = $pdo->query("SELECT * FROM gold_info WHERE cus_profile_id = '$cus_profile_id' ");
        if ($qry->rowCount() > 0) {
            ?>
<table class="table custom-table">
    <thead>
        <tr>
            <th colspan="5">Gold Info</th>
        </tr>
        <tr>
            <th width="20">S.NO</th>
            <th>Gold Type</th>
            <th>Purity</th>
            <th>Weight</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
 <?php
            $f = 1;
            while ($gold_info = $qry->fetchObject()) {
        ?>
                <tr>
                    <td><?php echo $f++; ?></td>
                    <td><?php echo $gold_info->gold_type; ?></td>
                    <td><?php echo $gold_info->purity; ?></td>
                    <td><?php echo $gold_info->weight; ?></td>
                    <td><?php echo $gold_info->value; ?></td>
                </tr>
            <?php
            }
            ?>
    </tbody>
</table> </br></br>
<?php
}
// if rows <= 1 -> do not show anything
?>
<!-- /////////////////////////////////////////////////////////////////////////// Gold Info END //////////////////////////////////////////////////////// -->


<?php
$pdo = null; //Connection Close.
?>
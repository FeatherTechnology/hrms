$(document).ready(function () {

    $('#company_id').on('change', function () {
        let company_id = $(this).val();
        if (company_id) {
            getDepartmentList(company_id, '#department_id', '');
        } else {
            $('#department_id').empty().append('<option value="">Select Department</option>');
            $('#staff_id').empty().append('<option value="">Select Staff</option>');
        }
    });

    // Department Change
    $('#department_id').on('change', function () {

        let company_id = $('#company_id').val();
        let dept_id = $('#department_id').val();
        getStaffList(company_id, dept_id);

    });

    $('#view_btn').click(function (e) {
        e.preventDefault();

        let company_id = $('#company_id').val();
        let department_id = $('#department_id').val();
        let staff_id = $('#staff_id').val();

        if (company_id != '' && department_id != '' && staff_id != '') {
            $.ajax({
                url: 'api/report_files/get_promotion_transfer_report.php',
                type: 'POST',
                data: {
                    company_id: $('#company_id').val(),
                    department_id: $('#department_id').val(),
                    staff_id: $('#staff_id').val()
                },
                dataType: 'json',
                success: function (response) {
                    drawCareerChart(response);
                }
            });
        } else {

            swalError('Please Fill All Fields!', 'All fields are required.');
        }



    });



});


$(function () {
    getCompanyName('#company_id')
})

async function getCompanyName(selector) {
    return new Promise((resolve, reject) => {
        $.post(
            "api/branch_creation/getCompanyName.php",
            {},

            function (response) {
                let dropdown = $(selector);
                dropdown.empty();
                dropdown.append('<option value="">Select Company</option>');
                $.each(response, function (index, item) {
                    dropdown.append(
                        `<option value="${item.id}">${item.company_name}
                        </option>`,
                    );
                });

                resolve();
            },

            "json",
        ).fail(function (xhr, status, error) {
            reject(error);
        });
    });
}

async function getDepartmentList(company_id, selector, selected_dept = '') {
    try {
        const response = await $.ajax({
            url: 'api/staff_creation/company_mapped_department.php',
            type: 'POST',
            dataType: 'json',
            data: {
                company_id: company_id,
                selected_dept: selected_dept
            }
        });

        let deptOption = '<option value="">Select Department</option>';

        $.each(response, function (index, val) {
            deptOption += `<option value="${val.id}">${val.department_name}</option>`;
        });

        $(selector).empty().append(deptOption);

    } catch (error) {
        console.error(error);
    }
}


async function getStaffList(company_id, dept_id) {
    // Validation
    if (company_id == '' || dept_id == '') {

        $('#staff_id').html('<option value="">Select Staff</option>');
        return false;
    }
    try {
        const response = await $.ajax({
            url: 'api/staff_creation/company_mapped_staff.php',
            type: 'POST',
            dataType: 'json',
            data: {
                company_id: company_id,
                dept_id: dept_id,
            }
        });

        let option = '<option value="">Select Staff</option>';
        $.each(response, function (index, val) {
            option += ` <option value="${val.id}">   ${val.staff_name}  </option> `;
        });
        $('#staff_id').empty().append(option);
    } catch (error) {
        console.error(error);

    }
}


function drawCareerChart(data) {
    $('#careerChart').html('');

    // 1. Maintain identical chronological logic sequence in JS layer
    data.sort((a, b) => {
        const dateA = new Date(a.event_date || a.effective_from || a.joining_date || a.created_on);
        const dateB = new Date(b.event_date || b.effective_from || b.joining_date || b.created_on);

        if (dateA.getTime() === dateB.getTime()) {
            const statusA = parseInt(a.occ_status || 0);
            const statusB = parseInt(b.occ_status || 0);
            if (statusA !== statusB) {
                return statusA - statusB;
            }
            return parseInt(a.id || 0) - parseInt(b.id || 0);
        }
        return dateA - dateB;
    });

    const lineSeriesData = [];
    const pointAnnotations = [];

    const joinedData = [];
    const promotionData = [];
    const transferData = [];
    const incrementData = [];

    const statusMap = {
        0: { text: 'Joined', color: '#1565c0' },
        1: { text: 'Promotion', color: '#e65100' },
        2: { text: 'Transfer', color: '#6a1b9a' },
        3: { text: 'Increment', color: '#2e7d32' }
    };

    let previousSalary = 0;

    data.forEach(function (row, index) {
        const eventDate = row.event_date || row.effective_from || row.joining_date || row.created_on;
        const currentSalary = parseInt(row.total_ctc || 0);
        const statusCode = parseInt(row.occ_status);
        const statusConfig = statusMap[statusCode] || { text: 'Event', color: '#333333' };
        const timestamp = new Date(eventDate).getTime();

        // Check the previous milestone record to detect specific department/branch shifts
        const prevRow = index > 0 ? data[index - 1] : null;

        // Store past history labels directly inside the node data objects
        row.old_designation = prevRow ? prevRow.designation : '-';
        row.old_branch = prevRow ? (prevRow.branch_name || '-') : '-';
        row.old_department = prevRow ? (prevRow.department_name || '-') : '-';
        row.old_team = prevRow ? (prevRow.team_name || '-') : '-';

        lineSeriesData.push({
            x: timestamp,
            y: currentSalary
        });

        // Compute step deltas for same-day stacking
        let barValue = currentSalary;
        if (prevRow) {
            const prevEventDate = prevRow.event_date || prevRow.effective_from || prevRow.joining_date || prevRow.created_on;
            if (new Date(prevEventDate).getTime() === timestamp) {
                barValue = currentSalary - previousSalary;
            }
        }

        const pointCoords = { x: timestamp, y: barValue };

        if (statusCode === 0) joinedData.push(pointCoords);
        else if (statusCode === 1) promotionData.push(pointCoords);
        else if (statusCode === 2) transferData.push(pointCoords);
        else if (statusCode === 3) incrementData.push(pointCoords);

        pointAnnotations.push({
            x: timestamp,
            y: currentSalary,
            marker: {
                size: 6,
                fillColor: '#ffffff',
                strokeColor: statusConfig.color,
                strokeWidth: 3
            },
            label: {
                text: statusConfig.text,
                borderColor: statusConfig.color,
                style: {
                    background: statusConfig.color,
                    color: '#ffffff',
                    fontSize: '11px',
                    fontWeight: 'bold',
                    padding: { left: 6, right: 6, top: 4, bottom: 4 }
                },
                offsetY: -10
            }
        });

        previousSalary = currentSalary;
    });

    const options = {
        chart: {
            height: 560,
            type: 'bar',
            stacked: true,
            toolbar: { 
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: true,
                    zoomout: true,
                    pan: false,
                    reset: true
                },
             }
        },
        colors: ['#1565c0', '#e65100', '#6a1b9a', '#2e7d32', '#4a90e2'],
        series: [
            { name: 'Joined Milestones', type: 'column', data: joinedData },
            { name: 'Promotion Milestones', type: 'column', data: promotionData },
            { name: 'Transfer Milestones', type: 'column', data: transferData },
            { name: 'Increment Milestones', type: 'column', data: incrementData },
            { name: 'Salary Step Tracker', type: 'line', data: lineSeriesData }
        ],
        stroke: {
            curve: 'stepline',
            width: [0, 0, 0, 0, 3]
        },
        plotOptions: {
            bar: {
                columnWidth: '20%',
                borderRadius: 0
            }
        },
        xaxis: { type: 'datetime' },
        yaxis: {
            labels: {
                formatter: function (val) { return '₹' + Number(val).toLocaleString(); }
            },
            title: { text: 'Salary (CTC)', style: { fontWeight: 600 } }
        },
        annotations: { points: pointAnnotations },
        dataLabels: { enabled: false },
        tooltip: {
            shared: true,
            intersect: false,
            // Turn off default wrapper styling, backgrounds, and shadows
            theme: 'none', 
            style: {
                fontSize: '13px',
                fontFamily: 'Arial, sans-serif'
            },
            custom: function ({ dataPointIndex, w }) {
                const activeSeriesData = w.config.series[4].data[dataPointIndex];
                if (!activeSeriesData) return '';

                const targetTimestamp = activeSeriesData.x;
                const matchingRows = data.filter(row => {
                    const d = row.event_date || row.effective_from || row.joining_date || row.created_on;
                    return new Date(d).getTime() === targetTimestamp;
                });

                if (matchingRows.length === 0) return '';
                
                // Keep Transfer showing first over Increment inside the card view layout loop
                matchingRows.reverse();

                const eventDate = matchingRows[0].event_date || matchingRows[0].effective_from || matchingRows[0].joining_date || matchingRows[0].created_on;
                const readableDate = new Date(eventDate).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });

                // --- FIX: Forced border: none; and box-shadow: none; to completely flatten the box ---
                let htmlBlock = `<div style="padding: 14px; min-width: 320px; background: #ffffff; border: none; box-shadow: none; font-family: Arial, sans-serif;">
                    <div style="font-size: 11px; color: #777; margin-bottom: 8px; text-align: right; font-weight: bold;">Effective Date: ${readableDate}</div>`;

                matchingRows.forEach((row, i) => {
                    const statusConfig = statusMap[parseInt(row.occ_status)] || { text: 'Event', color: '#333' };
                    const inlineStyle = i > 0 ? `margin-top: 12px; border-top: 1px dashed #ddd; padding-top: 12px;` : '';

                    let detailRowsHtml = '';

                    if (parseInt(row.occ_status) === 1) { // Promotion Details
                        detailRowsHtml = `
                            <tr><td style="padding: 4px 0; color: #555;"><b>Designation:</b></td><td style="padding: 4px 0; text-align: right; font-size:12px;"><span style="color:#777;">${row.old_designation}</span> &rarr; <b>${row.designation || '-'}</b></td></tr>
                        `;
                    } else if (parseInt(row.occ_status) === 2) { // Transfer Details
                        detailRowsHtml = `
                            <tr><td style="padding: 4px 0; color: #555;"><b>Branch:</b></td><td style="padding: 4px 0; text-align: right; font-size:12px;"><span style="color:#777;">${row.old_branch}</span> &rarr; <b>${row.branch_name || '-'}</b></td></tr>
                            <tr><td style="padding: 4px 0; color: #555;"><b>Department:</b></td><td style="padding: 4px 0; text-align: right; font-size:12px;"><span style="color:#777;">${row.old_department}</span> &rarr; <b>${row.department_name || '-'}</b></td></tr>
                            <tr><td style="padding: 4px 0; color: #555;"><b>Team:</b></td><td style="padding: 4px 0; text-align: right; font-size:12px;"><span style="color:#777;">${row.old_team}</span> &rarr; <b>${row.team_name || '-'}</b></td></tr>
                        `;
                    } else { // Joined or standard Increment Details
                        detailRowsHtml = `
                            <tr><td style="padding: 4px 0; color: #555;"><b>Designation:</b></td><td style="padding: 4px 0; text-align: right;">${row.designation || '-'}</td></tr>
                            <tr><td style="padding: 4px 0; color: #555;"><b>Branch/Dept:</b></td><td style="padding: 4px 0; text-align: right;">${row.branch_name || '-'} (${row.department_name || '-'})</td></tr>
                            <tr><td style="padding: 4px 0; color: #555;"><b>Team:</b></td><td style="padding: 4px 0; text-align: right;">${row.team_name || '-'}</td></tr>
                        `;
                    }

                    htmlBlock += `
                        <div style="${inlineStyle}">
                            <div style="font-size: 14px; font-weight: 700; color: ${statusConfig.color}; margin-bottom: 6px; border-bottom: 1px solid #eee; padding-bottom: 4px;">
                                ${statusConfig.text}
                            </div>
                            <table style="width: 100%; font-size: 13px; border-collapse: collapse; color: #333;">
                                <tr><td style="padding: 4px 0; color: #555;"><b>Salary:</b></td><td style="padding: 4px 0; text-align: right; font-weight: bold; color: #2e7d32;">₹${Number(row.total_ctc || 0).toLocaleString()}</td></tr>
                                ${detailRowsHtml}
                            </table>
                        </div>`;
                });

                htmlBlock += `</div>`;
                return htmlBlock;
            }
        },
        title: {
            text: (data.length > 0 ?
                (data[0].staff_name || 'Employee') + ' - ' + (data[0].staff_id || '') + ' - ' + (data[0].staff_type || '')
                : 'Employee Tracker'),
            align: 'center',
            style: { fontSize: '18px', fontWeight: 700 }
        }
    };

    if (window.careerChartObj) { window.careerChartObj.destroy(); }
    window.careerChartObj = new ApexCharts(document.querySelector("#careerChart"), options);
    window.careerChartObj.render();
}
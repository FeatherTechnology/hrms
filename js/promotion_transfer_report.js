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
            "api/attendance_files/get_company_list.php",
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

    // 1. Maintain chronological logic sequence
    data.sort((a, b) => {
        const dateA = new Date(a.event_date || a.effective_from || a.joining_date || a.created_on);
        const dateB = new Date(b.event_date || b.effective_from || b.joining_date || b.created_on);

        if (dateA.getTime() === dateB.getTime()) {
            const statusA = parseInt(a.occ_status || 0);
            const statusB = parseInt(b.occ_status || 0);
            if (statusA !== statusB) {
                return statusA - statusB; // Transfer (2) comes before Increment (3)
            }
            return parseInt(a.id || 0) - parseInt(b.id || 0);
        }
        return dateA - dateB;
    });

    const lineSeriesData = [];
    const pointAnnotations = [];
    
    // Four distinct series layers for perfect stacked control
    const transferBaseSeries = [];
    const transferNonCtcSeries = [];
    const incrementBaseSeries = [];
    const incrementNonCtcSeries = [];

    const statusMap = {
        0: { text: 'Joined', color: '#1565c0' },
        1: { text: 'Promotion', color: '#e65100' },
        2: { text: 'Transfer', color: '#6a1b9a' }, // Violet
        3: { text: 'Increment', color: '#2e7d32' } // Green
    };

    data.forEach(function (row, index) {
        const eventDate = row.event_date || row.effective_from || row.joining_date || row.created_on;
        
        const currentSalary = parseInt(row.total_ctc || 0);
        const grossTotal = parseInt(row.total_amount || currentSalary);
        const nonCtcSalary = Math.max(0, grossTotal - currentSalary);
        
        const statusCode = parseInt(row.occ_status);
        const statusConfig = statusMap[statusCode] || { text: 'Event', color: '#333333' };
        const timestamp = new Date(eventDate).getTime();

        const prevRow = index > 0 ? data[index - 1] : null;
        row.old_designation = prevRow ? prevRow.designation : '-';
        row.old_branch = prevRow ? (prevRow.branch_name || '-') : '-';
        row.old_department = prevRow ? (prevRow.department_name || '-') : '-';
        row.old_team = prevRow ? (prevRow.team_name || '-') : '-';

        // Continuous line path tracking gross value ceiling
        lineSeriesData.push({ x: timestamp, y: grossTotal });

        // Identify if this row shares a date with its neighbor to compute step delta
        let isSameDayEvent = false;
        let prevSalary = 0;
        if (prevRow) {
            const prevEventDate = prevRow.event_date || prevRow.effective_from || prevRow.joining_date || prevRow.created_on;
            if (new Date(prevEventDate).getTime() === timestamp) {
                isSameDayEvent = true;
                prevSalary = parseInt(prevRow.total_ctc || 0);
            }
        }

        // Distribute values across the 4 stacked layout layers
        if (isSameDayEvent && statusCode === 3) {
            // This is an Increment happening on the same day after a Transfer
            // Pop the placeholder 0 out from the previous iteration so we can layer directly on top
            incrementBaseSeries.pop();
            incrementNonCtcSeries.pop();
            
            incrementBaseSeries.push({ 
                x: timestamp, 
                y: currentSalary - prevSalary, // Visual height delta (e.g., 80000 - 45000 = 35000)
                fillColor: statusConfig.color 
            });
            incrementNonCtcSeries.push({ x: timestamp, y: nonCtcSalary });
        } else {
            // Normal base event or the leading event (Transfer) of the day
            transferBaseSeries.push({ 
                x: timestamp, 
                y: currentSalary, 
                fillColor: statusConfig.color 
            });
            transferNonCtcSeries.push({ x: timestamp, y: nonCtcSalary });
            
            incrementBaseSeries.push({ x: timestamp, y: 0 });
            incrementNonCtcSeries.push({ x: timestamp, y: 0 });
        }

        // Event milestone marker annotations
        pointAnnotations.push({
            x: timestamp,
            y: grossTotal,
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
    });

    const options = {
        chart: {
            height: 560,
            type: 'bar',
            stacked: true, // Forces components to align vertically over each other
            toolbar: { 
                show: true,
                tools: { download: true, selection: false, zoom: false, zoomin: true, zoomout: true, pan: false, reset: true }
            }
        },
        // Color mapping rules matching our 4 column index layout positions + line chart
        colors: [
            function({ seriesIndex, dataPointIndex, w }) {
                const dataConfig = w.config.series[seriesIndex].data[dataPointIndex];
                return (dataConfig && dataConfig.fillColor) ? dataConfig.fillColor : '#1565c0';
            },
            '#ff00ff', // Pink for Transfer Non-CTC block
            function({ seriesIndex, dataPointIndex, w }) {
                const dataConfig = w.config.series[seriesIndex].data[dataPointIndex];
                return (dataConfig && dataConfig.fillColor) ? dataConfig.fillColor : '#2e7d32';
            },
            '#ff00ff', // Pink for Increment Non-CTC block
            '#4a90e2'  // Line tracker path color
        ],
        series: [
            { name: 'Base CTC (Transfer/Initial)', type: 'column', data: transferBaseSeries },
            { name: 'Non-CTC Components (Transfer)', type: 'column', data: transferNonCtcSeries },
            { name: 'Base CTC Delta (Increment)', type: 'column', data: incrementBaseSeries },
            { name: 'Non-CTC Components (Increment)', type: 'column', data: incrementNonCtcSeries },
            { name: 'Salary Step Tracker', type: 'line', data: lineSeriesData }
        ],
        stroke: {
            curve: 'stepline',
            width: [0, 0, 0, 0, 3]
        },
        plotOptions: {
            bar: {
                columnWidth: '35%',
                borderRadius: 0
            }
        },
        xaxis: { type: 'datetime' },
        yaxis: {
            labels: {
                formatter: function (val) { return '₹' + Number(val).toLocaleString(); }
            },
            title: { text: 'Total Salary', style: { fontWeight: 600 } }
        },
        annotations: { points: pointAnnotations },
        dataLabels: { enabled: false },
        legend: {
            show: true,
            customLegendItems: ['Joined', 'Promotion', 'Transfer', 'Increment', 'Non-CTC Allowances'],
            markers: {
                fillColors: ['#1565c0', '#e65100', '#6a1b9a', '#2e7d32', '#ff00ff']
            },
            onItemClick: {
                toggleDataSeries: false // Prevents default engine conflict
            },
            click: function(chartContext, seriesIndex, config) {
                if (seriesIndex === 4) { 
                    chartContext.toggleSeries('Non-CTC Components (Transfer)');
                    chartContext.toggleSeries('Non-CTC Components (Increment)');
                } else if (seriesIndex === 3) {
                    chartContext.toggleSeries('Base CTC Delta (Increment)');
                } else {
                    chartContext.toggleSeries('Base CTC (Transfer/Initial)');
                }
            }
        },
        tooltip: {
            shared: false,      // Breaks data apart by the specific hovered block
            intersect: true,    // Fires only when the cursor touches a specific block segment
            theme: 'none', 
            style: { fontSize: '13px', fontFamily: 'Arial, sans-serif' },
            custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                const targetPointData = w.config.series[seriesIndex].data[dataPointIndex];
                if (!targetPointData || targetPointData.y === 0) return ''; 

                const targetTimestamp = targetPointData.x;
                
                const matchingRows = data.filter(row => {
                    const d = row.event_date || row.effective_from || row.joining_date || row.created_on;
                    return new Date(d).getTime() === targetTimestamp;
                });
                if (matchingRows.length === 0) return '';

                const isNonCtcHover = (seriesIndex === 1 || seriesIndex === 3);
                const getStatusConfig = (status) => statusMap[parseInt(status)] || { text: 'Event', color: '#333' };

                const eventDate = matchingRows[0].event_date || matchingRows[0].effective_from || matchingRows[0].joining_date || matchingRows[0].created_on;
                const readableDate = new Date(eventDate).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });

                let htmlBlock = `<div style="padding: 12px; min-width: 280px; background: #ffffff; border: none; box-shadow: 0px 2px 10px rgba(0,0,0,0.15); font-family: Arial, sans-serif;">
                    <div style="font-size: 11px; color: #777; margin-bottom: 6px; text-align: right; font-weight: bold;">Effective Date: ${readableDate}</div>`;

                // --- CONDITION A: MOUSE IS HOVERING A PINK NON-CTC BLOCK ---
                if (isNonCtcHover) {
                    const targetedRow = (seriesIndex === 3 && matchingRows.length > 1) ? matchingRows[1] : matchingRows[0];
                    const statusConfig = getStatusConfig(targetedRow.occ_status);

                    let allowancesHtml = '';
                    if (targetedRow.allowances && Array.isArray(targetedRow.allowances)) {
                        targetedRow.allowances.forEach(allw => {
                            const freqLabel = parseInt(allw.pay_frequency) === 2 ? 'Per Day' : 'Per Month';
                            allowancesHtml += `
                                <tr>
                                    <td style="padding: 4px 0; color: #555; font-size: 12px; padding-left: 4px;">• ${allw.salary_component}:</td>
                                    <td style="padding: 4px 0; text-align: right; font-size: 12px; color: #333; font-weight: bold;">₹${Number(allw.ctc_amount).toLocaleString()} (${freqLabel})</td>
                                </tr>`;
                        });
                    }

                    htmlBlock += `
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: #ff00ff; margin-bottom: 6px; border-bottom: 1px solid #eee; padding-bottom: 4px;">
                                Non-CTC(${statusConfig.text})
                            </div>
                            <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                                ${allowancesHtml ? allowancesHtml : '<tr><td colspan="2" style="color:#777; font-size:12px;">No specific components recorded.</td></tr>'}
                                <tr style="border-top: 1px dashed #eee;">
                                    <td style="padding: 6px 0 0 0; color: #555;"><b>Total Non-CTC Value:</b></td>
                                    <td style="padding: 6px 0 0 0; text-align: right; font-weight: bold; color: #ff00ff;">₹${Number(targetPointData.y).toLocaleString()}</td>
                                </tr>
                            </table>
                        </div>`;
                } 
                // --- CONDITION B: MOUSE IS HOVERING A BASE COLOR CTC BLOCK ---
                else {
                    const targetedRow = (seriesIndex === 2 && matchingRows.length > 1) ? matchingRows[1] : matchingRows[0];
                    const statusConfig = getStatusConfig(targetedRow.occ_status);

                    let detailRowsHtml = '';
                    if (parseInt(targetedRow.occ_status) === 1) { 
                        detailRowsHtml = `<tr><td style="padding: 4px 0; color: #555;"><b>Designation:</b></td><td style="padding: 4px 0; text-align: right; font-size:12px;"><span style="color:#777;">${targetedRow.old_designation}</span> &rarr; <b>${targetedRow.designation || '-'}</b></td></tr>`;
                    } else if (parseInt(targetedRow.occ_status) === 2) { 
                        detailRowsHtml = `
                            <tr><td style="padding: 4px 0; color: #555;"><b>Branch:</b></td><td style="padding: 4px 0; text-align: right; font-size:12px;"><span style="color:#777;">${targetedRow.old_branch}</span> &rarr; <b>${targetedRow.branch_name || '-'}</b></td></tr>
                            <tr><td style="padding: 4px 0; color: #555;"><b>Department:</b></td><td style="padding: 4px 0; text-align: right; font-size:12px;"><span style="color:#777;">${targetedRow.old_department}</span> &rarr; <b>${targetedRow.department_name || '-'}</b></td></tr>`;
                    } else { 
                        detailRowsHtml = `
                            <tr><td style="padding: 4px 0; color: #555;"><b>Designation:</b></td><td style="padding: 4px 0; text-align: right;">${targetedRow.designation || '-'}</td></tr>
                            <tr><td style="padding: 4px 0; color: #555;"><b>Branch/Dept:</b></td><td style="padding: 4px 0; text-align: right;">${targetedRow.branch_name || '-'} (${targetedRow.department_name || '-'})</td></tr>`;
                    }

                    htmlBlock += `
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: ${statusConfig.color}; margin-bottom: 6px; border-bottom: 1px solid #eee; padding-bottom: 4px;">
                                ${statusConfig.text} Details
                            </div>
                            <table style="width: 100%; font-size: 13px; border-collapse: collapse; color: #333;">
                                <tr><td style="padding: 4px 0; color: #555;"><b>CTC Salary:</b></td><td style="padding: 4px 0; text-align: right; font-weight: bold; color: #2e7d32;">₹${Number(targetedRow.total_ctc || 0).toLocaleString()}</td></tr>
                                <tr><td style="padding: 4px 0; color: #555;"><b>Gross Total:</b></td><td style="padding: 4px 0; text-align: right; font-weight: bold; color: #1565c0;">₹${Number(targetedRow.total_amount || targetedRow.total_ctc || 0).toLocaleString()}</td></tr>
                                ${detailRowsHtml}
                            </table>
                        </div>`;
                }

                htmlBlock += `</div>`;
                return htmlBlock;
            }
        },
        title: {
            text: data.length > 0 
                ? `${data[0].staff_name || 'Employee'}${data[0].staff_id ? ` - ${data[0].staff_id}` : ''}`
                : 'Employee Details',
            align: 'center',
            style: { fontSize: '18px', fontWeight: 700 }
        }
    };

    if (window.careerChartObj) { window.careerChartObj.destroy(); }
    window.careerChartObj = new ApexCharts(document.querySelector("#careerChart"), options);
    window.careerChartObj.render();
}
 async function generatePDF(userId = null) {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

            // 🟢 Page 1: Title Page
            const TitlepageWidth = pdf.internal.pageSize.getWidth();

            // 🖼️ Logos (replace with your actual image paths or base64)
            const leftLogo = "../../assets/images/Logo.jpg";   // e.g. Barangay logo
            const rightLogo = "../../assets/images/talavera.png"; // e.g. Municipality or BSIS logo

            // 🖼️ Add logos — make sure these images exist or are accessible
            try {
                const leftImg = await fetch(leftLogo).then(res => res.blob()).then(blob => URL.createObjectURL(blob));
                const rightImg = await fetch(rightLogo).then(res => res.blob()).then(blob => URL.createObjectURL(blob));

                // Draw logos (approx. 25mm wide)
                pdf.addImage(leftImg, "PNG", 25, 25, 25, 25);   // Left logo
                pdf.addImage(rightImg, "PNG", TitlepageWidth - 50, 25, 25, 25); // Right logo
            } catch (err) {
                console.warn("Logo images not found or failed to load:", err);
            }

            // 🟢 Title Section
            pdf.setFontSize(20);
            pdf.text("Community Health Report", TitlepageWidth / 2, 45, { align: "center" });
            pdf.setFontSize(14);
            pdf.text("Barangay Information System", TitlepageWidth / 2, 55, { align: "center" });
            pdf.setFontSize(10);
            pdf.text(`Generated on: ${new Date().toLocaleString()}`, TitlepageWidth / 2, 65, { align: "center" });

            // 🟢 Divider Line
            pdf.setDrawColor(100);
            pdf.line(30, 75, TitlepageWidth - 30, 75);

            // 🟢 Signature Lines
            pdf.setFontSize(11);
            pdf.text("Prepared by: ____________________", 35, 95);
            pdf.text("Approved by: ____________________", TitlepageWidth - 120, 95);

            pdf.addPage();

            // 🟢 Page 2: Health Table
            let url = "../../../backend/actions/user/fetch_user_health_table.php";
            if (userId) url += `?user_id=${userId}`;

            const response = await fetch(url);
            const result = await response.json();

            if (!result || result.status !== "success" || !result.data.length) {
                pdf.setFontSize(12);
                pdf.text("No health data found.", 14, 30);
                pdf.save("Community_Health_Report.pdf");
                return;
            }

            const rows = result.data.map((row) => {
                const fullName = [row.f_name, row.m_name, row.l_name, row.ext_name].filter(Boolean).join(" ");
                return [
                    fullName || "-",
                    row.email || "-",
                    row.health_condition || "-",
                    row.common_health_issue || "-",
                    row.vaccination_status || "-",
                    row.pwd_status || "-",
                    row.senior_citizen_status || "-",
                    row.blood_type || "-",
                    String(row.height_cm ?? "-"),
                    String(row.weight_kg ?? "-"),
                    row.last_medical_checkup || "-",
                    row.health_remarks || "-"
                ];
            });

            const headers = [[
                "Full Name",
                "Email",
                "Health Condition",
                "Common Health Issue",
                "Vaccination Status",
                "PWD",
                "Senior",
                "Blood",
                "Height (cm)",
                "Weight (kg)",
                "Last Checkup",
                "Remarks"
            ]];

            pdf.setFontSize(14);
            pdf.text("User Health Records", 14, 20);
            pdf.autoTable({
                head: headers,
                body: rows,
                startY: 25,
                theme: 'grid',
                styles: { fontSize: 9, cellPadding: 3, valign: 'middle' },
                headStyles: { fillColor: [33, 150, 243], textColor: 255, fontStyle: 'bold' },
                alternateRowStyles: { fillColor: [245, 245, 245] },
                margin: { left: 10, right: 10 }
            });

            // 🟣 New Page for Diagrams
            pdf.addPage();

            // 🟢 Page margins
            const marginX = 10;
            const marginY = 15;
            const pageWidth = pdf.internal.pageSize.getWidth();
            const usableWidth = pageWidth - marginX * 2;

            // 🟢 Title & subtitle
            pdf.setFontSize(12);
            pdf.text("Charts Overview", pageWidth / 2, marginY + 5, { align: "center" });
            pdf.setFontSize(8);
            pdf.text("Visual summary of health data", pageWidth / 2, marginY + 10, { align: "center" });

            // 🟢 Chart IDs (match your HTML IDs)
            const chartIds = [
                "genderChart", "bloodChart", "conditionChart", "issuesChart",
                "heightChart", "heightRangeChart", "weightChart", "bmiRangeChart",
                "pwdChart", "seniorChart", "vaccinationChart", "conditionSeverityChart",
                "ageGroupChart", "healthTrendChart", "commonIllnessChart", "checkupFrequencyChart"
            ];

            // ⚙️ Grid configuration (4x4)
            const chartcolumns = 4;
            const chartrows = 4;
            const chartWidth = 45;  // smaller width for compact grid
            const chartHeight = 35;
            const hSpacing = (usableWidth - chartcolumns * chartWidth) / (chartcolumns - 1);
            const vSpacing = 12;  // vertical space between charts
            const startY = marginY + 20;

            // 🖼️ Capture and place charts
            let currentChart = 0;

            for (let r = 0; r < chartrows; r++) {
                for (let c = 0; c < chartcolumns; c++) {
                    if (currentChart >= chartIds.length) break;

                    const id = chartIds[currentChart];
                    const canvas = document.getElementById(id);

                    if (canvas) {
                        const x = marginX + c * (chartWidth + hSpacing);
                        const y = startY + r * (chartHeight + vSpacing);

                        const chartImg = await html2canvas(canvas, { scale: 2 }).then(el => el.toDataURL("image/png"));
                        pdf.addImage(chartImg, "PNG", x, y, chartWidth, chartHeight);
                    }
                    currentChart++;
                }
            }

            // Optional footer
            pdf.setFontSize(7);
            pdf.text("Generated by Barangay Information System", pageWidth / 2, 205, { align: "center" });

            // 🟢 Save the PDF
            pdf.save("Community_Health_Report.pdf");
        }
async function printCategory(category) {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
    const pageWidth = pdf.internal.pageSize.getWidth();

    // 🟢 Header
    const leftLogo = "../../assets/images/Logo.jpg";
    const rightLogo = "../../assets/images/talavera.png";
    try {
        const [leftImg, rightImg] = await Promise.all([
            fetch(leftLogo).then(res => res.blob()).then(blob => URL.createObjectURL(blob)),
            fetch(rightLogo).then(res => res.blob()).then(blob => URL.createObjectURL(blob))
        ]);
        pdf.addImage(leftImg, "PNG", 25, 25, 25, 25);
        pdf.addImage(rightImg, "PNG", pageWidth - 50, 25, 25, 25);
    } catch (err) {
        console.warn("Logo load failed:", err);
    }

    const formattedCategory = category.replace(/_/g, " ").replace(/\b\w/g, c => c.toUpperCase());
    pdf.setFontSize(20);
    pdf.text("Community Health Report", pageWidth / 2, 45, { align: "center" });
    pdf.setFontSize(14);
    pdf.text(`Category: ${formattedCategory}`, pageWidth / 2, 55, { align: "center" });
    pdf.setFontSize(10);
    pdf.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth / 2, 65, { align: "center" });
    pdf.line(30, 75, pageWidth - 30, 75);
    pdf.setFontSize(11);
    pdf.text("Prepared by: ____________________", 35, 95);
    pdf.text("Approved by: ____________________", pageWidth - 120, 95);

    pdf.addPage();

    // 🟢 Fetch data
    const url = `../../../backend/actions/user/print_health_category.php?category=${encodeURIComponent(category)}`;
    const response = await fetch(url);
    const result = await response.json();

    if (!result.success || !result.users?.length) {
        pdf.setFontSize(12);
        pdf.text(`No data found for category: ${formattedCategory}`, 14, 30);
        pdf.save(`Health_Report_${formattedCategory}.pdf`);
        return;
    }

    const safe = v => (v ? v : "-");
    let headers = [];
    let rows = [];

    // 🔸 Always include core info
    const baseInfo = u => [
        safe(u.full_name),
        safe(u.email),
        safe(u.gender),
        safe(u.age),
        safe(u.contact_no),
        safe(u.purok),
        safe(u.barangay),
        safe(u.municipality),
        safe(u.province)
    ];

    // 🔸 Append category-specific columns
    switch (category.toLowerCase()) {
        case "pwd":
            headers = [["Full Name", "Email", "Gender", "Age", "Contact No.", "Purok", "Barangay", "Municipality", "Province", "PWD Status", "Health Condition", "Remarks"]];
            rows = result.users.map(u => [...baseInfo(u), safe(u.pwd_status), safe(u.health_condition), safe(u.health_remarks)]);
            break;

        case "senior":
        case "senior citizen":
            headers = [["Full Name", "Email", "Gender", "Age", "Contact No.", "Purok", "Barangay", "Municipality", "Province", "Health Condition", "Common Issue", "Last Checkup"]];
            rows = result.users.map(u => [...baseInfo(u), safe(u.health_condition), safe(u.common_health_issue), safe(u.last_medical_checkup)]);
            break;

        case "vaccinated":
        case "unvaccinated":
            headers = [["Full Name", "Email", "Gender", "Age", "Contact No.", "Purok", "Barangay", "Municipality", "Province", "Vaccination Status", "Last Checkup"]];
            rows = result.users.map(u => [...baseInfo(u), safe(u.vaccination_status), safe(u.last_medical_checkup)]);
            break;

        case "underweight":
        case "overweight":
        case "obese":
            headers = [["Full Name", "Email", "Gender", "Age", "Contact No.", "Purok", "Barangay", "Municipality", "Province", "Height (cm)", "Weight (kg)", "BMI", "BMI Category", "Health Remarks"]];
            rows = result.users.map(u => [...baseInfo(u), safe(u.height_cm), safe(u.weight_kg), safe(u.bmi), safe(u.bmi_category), safe(u.health_remarks)]);
            break;

        case "healthy":
        case "diabetes":
        case "hypertension":
        case "asthma":
            headers = [["Full Name", "Email", "Gender", "Age", "Contact No.", "Purok", "Barangay", "Municipality", "Province", "Condition", "Common Issue", "Remarks"]];
            rows = result.users.map(u => [...baseInfo(u), safe(u.health_condition), safe(u.common_health_issue), safe(u.health_remarks)]);
            break;

        default:
            headers = [["Full Name", "Email", "Gender", "Age", "Contact No.", "Purok", "Barangay", "Municipality", "Province", "Health Condition", "Vaccination", "PWD", "Senior"]];
            rows = result.users.map(u => [...baseInfo(u), safe(u.health_condition), safe(u.vaccination_status), safe(u.pwd_status), safe(u.senior_citizen_status)]);
            break;
    }

    // 📋 Table
    pdf.autoTable({
        head: headers,
        body: rows,
        startY: 25,
        theme: "grid",
        styles: { fontSize: 8, cellPadding: 3, valign: "middle" },
        headStyles: { fillColor: [33, 150, 243], textColor: 255, fontStyle: "bold" },
        alternateRowStyles: { fillColor: [245, 245, 245] },
        margin: { left: 10, right: 10 }
    });

    // 📊 Chart page
    pdf.addPage();
    pdf.text(`Visual Summary for ${formattedCategory}`, pageWidth / 2, 15, { align: "center" });
    const chartIds = ["genderChart", "conditionChart", "vaccinationChart"];
    let x = 15, y = 30;
    for (const id of chartIds) {
        const canvas = document.getElementById(id);
        if (canvas) {
            const chart = await html2canvas(canvas, { scale: 2 });
            pdf.addImage(chart.toDataURL("image/png"), "PNG", x, y, 60, 45);
            x += 70;
        }
    }

    pdf.setFontSize(7);
    pdf.text("Generated by Barangay Information System", pageWidth / 2, 205, { align: "center" });
    pdf.save(`Health_Report_${formattedCategory}.pdf`);
}

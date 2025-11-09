async function downloadIncidentPDF(incidentId) {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({ orientation: "portrait", unit: "mm", format: "a4" });

    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();
    const margin = 15;
    const lineHeight = 7;

    // === 🖼 Logos ===
    const leftLogo = "../../assets/images/Logo.jpg";
    const rightLogo = "../../assets/images/talavera.png";

    try {
        const leftImg = await fetch(leftLogo).then(r => r.blob()).then(blob => URL.createObjectURL(blob));
        const rightImg = await fetch(rightLogo).then(r => r.blob()).then(blob => URL.createObjectURL(blob));
        pdf.addImage(leftImg, "JPEG", margin, 10, 25, 25);
        pdf.addImage(rightImg, "JPEG", pageWidth - margin - 25, 10, 25, 25);
    } catch (err) {
        console.warn("Logo images not found:", err);
    }

    // === 🟦 Header ===
    pdf.setFont("helvetica", "bold");
    pdf.setFontSize(18);
    pdf.setTextColor(30, 50, 80);
    pdf.text("Barangay Incident Reporting System", pageWidth / 2, 22, { align: "center" });

    pdf.setFont("helvetica", "normal");
    pdf.setFontSize(13);
    pdf.setTextColor(90);
    pdf.text("Official Incident Report Form", pageWidth / 2, 30, { align: "center" });

    pdf.setDrawColor(30, 50, 80);
    pdf.setLineWidth(0.8);
    pdf.line(margin, 45, pageWidth - margin, 45);

    // === Fetch Incident Data ===
    const res = await fetch(`../../../backend/actions/incident_report/fetch_single.php?id=${incidentId}`);
    const data = await res.json();
    if (!data || data.status !== "success") {
        alert("Incident data not found.");
        return;
    }

    const incident = data.incident;
    const persons = data.persons || [];

    // === 🗓 Prepared Date ===
    const preparedDate = new Date().toLocaleString();
    pdf.setFontSize(9);
    pdf.setTextColor(60);
    pdf.text(`Generated: ${preparedDate}`, margin, 40);

    let y = 50;

    // === 📋 Incident Details Section ===
    pdf.setFont("helvetica", "bold");
    pdf.setFontSize(12);
    pdf.setTextColor(0, 51, 102);
    pdf.text("INCIDENT DETAILS", margin, y);
    pdf.setDrawColor(0, 51, 102);
    pdf.setLineWidth(0.3);
    pdf.line(margin, y + 1, pageWidth - margin, y + 1);
    y += 10;

    pdf.setFont("helvetica", "normal");
    pdf.setFontSize(11);
    pdf.setTextColor(0);

    const details = [
        ["Incident ID", incident.incident_id],
        ["Category", incident.category || "-"],
        ["Type", incident.type || "-"],
        ["Date & Time", incident.date_time || "-"],
        ["Location", incident.location || "-"],
        ["Reporter", incident.reporter || "-"],
        ["Status", incident.status || "Pending"],
        ["Action Taken", incident.action_taken || "None"]
    ];

    details.forEach(([label, value]) => {
        pdf.setFont("helvetica", "bold");
        pdf.text(`${label}:`, margin, y);
        pdf.setFont("helvetica", "normal");
        pdf.text(value.toString(), margin + 40, y);
        y += lineHeight;
    });

    y += 5;

    // === 📝 Description ===
    pdf.setFont("helvetica", "bold");
    pdf.setFontSize(12);
    pdf.setTextColor(0, 51, 102);
    pdf.text("DESCRIPTION", margin, y);
    pdf.setLineWidth(0.3);
    pdf.line(margin, y + 1, pageWidth - margin, y + 1);
    y += 8;

    pdf.setFont("helvetica", "normal");
    pdf.setFontSize(11);
    pdf.setTextColor(0);
    const desc = pdf.splitTextToSize(incident.description || "No description provided.", pageWidth - margin * 2);
    pdf.text(desc, margin, y);
    y += desc.length * 6 + 10;

    // === 🖼 Photo ===
    if (incident.photo) {
        try {
            const imgData = await fetch(`../../../uploads/incidents/${incident.photo}`)
                .then(r => r.blob())
                .then(blob => URL.createObjectURL(blob));
            pdf.addImage(imgData, "JPEG", margin, y, 70, 60);
            y += 70;
        } catch (err) {
            console.warn("Incident photo not found:", err);
        }
    }

    // === 👥 Persons Involved ===
    if (persons.length) {
        y += 5;
        pdf.setFont("helvetica", "bold");
        pdf.setFontSize(12);
        pdf.setTextColor(0, 51, 102);
        pdf.text("PERSONS INVOLVED", margin, y);
        pdf.setLineWidth(0.3);
        pdf.line(margin, y + 1, pageWidth - margin, y + 1);
        y += 8;

        pdf.setFont("helvetica", "normal");
        pdf.setFontSize(11);
        pdf.setTextColor(0);
        persons.forEach((p, index) => {
            const name = p.res_fname
                ? `${p.res_fname} ${p.res_lname}`
                : `${p.nonres_fname || ""} ${p.nonres_lname || ""}`;
            pdf.text(`${index + 1}. ${p.role || "Role"} — ${name}`, margin, y);
            y += lineHeight;
        });
        y += 5;
    }

    // === ✍ Signature Section ===
    y += 10;
    pdf.setFont("helvetica", "bold");
    pdf.setFontSize(11);
    pdf.setTextColor(0, 51, 102);
    pdf.text("Prepared by:", margin, y);
    pdf.text("Approved by:", pageWidth / 2 + 30, y);

    pdf.setLineWidth(0.2);
    pdf.line(margin, y + 20, margin + 60, y + 20);
    pdf.line(pageWidth / 2 + 30, y + 20, pageWidth / 2 + 90, y + 20);

    y += 30;

    // === 📜 Footer ===
    pdf.setFont("helvetica", "italic");
    pdf.setFontSize(8);
    pdf.setTextColor(100);
    pdf.text("Generated by Barangay Incident Reporting System", pageWidth / 2, pageHeight - 10, { align: "center" });

    // === Save PDF ===
    pdf.save(`Incident_Report_${incident.incident_id}.pdf`);
}

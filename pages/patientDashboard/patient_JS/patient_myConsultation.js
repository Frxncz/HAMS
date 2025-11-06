// --- Show Appointment Details Modal ---
function openDetailsModal(appt) {
  const modal = document.getElementById("detailsModal");
  const content = document.getElementById("modalContent");

  content.innerHTML = `
    <p><strong>Doctor:</strong> ${appt.doctor_name}</p>
    <p><strong>Specialty:</strong> ${appt.specialty ?? "General Practitioner"}</p>
    <p><strong>Date:</strong> ${appt.appt_date}</p>
    <p><strong>Time:</strong> ${appt.appt_time}</p>
    <p><strong>Purpose:</strong> ${appt.purpose}</p>
    <p><strong>Status:</strong> ${appt.status}</p>
  `;
  modal.style.display = "flex";
}

// --- Close Modal ---
function closeModal() {
  document.getElementById("detailsModal").style.display = "none";
}

// --- Cancel Appointment ---
function cancelAppointment(id) {
  if (confirm("Are you sure you want to cancel this appointment?")) {
    fetch("../../backend/cancel_appointment.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id=" + id
    }).then(() => location.reload());
  }
}

// --- Optional: Close modal when clicking outside ---
document.addEventListener("click", (e) => {
  const modal = document.getElementById("detailsModal");
  if (e.target === modal) closeModal();
});

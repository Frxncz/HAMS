document.addEventListener('DOMContentLoaded', function () {
  // modal elements
  const modal = document.getElementById('appointmentModal');
  const closeBtn = modal && modal.querySelector('.modal-close');
  const backBtn = document.getElementById('modalBackBtn');

  function openModal() { modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false'); }
  function closeModal() { modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true'); }

  // populate modal data
  function populateFromButton(btn) {
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-patient-name');
    const email = btn.getAttribute('data-email');
    const date = btn.getAttribute('data-date');
    const time = btn.getAttribute('data-time');
    const purpose = btn.getAttribute('data-purpose');
    const status = btn.getAttribute('data-status');
    const pid = btn.getAttribute('data-patient-id');

    document.getElementById('m_patient').textContent = name || '';
    document.getElementById('m_email').textContent = email || '';
    document.getElementById('m_date').textContent = date || '';
    document.getElementById('m_time').textContent = time || '';
    document.getElementById('m_purpose').textContent = purpose ? purpose.replace(/_/g, ' ') : '';
    document.getElementById('m_status').textContent = status || '';
    document.getElementById('m_pid').textContent = pid || '';

    // set hidden inputs for forms
    const a = document.getElementById('modal_app_id_accept');
    const d = document.getElementById('modal_app_id_decline');
    if (a) a.value = id;
    if (d) d.value = id;
  }

  // attach to all view buttons
  document.querySelectorAll('.view-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      populateFromButton(btn);
      openModal();
    });
  });

  // close actions
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (backBtn) backBtn.addEventListener('click', closeModal);

  // close on outside click
  modal.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
  });

  // close on Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
  });
});

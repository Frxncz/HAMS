document.addEventListener('DOMContentLoaded', function () {
  // modal elements
  const viewModal = document.getElementById('sessionViewModal');
  const reschedModal = document.getElementById('sessionReschedModal');
  const viewClose = viewModal && viewModal.querySelector('.modal-close');
  const reschedClose = reschedModal && reschedModal.querySelector('.modal-close');

  function openModal(modal) { modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false'); }
  function closeModal(modal) { modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true'); }

  // populate view modal
  function populateView(btn) {
    document.getElementById('sv_patient').textContent = btn.getAttribute('data-patient-name') || '';
    document.getElementById('sv_email').textContent = btn.getAttribute('data-email') || '';
    document.getElementById('sv_pid').textContent = btn.getAttribute('data-pid') || '';
    document.getElementById('sv_date').textContent = btn.getAttribute('data-date') || '';
    document.getElementById('sv_time').textContent = btn.getAttribute('data-time') || '';
    document.getElementById('sv_purpose').textContent = (btn.getAttribute('data-purpose') || '').replace(/_/g,' ');
  }

  // attach view buttons
  document.querySelectorAll('.btn-view').forEach(function (b) {
    b.addEventListener('click', function () {
      populateView(b);
      openModal(viewModal);
    });
  });

  if (viewClose) viewClose.addEventListener('click', function () { closeModal(viewModal); });
  if (viewModal) viewModal.addEventListener('click', function (e) { if (e.target === viewModal) closeModal(viewModal); });

  // reschedule handling: select only trigger buttons that have data-id to avoid catching the modal's Save button
  document.querySelectorAll('.btn-reschedule[data-id]').forEach(function (b) {
    b.addEventListener('click', function () {
      const id = b.getAttribute('data-id');
      const date = b.getAttribute('data-date');
      const time = b.getAttribute('data-time');
      document.getElementById('rs_app_id').value = id;
      document.getElementById('rs_date').value = date;
      document.getElementById('rs_time').value = time;
      openModal(reschedModal);
    });
  });

  if (reschedClose) reschedClose.addEventListener('click', function () { closeModal(reschedModal); });
  if (reschedModal) reschedModal.addEventListener('click', function (e) { if (e.target === reschedModal) closeModal(reschedModal); });

  // close on escape for both
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (viewModal && viewModal.classList.contains('open')) closeModal(viewModal);
      if (reschedModal && reschedModal.classList.contains('open')) closeModal(reschedModal);
    }
  });
});

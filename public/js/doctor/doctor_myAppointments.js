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

    // hide the inline accept/decline buttons for this card while modal is open to avoid visual duplication
    // store reference on modal so we can restore later
    const card = btn.closest('.appointment-card');
    if (card) {
      const inlineBtns = card.querySelectorAll('.accept-btn, .decline-btn');
      // save for restore
      modal._hiddenInline = inlineBtns;
      inlineBtns.forEach(b => b.classList.add('hidden-during-modal'));
    }
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
  
  // restore inline buttons when modal closes
  // observe modal class changes via mutation observer to detect close and restore
  const mo = new MutationObserver(function () {
    if (!modal.classList.contains('open') && modal._hiddenInline) {
      modal._hiddenInline.forEach(b => b.classList.remove('hidden-during-modal'));
      modal._hiddenInline = null;
    }
  });
  mo.observe(modal, { attributes: true, attributeFilter: ['class'] });
});

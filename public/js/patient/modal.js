/**
 * Modal Functionality
 * Handles the opening, closing, and interaction of the appointment modal
 */

(function() {
  'use strict';

  // Wait for DOM to be fully loaded
  document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const openBtn = document.getElementById('openScheduleBtn');
    const modal = document.getElementById('modalBackdrop');
    const cancelModal = document.getElementById('cancelModal');
    const scheduleForm = document.getElementById('scheduleForm');

    // Check if elements exist
    if (!openBtn || !modal || !cancelModal) {
      console.error('Modal elements not found');
      return;
    }

    /**
     * Open modal function
     */
    function openModal() {
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    /**
     * Close modal function
     */
    function closeModal() {
      modal.style.display = 'none';
      document.body.style.overflow = ''; // Restore scrolling
      
      // Optional: Reset form when closing
      if (scheduleForm) {
        scheduleForm.reset();
      }
    }

    /**
     * Event: Open modal when Schedule Now button is clicked
     */
    openBtn.addEventListener('click', function(e) {
      e.preventDefault();
      openModal();
    });

    /**
     * Event: Close modal when Cancel button is clicked
     */
    cancelModal.addEventListener('click', function(e) {
      e.preventDefault();
      closeModal();
    });

    /**
     * Event: Close modal when clicking outside (on backdrop)
     */
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        closeModal();
      }
    });

    /**
     * Event: Close modal with Escape key
     */
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        closeModal();
      }
    });

    /**
     * Optional: Form validation before submission
     */
    if (scheduleForm) {
      scheduleForm.addEventListener('submit', function(e) {
        const doctorId = document.getElementById('doctor_id').value;
        const apptDate = document.getElementById('appt_date').value;
        const apptTime = document.getElementById('appt_time').value;
        const purpose = document.getElementById('purpose').value;

        // Basic validation
        if (!doctorId || !apptDate || !apptTime || !purpose) {
          e.preventDefault();
          alert('Please fill in all fields');
          return false;
        }

        // Optional: Date validation (prevent past dates)
        const selectedDate = new Date(apptDate);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
          e.preventDefault();
          alert('Please select a future date');
          return false;
        }

        // Form is valid, allow submission
        return true;
      });
    }

    // Log that modal is ready
    console.log('Appointment modal initialized successfully');
  });

})();
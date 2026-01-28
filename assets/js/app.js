// Keep JS minimal for low-end PCs.
// Placeholder for small UX helpers (confirm deletes, etc.)

document.addEventListener('click', function (e) {
  const btn = e.target.closest('[data-confirm]');
  if (!btn) return;
  const msg = btn.getAttribute('data-confirm') || 'Are you sure?';
  if (!window.confirm(msg)) {
    e.preventDefault();
  }
});





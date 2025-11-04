document.addEventListener('DOMContentLoaded', () => {
  // lignes cliquables
  document.querySelectorAll('tr.row-click[data-href]').forEach(row => {
    row.addEventListener('click', e => {
      if (e.target.closest('a, button, input, select, form')) return;
      window.location.href = row.dataset.href;
    });
  });

  // rendre tous les <button class="btn"> d’un form delete => petits
  document.querySelectorAll('form[action*="delete"] .btn').forEach(btn => {
    btn.classList.add('btn-sm');
  });
});

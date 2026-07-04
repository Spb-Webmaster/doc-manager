@props(['tableSelector', 'route'])

{{--
    Переиспользуемый тулбар массового удаления для списков документов
    (счета, акты). Логика на чистом JS без внешних зависимостей —
    ищет чекбоксы с классом .bulk-select-all / .bulk-select-row внутри
    таблицы $tableSelector и отправляет выбранные id на $route.
--}}
<span class="bulk-toolbar" data-bulk-toolbar data-table="{{ $tableSelector }}" data-route="{{ $route }}">
    <span class="bulk-toolbar-count"><span class="bulk-count">0</span> выбрано</span>
    <button type="button" class="btn btn-sm btn-danger-sm bulk-delete-btn">Удалить выбранное</button>
</span>

@once
    @push('styles')
    <style>
      .bulk-toolbar { display: none; align-items: center; gap: 12px; }
      .bulk-toolbar.show { display: flex; }
      .bulk-toolbar-count { font-size: 12.5px; font-weight: 600; color: var(--red); }
    </style>
    @endpush

    @push('scripts')
    <script>
      function initBulkToolbars() {
        document.querySelectorAll('[data-bulk-toolbar]').forEach((toolbar) => {
          const table = document.querySelector(toolbar.dataset.table);
          if (!table) return;

          const endpoint  = toolbar.dataset.route;
          const countEl   = toolbar.querySelector('.bulk-count');
          const deleteBtn = toolbar.querySelector('.bulk-delete-btn');
          const selectAll = table.querySelector('.bulk-select-all');

          const rows = () => Array.from(table.querySelectorAll('.bulk-select-row'));
          const checkedRows = () => rows().filter((cb) => cb.checked);

          function refresh() {
            const checked = checkedRows();
            toolbar.classList.toggle('show', checked.length > 0);
            countEl.textContent = checked.length;
            if (selectAll) {
              const all = rows();
              selectAll.checked = all.length > 0 && checked.length === all.length;
              selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
            }
          }

          table.addEventListener('change', (e) => {
            if (e.target.classList.contains('bulk-select-row')) refresh();
          });

          if (selectAll) {
            selectAll.addEventListener('change', () => {
              rows().forEach((cb) => { cb.checked = selectAll.checked; });
              refresh();
            });
          }

          deleteBtn.addEventListener('click', async () => {
            const ids = checkedRows().map((cb) => cb.value);
            if (!ids.length) return;
            if (!confirm(`Удалить выбранные документы (${ids.length})? Это действие нельзя отменить.`)) return;

            try {
              const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': CSRF,
                  'Accept': 'application/json',
                },
                body: JSON.stringify({ ids }),
              });
              if (res.ok) {
                if (typeof showToast === 'function') showToast(`Удалено: ${ids.length}`);
                setTimeout(() => location.reload(), 600);
              } else if (typeof showToast === 'function') {
                showToast('Не удалось удалить выбранные документы');
              }
            } catch {
              if (typeof showToast === 'function') showToast('Ошибка соединения');
            }
          });
        });
      }

      document.addEventListener('DOMContentLoaded', initBulkToolbars);
    </script>
    @endpush
@endonce

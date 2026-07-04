window.makeSortable = function makeSortable({ getContainer, itemSel, handleSel, saveUrl, csrf, getId, onDragEnd }) {
    let dragSrc = null;

    function attach(item) {
        item.addEventListener('dragstart', function (e) {
            dragSrc = this;
            e.dataTransfer.effectAllowed = 'move';
            setTimeout(() => this.classList.add('dragging'), 0);
        });

        item.addEventListener('dragend', function () {
            this.classList.remove('dragging');
            this.draggable = false;
            getContainer().querySelectorAll(itemSel).forEach(el => el.classList.remove('drag-over'));
            if (onDragEnd) onDragEnd();
            save();
        });

        item.addEventListener('dragover', function (e) {
            e.preventDefault();
            if (this === dragSrc) return;
            this.classList.add('drag-over');
        });

        item.addEventListener('dragleave', function () {
            this.classList.remove('drag-over');
        });

        item.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            if (!dragSrc || this === dragSrc) return;
            const container = getContainer();
            const items = [...container.querySelectorAll(itemSel)];
            const srcIdx = items.indexOf(dragSrc);
            const tgtIdx = items.indexOf(this);
            if (srcIdx < tgtIdx) container.insertBefore(dragSrc, this.nextSibling);
            else                 container.insertBefore(dragSrc, this);
        });

        item.querySelector(handleSel).addEventListener('mousedown', function () {
            this.closest(itemSel).draggable = true;
        });
    }

    async function save() {
        const ids = [...getContainer().querySelectorAll(itemSel)].map(getId);
        try {
            await fetch(saveUrl, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body:    JSON.stringify({ ids }),
            });
        } catch {}
    }

    return { attach };
};

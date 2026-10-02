/**
 * Календарь кабинета — единственная реализация выбора даты на проекте.
 *
 * Разметку поля (иконка, поле ввода, скрытое ISO-значение и всплывающий календарь)
 * строит сам модуль, поэтому Blade-компонент <x-cabinet.date-picker> и формы,
 * собираемые на лету в JS, используют одну и ту же вёрстку.
 *
 * Разметка-зацепка:
 *   <div class="date-wrap" data-calendar
 *        data-value="2026-11-13"      — выбранная дата (Y-m-d), необязательно
 *        data-input-id="te-next"      — id видимого поля, необязательно
 *        data-name="next_run_at"      — name скрытого поля для отправки формы
 *        data-format="short|long"     — формат показа: 13.11.2026 или 13 ноября 2026 г.
 *        data-placeholder="…"></div>
 *
 * Инициализация: cabinetCalendarInit(scope) для статичной разметки,
 * window.cabinetCalendar.init(el) — для форм, отрисованных через innerHTML.
 */

const MONTHS = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
const DOW    = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

const ICON = '<svg class="date-ico" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">'
           + '<rect x="1.5" y="2.5" width="13" height="12" rx="2"/><path d="M5 1.5v2M11 1.5v2M1.5 6.5h13"/></svg>';

/** Дата → «13 ноября 2026 г.» */
export function formatDateLong(date) {
    return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
}

/** Дата → «13.11.2026» */
export function formatDateShort(date) {
    return date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

/** Дата → «2026-11-13» (локальная, без сдвига часового пояса, в отличие от toISOString) */
export function toIsoDate(date) {
    if (!date) {
        return '';
    }

    return date.getFullYear()
        + '-' + String(date.getMonth() + 1).padStart(2, '0')
        + '-' + String(date.getDate()).padStart(2, '0');
}

/** «2026-11-13» → Date (локальная полночь) или null */
export function parseIsoDate(value) {
    const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value ?? '').trim());

    return m ? new Date(+m[1], +m[2] - 1, +m[3]) : null;
}

function sameDay(a, b) {
    return !!a && !!b && a.toDateString() === b.toDateString();
}

/** Все открытые календари страницы — чтобы один обработчик закрывал любой из них */
const instances = new Set();
let globalBound = false;

function bindGlobalHandlers() {
    if (globalBound) {
        return;
    }
    globalBound = true;

    document.addEventListener('click', (e) => {
        instances.forEach((cal) => {
            if (!cal.wrap.contains(e.target)) {
                cal.close();
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            instances.forEach((cal) => cal.close());
        }
    });

    // Поле может лежать в прокручиваемой модалке — держим попап у поля
    window.addEventListener('scroll', () => instances.forEach((c) => c.reposition()), true);
    window.addEventListener('resize', () => instances.forEach((c) => c.reposition()));
}

class CabinetCalendar {
    constructor(wrap, options = {}) {
        this.wrap     = wrap;
        this.format   = options.format   || wrap.dataset.format || 'long';
        this.onSelect = options.onSelect || null;

        const initial = options.value !== undefined
            ? (options.value instanceof Date ? options.value : parseIsoDate(options.value))
            : parseIsoDate(wrap.dataset.value);

        this.selected = initial;
        this.viewing  = new Date(initial || new Date());

        this.build();
        this.renderInput();
        this.renderGrid();

        instances.add(this);
        bindGlobalHandlers();
        wrap.__cabinetCalendar = this;
    }

    /** Строит разметку поля внутри .date-wrap (вызывается один раз) */
    build() {
        const wrap = this.wrap;
        wrap.classList.add('date-wrap');
        wrap.innerHTML = '';

        wrap.insertAdjacentHTML('beforeend', ICON);

        this.input = document.createElement('input');
        this.input.type      = 'text';
        this.input.readOnly  = true;
        this.input.className = 'date-input';
        if (wrap.dataset.inputId)     this.input.id          = wrap.dataset.inputId;
        if (wrap.dataset.placeholder) this.input.placeholder = wrap.dataset.placeholder;
        wrap.appendChild(this.input);

        this.hidden = document.createElement('input');
        this.hidden.type = 'hidden';
        this.hidden.setAttribute('data-calendar-value', '');
        if (wrap.dataset.name) this.hidden.name = wrap.dataset.name;
        wrap.appendChild(this.hidden);

        this.pop = document.createElement('div');
        this.pop.className = 'cal-pop';
        this.pop.innerHTML = '<div class="cal-header">'
            + '<button type="button" class="cal-nav" data-cal-prev>‹</button>'
            + '<span class="cal-month-lbl"></span>'
            + '<button type="button" class="cal-nav" data-cal-next>›</button>'
            + '</div><div class="cal-grid"></div>';
        wrap.appendChild(this.pop);

        this.label = this.pop.querySelector('.cal-month-lbl');
        this.grid  = this.pop.querySelector('.cal-grid');

        this.input.addEventListener('click', (e) => { e.stopPropagation(); this.toggle(); });
        this.pop.addEventListener('click', (e) => e.stopPropagation());
        this.pop.querySelector('[data-cal-prev]').addEventListener('click', () => this.shiftMonth(-1));
        this.pop.querySelector('[data-cal-next]').addEventListener('click', () => this.shiftMonth(1));
        this.grid.addEventListener('click', (e) => {
            const day = e.target.closest('.cal-day:not(.empty)');
            if (day) {
                this.setDate(new Date(+day.dataset.y, +day.dataset.m, +day.dataset.d));
                this.close();
            }
        });
    }

    renderInput() {
        const fmt = this.format === 'short' ? formatDateShort : formatDateLong;
        this.input.value  = this.selected ? fmt(this.selected) : '';
        this.hidden.value = toIsoDate(this.selected);
    }

    renderGrid() {
        const y = this.viewing.getFullYear();
        const m = this.viewing.getMonth();
        const today = new Date();

        this.label.textContent = MONTHS[m] + ' ' + y;

        let html = DOW.map((d) => `<div class="cal-dow">${d}</div>`).join('');

        let lead = new Date(y, m, 1).getDay();
        lead = (lead === 0 ? 7 : lead) - 1;
        html += '<div class="cal-day empty"></div>'.repeat(lead);

        const total = new Date(y, m + 1, 0).getDate();
        for (let d = 1; d <= total; d++) {
            const date = new Date(y, m, d);
            const cls  = ['cal-day'];
            if (sameDay(date, this.selected)) cls.push('selected');
            else if (sameDay(date, today))    cls.push('today');
            html += `<div class="${cls.join(' ')}" data-y="${y}" data-m="${m}" data-d="${d}">${d}</div>`;
        }

        this.grid.innerHTML = html;
    }

    shiftMonth(delta) {
        this.viewing = new Date(this.viewing.getFullYear(), this.viewing.getMonth() + delta, 1);
        this.renderGrid();
    }

    /** Программная установка даты; silent — не дёргать onSelect */
    setDate(date, { silent = false } = {}) {
        this.selected = date instanceof Date ? date : parseIsoDate(date);
        if (this.selected) {
            this.viewing = new Date(this.selected);
        }
        this.renderInput();
        this.renderGrid();

        if (!silent && this.onSelect) {
            this.onSelect(this.selected, this);
        }
    }

    getDate() {
        return this.selected;
    }

    /** Выбранная дата как «2026-11-13» либо пустая строка */
    getIso() {
        return toIsoDate(this.selected);
    }

    /**
     * Попап позиционируется фиксированно: поле может лежать в прокручиваемой
     * колонке или модалке, и абсолютный попап там обрезался бы по краю контейнера.
     */
    reposition() {
        if (!this.pop.classList.contains('open')) {
            return;
        }

        const rect  = this.input.getBoundingClientRect();
        const popH  = this.pop.offsetHeight;
        const below = window.innerHeight - rect.bottom;
        const above = rect.top;

        this.pop.style.left = Math.max(8, Math.min(rect.left, window.innerWidth - this.pop.offsetWidth - 8)) + 'px';
        this.pop.style.top  = (below < popH + 12 && above > below)
            ? Math.max(8, rect.top - popH - 6) + 'px'
            : (rect.bottom + 6) + 'px';
    }

    open() {
        instances.forEach((cal) => { if (cal !== this) cal.close(); });
        this.renderGrid();
        this.pop.classList.add('open');
        this.input.classList.add('open');
        this.reposition();
    }

    close() {
        this.pop.classList.remove('open');
        this.input.classList.remove('open');
    }

    toggle() {
        this.pop.classList.contains('open') ? this.close() : this.open();
    }

    destroy() {
        instances.delete(this);
        delete this.wrap.__cabinetCalendar;
    }
}

/** Создаёт календарь на конкретном .date-wrap (повторный вызов вернёт уже созданный) */
export function createCabinetCalendar(wrap, options = {}) {
    if (!wrap) {
        return null;
    }

    return wrap.__cabinetCalendar || new CabinetCalendar(wrap, options);
}

/**
 * Инициализирует все [data-calendar] внутри scope.
 * @returns {CabinetCalendar[]} созданные (и ранее созданные) экземпляры
 */
export function cabinetCalendarInit(scope = document, options = {}) {
    // Явно переданный пустой scope (не нашли элемент) не должен превращаться
    // в document — иначе вызывающий код получил бы чужой календарь страницы
    if (arguments.length > 0 && !scope) {
        return [];
    }

    const root  = scope || document;
    const nodes = [...root.querySelectorAll('[data-calendar]')];

    if (root.matches && root.matches('[data-calendar]')) {
        nodes.unshift(root);
    }

    return nodes.map((el) => createCabinetCalendar(el, options));
}

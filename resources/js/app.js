import './bootstrap';

const html = document.documentElement;
const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
const arabicDigits = '٠١٢٣٤٥٦٧٨٩';

const toEnglishDigits = (value) => String(value ?? '')
    .replace(/[۰-۹]/g, (digit) => String(persianDigits.indexOf(digit)))
    .replace(/[٠-٩]/g, (digit) => String(arabicDigits.indexOf(digit)));

const faDigits = (value) => String(value ?? '').replace(/\d/g, (digit) => persianDigits[Number(digit)]);
const pad = (value) => String(value).padStart(2, '0');

const preferredTheme = () => localStorage.getItem('theme') || (systemTheme.matches ? 'dark' : 'light');

const applyTheme = (theme, persist = false) => {
    const isDark = theme === 'dark';
    html.classList.toggle('dark', isDark);
    html.style.colorScheme = isDark ? 'dark' : 'light';

    if (persist) localStorage.setItem('theme', theme);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const label = isDark ? 'فعال‌کردن حالت روشن' : 'فعال‌کردن حالت تاریک';
        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);
        button.setAttribute('aria-pressed', String(isDark));
    });
};

applyTheme(preferredTheme());

systemTheme.addEventListener?.('change', (event) => {
    if (!localStorage.getItem('theme')) applyTheme(event.matches ? 'dark' : 'light');
});

const showToast = (message, type = 'default', duration = 3200) => {
    if (!message) return;

    let region = document.querySelector('[data-toast-region]');
    if (!region) {
        region = document.createElement('div');
        region.className = 'app-toast-region';
        region.dataset.toastRegion = '';
        region.setAttribute('aria-live', 'polite');
        region.setAttribute('aria-atomic', 'true');
        document.body.appendChild(region);
    }

    const toast = document.createElement('div');
    toast.className = `app-toast ${type === 'success' ? 'is-success' : ''} ${type === 'error' ? 'is-error' : ''}`;
    toast.setAttribute('role', 'status');
    toast.innerHTML = `<span class="min-w-0 flex-1">${String(message).replace(/[&<>"']/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
    })[character])}</span>`;
    region.appendChild(toast);

    window.setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(8px)';
        window.setTimeout(() => toast.remove(), 180);
    }, duration);
};

const gregorianToJalali = (gy, gm, gd) => {
    const monthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    let gDayNo = 365 * (gy - 1600) + Math.floor((gy - 1597) / 4)
        - Math.floor((gy - 1501) / 100) + Math.floor((gy - 1201) / 400);

    for (let i = 0; i < gm - 1; i += 1) gDayNo += monthDays[i];
    const leap = (gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0;
    if (gm > 2 && leap) gDayNo += 1;
    gDayNo += gd - 1;

    let jDayNo = gDayNo - 79;
    const jNp = Math.floor(jDayNo / 12053);
    jDayNo %= 12053;
    let jy = 979 + 33 * jNp + 4 * Math.floor(jDayNo / 1461);
    jDayNo %= 1461;

    if (jDayNo >= 366) {
        jy += Math.floor((jDayNo - 1) / 365);
        jDayNo = (jDayNo - 1) % 365;
    }

    const jMonthDays = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
    let jm = 0;
    while (jm < 11 && jDayNo >= jMonthDays[jm]) {
        jDayNo -= jMonthDays[jm];
        jm += 1;
    }

    return [jy, jm + 1, jDayNo + 1];
};

const jalaliToGregorian = (jy, jm, jd) => {
    const year = jy - 979;
    let jDayNo = 365 * year + Math.floor(year / 33) * 8 + Math.floor(((year % 33) + 3) / 4);
    for (let i = 0; i < jm - 1; i += 1) jDayNo += i < 6 ? 31 : 30;
    jDayNo += jd - 1;

    let gDayNo = jDayNo + 79;
    let gy = 1600 + 400 * Math.floor(gDayNo / 146097);
    gDayNo %= 146097;
    let leap = true;

    if (gDayNo >= 36525) {
        gDayNo -= 1;
        gy += 100 * Math.floor(gDayNo / 36524);
        gDayNo %= 36524;
        if (gDayNo >= 365) gDayNo += 1;
        else leap = false;
    }

    gy += 4 * Math.floor(gDayNo / 1461);
    gDayNo %= 1461;

    if (gDayNo >= 366) {
        leap = false;
        gDayNo -= 1;
        gy += Math.floor(gDayNo / 365);
        gDayNo %= 365;
    }

    const monthDays = [31, leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    let gm = 0;
    while (gDayNo >= monthDays[gm]) {
        gDayNo -= monthDays[gm];
        gm += 1;
    }

    return [gy, gm + 1, gDayNo + 1];
};

const persianMonthNames = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
const persianWeekdays = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
const fullWeekdays = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'];

const jalaliMonthLength = (jy, jm) => {
    const [gy, gm, gd] = jalaliToGregorian(jy, jm, 1);
    const next = jm === 12 ? jalaliToGregorian(jy + 1, 1, 1) : jalaliToGregorian(jy, jm + 1, 1);
    return Math.round((Date.UTC(next[0], next[1] - 1, next[2]) - Date.UTC(gy, gm - 1, gd)) / 86400000);
};

const dateKey = (gy, gm, gd) => `${gy}-${pad(gm)}-${pad(gd)}`;

const dateLabel = (gy, gm, gd) => {
    const [jy, jm, jd] = gregorianToJalali(gy, gm, gd);
    const weekday = fullWeekdays[new Date(Date.UTC(gy, gm - 1, gd)).getUTCDay()];
    return `${weekday}، ${faDigits(jd)} ${persianMonthNames[jm - 1]} ${faDigits(jy)}`;
};

const initDigitNormalization = () => {
    const selector = [
        'input[type="tel"]',
        'input[inputmode="tel"]',
        'input[type="number"]',
        'input[inputmode="numeric"]',
        'input[inputmode="decimal"]',
        '[data-normalize-digits]',
    ].join(',');

    const normalize = (input, compact = false) => {
        const cursor = input.selectionStart;
        let value = toEnglishDigits(input.value);
        if (compact) value = value.replace(/[\s\-()]/g, '');
        if (value !== input.value) {
            input.value = value;
            if (cursor !== null && input.setSelectionRange) input.setSelectionRange(cursor, cursor);
        }
    };

    document.querySelectorAll(selector).forEach((input) => {
        input.addEventListener('input', () => normalize(input));
        input.addEventListener('blur', () => normalize(input, input.matches('input[type="tel"]')));
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            form.querySelectorAll(selector).forEach((input) => normalize(input, input.matches('input[type="tel"]')));
        });
    });
};

const initPersianDatePicker = () => {
    const fields = [...document.querySelectorAll('[data-persian-date-field]')];
    if (!fields.length) return;

    let activeField = null;
    let activeTrigger = null;
    let visibleYear;
    let visibleMonth;

    const modal = document.createElement('div');
    modal.className = 'persian-calendar-modal hidden';
    modal.innerHTML = `
        <div class="persian-calendar-backdrop" data-calendar-close></div>
        <div class="persian-calendar-dialog" role="dialog" aria-modal="true" aria-labelledby="persian-calendar-title" tabindex="-1">
            <div class="flex items-center justify-between border-b border-gray-100 p-4 dark:border-gray-700">
                <button type="button" class="calendar-nav" data-calendar-next aria-label="ماه بعد">‹</button>
                <div class="text-center">
                    <div id="persian-calendar-title" class="font-extrabold text-gray-900 dark:text-white" data-calendar-title></div>
                    <div class="mt-1 text-xs text-gray-500">تقویم هجری شمسی</div>
                </div>
                <button type="button" class="calendar-nav" data-calendar-prev aria-label="ماه قبل">›</button>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-7 gap-1 text-center text-xs font-extrabold text-gray-400" data-calendar-weekdays></div>
                <div class="mt-2 grid grid-cols-7 gap-1" data-calendar-days></div>
            </div>
            <div class="flex items-center justify-between border-t border-gray-100 p-3 dark:border-gray-700">
                <button type="button" class="rounded-lg px-2 py-1 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" data-calendar-clear>پاک‌کردن</button>
                <button type="button" class="rounded-lg px-2 py-1 text-sm font-bold text-primary hover:bg-primary-50 dark:hover:bg-primary-950/30" data-calendar-today>امروز</button>
            </div>
        </div>`;
    document.body.appendChild(modal);

    const dialog = modal.querySelector('[role="dialog"]');
    modal.querySelector('[data-calendar-weekdays]').innerHTML = persianWeekdays
        .map((day) => `<span class="py-2" aria-hidden="true">${day}</span>`)
        .join('');

    const close = () => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        activeTrigger?.setAttribute('aria-expanded', 'false');
        activeField = null;
        activeTrigger?.focus();
        activeTrigger = null;
    };

    const withinRange = (key) => {
        if (!activeField) return true;
        const hidden = activeField.querySelector('[data-persian-date-value]');
        return (!hidden.min || key >= hidden.min) && (!hidden.max || key <= hidden.max);
    };

    const setValue = (gy, gm, gd) => {
        if (!activeField) return;
        const key = dateKey(gy, gm, gd);
        if (!withinRange(key)) return;

        const hidden = activeField.querySelector('[data-persian-date-value]');
        const label = activeField.querySelector('[data-persian-date-label]');
        hidden.value = key;
        label.textContent = dateLabel(gy, gm, gd);
        activeField.querySelector('[data-persian-date-trigger]')?.classList.remove('text-gray-400');
        hidden.dispatchEvent(new Event('input', { bubbles: true }));
        hidden.dispatchEvent(new Event('change', { bubbles: true }));
        close();
    };

    const render = () => {
        modal.querySelector('[data-calendar-title]').textContent = `${persianMonthNames[visibleMonth - 1]} ${faDigits(visibleYear)}`;
        const daysNode = modal.querySelector('[data-calendar-days]');
        const [firstGy, firstGm, firstGd] = jalaliToGregorian(visibleYear, visibleMonth, 1);
        const firstDayIndex = (new Date(Date.UTC(firstGy, firstGm - 1, firstGd)).getUTCDay() + 1) % 7;
        const monthLength = jalaliMonthLength(visibleYear, visibleMonth);
        const selectedValue = activeField?.querySelector('[data-persian-date-value]')?.value;
        const today = new Date();
        const todayKey = dateKey(today.getFullYear(), today.getMonth() + 1, today.getDate());
        let output = '<span aria-hidden="true"></span>'.repeat(firstDayIndex);

        for (let day = 1; day <= monthLength; day += 1) {
            const [gy, gm, gd] = jalaliToGregorian(visibleYear, visibleMonth, day);
            const key = dateKey(gy, gm, gd);
            const selected = key === selectedValue;
            const isToday = key === todayKey;
            const disabled = !withinRange(key);
            output += `<button type="button"
                class="calendar-day ${selected ? 'is-selected' : ''} ${isToday ? 'is-today' : ''}"
                data-calendar-day="${day}"
                aria-label="${dateLabel(gy, gm, gd)}"
                aria-selected="${selected}"
                ${disabled ? 'disabled' : ''}>${faDigits(day)}</button>`;
        }

        daysNode.innerHTML = output;
        daysNode.querySelectorAll('[data-calendar-day]:not(:disabled)').forEach((button) => {
            button.addEventListener('click', () => {
                const [gy, gm, gd] = jalaliToGregorian(visibleYear, visibleMonth, Number(button.dataset.calendarDay));
                setValue(gy, gm, gd);
            });
        });
    };

    fields.forEach((field) => {
        const trigger = field.querySelector('[data-persian-date-trigger]');
        trigger?.addEventListener('click', () => {
            activeField = field;
            activeTrigger = trigger;
            activeTrigger.setAttribute('aria-expanded', 'true');
            const value = field.querySelector('[data-persian-date-value]').value;
            const today = new Date();
            let gy = today.getFullYear();
            let gm = today.getMonth() + 1;
            let gd = today.getDate();

            if (/^\d{4}-\d{2}-\d{2}$/.test(value)) [gy, gm, gd] = value.split('-').map(Number);
            [visibleYear, visibleMonth] = gregorianToJalali(gy, gm, gd);

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            render();
            window.requestAnimationFrame(() => dialog.focus());
        });
    });

    modal.querySelectorAll('[data-calendar-close]').forEach((button) => button.addEventListener('click', close));
    modal.querySelector('[data-calendar-prev]').addEventListener('click', () => {
        visibleMonth -= 1;
        if (visibleMonth < 1) {
            visibleMonth = 12;
            visibleYear -= 1;
        }
        render();
    });
    modal.querySelector('[data-calendar-next]').addEventListener('click', () => {
        visibleMonth += 1;
        if (visibleMonth > 12) {
            visibleMonth = 1;
            visibleYear += 1;
        }
        render();
    });
    modal.querySelector('[data-calendar-today]').addEventListener('click', () => {
        const today = new Date();
        setValue(today.getFullYear(), today.getMonth() + 1, today.getDate());
    });
    modal.querySelector('[data-calendar-clear]').addEventListener('click', () => {
        if (!activeField) return;
        const hidden = activeField.querySelector('[data-persian-date-value]');
        if (hidden.required) {
            showToast('این تاریخ الزامی است و نمی‌توان آن را پاک کرد.', 'error');
            return;
        }
        hidden.value = '';
        activeField.querySelector('[data-persian-date-label]').textContent = activeField.dataset.placeholder || 'انتخاب تاریخ';
        activeField.querySelector('[data-persian-date-trigger]')?.classList.add('text-gray-400');
        hidden.dispatchEvent(new Event('change', { bubbles: true }));
        close();
    });

    document.addEventListener('keydown', (event) => {
        if (modal.classList.contains('hidden')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft') modal.querySelector('[data-calendar-next]').click();
        if (event.key === 'ArrowRight') modal.querySelector('[data-calendar-prev]').click();
    });
};

const initWeeklySchedule = () => {
    const dayCards = [...document.querySelectorAll('[data-weekly-day]')];
    if (!dayCards.length) return;

    const timeToMinutes = (time) => {
        const parts = toEnglishDigits(time).split(':').map(Number);
        return parts.length === 2 && parts.every(Number.isFinite) ? parts[0] * 60 + parts[1] : null;
    };
    const minutesToTime = (minutes) => `${pad(Math.floor(minutes / 60))}:${pad(minutes % 60)}`;

    const currentCapacities = (card) => {
        const result = {};
        card.querySelectorAll('[data-slot-capacity]').forEach((input) => {
            result[input.dataset.slotTime] = Number(toEnglishDigits(input.value) || 1);
        });
        return result;
    };

    const setDayState = (card, enabled) => {
        const state = card.querySelector('[data-day-state]');
        if (state) state.textContent = enabled ? 'روز کاری' : 'تعطیل';
        card.classList.toggle('ring-1', enabled);
        card.classList.toggle('ring-primary/10', enabled);
    };

    const renderDay = (card, forcedCapacities = null) => {
        const enabled = card.querySelector('[data-day-enabled]').checked;
        const controls = card.querySelectorAll('[data-day-control]');
        const content = card.querySelector('[data-day-content]');
        const slotsNode = card.querySelector('[data-day-slots]');

        controls.forEach((control) => { control.disabled = !enabled; });
        content?.classList.toggle('opacity-50', !enabled);
        setDayState(card, enabled);

        if (!enabled) {
            slotsNode.innerHTML = '<div class="col-span-full rounded-xl border border-dashed border-gray-200 px-4 py-5 text-center text-sm text-gray-400 dark:border-gray-700">این روز تعطیل است.</div>';
            return;
        }

        const weekday = card.dataset.weekday;
        const start = timeToMinutes(card.querySelector('[data-day-start]').value);
        const end = timeToMinutes(card.querySelector('[data-day-end]').value);
        const duration = Number(toEnglishDigits(card.querySelector('[data-day-duration]').value));
        const defaultCapacity = Math.max(1, Number(toEnglishDigits(card.querySelector('[data-day-capacity]').value) || 1));
        const previous = forcedCapacities || currentCapacities(card);

        if (start === null || end === null || end <= start || !duration) {
            slotsNode.innerHTML = '<div class="col-span-full rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-300">ساعت شروع و پایان را درست وارد کنید.</div>';
            return;
        }

        if ((end - start) % duration !== 0) {
            slotsNode.innerHTML = '<div class="col-span-full rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-300">فاصله شروع تا پایان باید دقیقاً بر مدت هر اسلات بخش‌پذیر باشد.</div>';
            return;
        }

        const rows = [];
        for (let cursor = start; cursor + duration <= end; cursor += duration) {
            const startTime = minutesToTime(cursor);
            const endTime = minutesToTime(cursor + duration);
            const capacity = Math.max(1, Math.min(100, Number(previous[startTime] ?? defaultCapacity)));
            rows.push(`
                <label class="slot-capacity-card">
                    <span class="slot-time" dir="ltr">${faDigits(startTime)} تا ${faDigits(endTime)}</span>
                    <span class="text-center text-xs text-gray-500">ظرفیت خودرو</span>
                    <span class="flex items-center gap-2">
                        <button type="button" class="slot-step" data-capacity-minus aria-label="کاهش ظرفیت اسلات ${faDigits(startTime)}">−</button>
                        <input type="number" inputmode="numeric" min="1" max="100" value="${capacity}"
                            name="days[${weekday}][slot_capacities][${startTime}]"
                            class="slot-capacity-input" data-slot-capacity data-slot-time="${startTime}"
                            aria-label="ظرفیت اسلات ${faDigits(startTime)}">
                        <button type="button" class="slot-step" data-capacity-plus aria-label="افزایش ظرفیت اسلات ${faDigits(startTime)}">+</button>
                    </span>
                </label>`);
        }
        slotsNode.innerHTML = rows.join('');

        slotsNode.querySelectorAll('[data-capacity-minus], [data-capacity-plus]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = button.parentElement.querySelector('[data-slot-capacity]');
                const change = button.hasAttribute('data-capacity-plus') ? 1 : -1;
                input.value = Math.max(1, Math.min(100, Number(toEnglishDigits(input.value) || 1) + change));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    };

    dayCards.forEach((card) => {
        let initial = {};
        try {
            initial = JSON.parse(card.dataset.initialCapacities || '{}');
        } catch (_) {
            initial = {};
        }

        card.querySelectorAll('[data-day-enabled], [data-day-start], [data-day-end], [data-day-duration], [data-day-capacity]').forEach((input) => {
            input.addEventListener('change', () => renderDay(card));
            input.addEventListener('input', () => {
                if (!input.matches('[data-day-capacity]')) renderDay(card);
            });
        });

        card.querySelector('[data-apply-default]')?.addEventListener('click', () => {
            const capacity = Math.max(1, Number(toEnglishDigits(card.querySelector('[data-day-capacity]').value) || 1));
            card.querySelectorAll('[data-slot-capacity]').forEach((input) => { input.value = capacity; });
            showToast('ظرفیت پیش‌فرض روی همه اسلات‌های این روز اعمال شد.', 'success');
        });

        card.querySelector('[data-copy-day]')?.addEventListener('click', () => {
            const enabled = card.querySelector('[data-day-enabled]').checked;
            const capacities = currentCapacities(card);
            dayCards.forEach((target) => {
                if (target === card) return;
                target.querySelector('[data-day-enabled]').checked = enabled;
                target.querySelector('[data-day-start]').value = card.querySelector('[data-day-start]').value;
                target.querySelector('[data-day-end]').value = card.querySelector('[data-day-end]').value;
                target.querySelector('[data-day-duration]').value = card.querySelector('[data-day-duration]').value;
                target.querySelector('[data-day-capacity]').value = card.querySelector('[data-day-capacity]').value;
                renderDay(target, capacities);
            });
            showToast('برنامه این روز برای سایر روزهای هفته کپی شد.', 'success');
        });

        renderDay(card, initial);
    });
};

const initSlotPicker = () => {
    const select = document.querySelector('[data-booking-slot-select]');
    const cards = [...document.querySelectorAll('[data-booking-slot-card]')];
    if (!select || !cards.length) return;

    const selectCard = (card) => {
        if (card.getAttribute('aria-disabled') === 'true') return;
        select.value = card.dataset.slotId;
        cards.forEach((item) => {
            const active = item === card;
            item.classList.toggle('is-selected', active);
            item.setAttribute('aria-selected', String(active));
        });
        select.dispatchEvent(new Event('change', { bubbles: true }));
    };

    cards.forEach((card) => {
        card.setAttribute('role', 'option');
        card.setAttribute('tabindex', '0');
        card.setAttribute('aria-selected', String(card.classList.contains('is-selected')));
        card.addEventListener('click', () => selectCard(card));
        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                selectCard(card);
            }
        });
    });
};

const initSidebar = () => {
    const sidebar = document.querySelector('[data-panel-sidebar]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    if (!sidebar || !overlay) return;

    const setSidebar = (open) => {
        sidebar.classList.toggle('translate-x-full', !open);
        sidebar.classList.toggle('translate-x-0', open);
        overlay.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
        sidebar.setAttribute('aria-hidden', String(!open && window.innerWidth < 1024));
        document.querySelectorAll('[data-sidebar-open]').forEach((button) => button.setAttribute('aria-expanded', String(open)));
    };

    document.querySelectorAll('[data-sidebar-open]').forEach((button) => {
        button.setAttribute('aria-controls', 'panel-sidebar');
        button.setAttribute('aria-expanded', 'false');
        button.addEventListener('click', () => setSidebar(true));
    });
    document.querySelectorAll('[data-sidebar-close], [data-sidebar-overlay]').forEach((button) => button.addEventListener('click', () => setSidebar(false)));
    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
        if (window.innerWidth < 1024) setSidebar(false);
    }));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.body.classList.remove('overflow-hidden');
            overlay.classList.add('hidden');
            sidebar.setAttribute('aria-hidden', 'false');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !overlay.classList.contains('hidden')) setSidebar(false);
    });
};

const initDropdowns = () => {
    const closeAll = (except = null) => {
        document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
            if (menu !== except) {
                menu.classList.add('hidden');
                document.querySelector(`[data-dropdown-toggle="${menu.id}"]`)?.setAttribute('aria-expanded', 'false');
            }
        });
    };

    document.querySelectorAll('[data-dropdown-toggle]').forEach((button) => {
        button.setAttribute('aria-haspopup', 'menu');
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const menu = document.getElementById(button.dataset.dropdownToggle);
            if (!menu) return;
            const willOpen = menu.classList.contains('hidden');
            closeAll(menu);
            menu.classList.toggle('hidden', !willOpen);
            button.setAttribute('aria-expanded', String(willOpen));
        });
    });

    document.addEventListener('click', () => closeAll());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAll();
    });
};

const initPasswordToggles = () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const input = document.getElementById(button.dataset.passwordToggle);
        if (!input) return;

        const update = () => {
            const visible = input.type === 'text';
            button.setAttribute('aria-label', visible ? 'پنهان‌کردن رمز عبور' : 'نمایش رمز عبور');
            button.setAttribute('title', visible ? 'پنهان‌کردن رمز عبور' : 'نمایش رمز عبور');
            button.setAttribute('aria-pressed', String(visible));
        };

        update();
        button.addEventListener('click', () => {
            input.type = input.type === 'password' ? 'text' : 'password';
            update();
            input.focus({ preventScroll: true });
        });
    });
};

const initAuthTabs = () => {
    const tabs = [...document.querySelectorAll('[data-auth-tab]')];
    if (!tabs.length) return;

    const activate = (tab) => {
        tabs.forEach((button) => {
            const active = button.dataset.authTab === tab;
            button.classList.toggle('bg-primary', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('text-gray-600', !active);
            button.classList.toggle('dark:text-gray-300', !active);
            button.setAttribute('aria-selected', String(active));
            button.setAttribute('tabindex', active ? '0' : '-1');
        });
        document.querySelectorAll('[data-auth-panel]').forEach((panel) => {
            const active = panel.dataset.authPanel === tab;
            panel.classList.toggle('hidden', !active);
            panel.setAttribute('aria-hidden', String(!active));
        });
    };

    tabs.forEach((button) => {
        button.setAttribute('role', 'tab');
        button.addEventListener('click', () => activate(button.dataset.authTab));
    });
    document.querySelector('[data-auth-tabs]')?.setAttribute('role', 'tablist');
    activate(document.querySelector('[data-auth-tabs]')?.dataset.defaultTab || tabs[0].dataset.authTab);
};

const initForms = () => {
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented || !form.checkValidity()) return;
            const submitter = event.submitter;
            if (!submitter || submitter.dataset.noLoading !== undefined) return;

            submitter.dataset.originalText = submitter.innerHTML;
            submitter.style.minWidth = `${submitter.getBoundingClientRect().width}px`;
            submitter.setAttribute('aria-busy', 'true');
            submitter.disabled = true;
            submitter.innerHTML = '<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-l-transparent" aria-hidden="true"></span><span>در حال ثبت...</span>';
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    applyTheme(preferredTheme());

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => applyTheme(html.classList.contains('dark') ? 'light' : 'dark', true));
    });

    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            if (!window.confirm(element.dataset.confirm || 'آیا از انجام این عملیات مطمئن هستید؟')) event.preventDefault();
        });
    });

    initDigitNormalization();
    initSidebar();
    initDropdowns();
    initPasswordToggles();
    initAuthTabs();
    initPersianDatePicker();
    initWeeklySchedule();
    initSlotPicker();
    initForms();
});

import './bootstrap';
// IMask to add input masks support
import IMask from 'imask';
window.IMask = IMask;




// import styles bundle
import 'swiper/css/bundle';


import './script';
import './include/fancybox/fancybox';
import './include/form_async/async';
import {cabinetMessageDeleteInit} from './include/fancybox/cabinet_message';
cabinetMessageDeleteInit();

import { mzSelect } from './include/select/mz-select';
mzSelect();

import { cabinetSidebarInit } from './include/cabinet/sidebar';
cabinetSidebarInit();

import {
    cabinetCalendarInit,
    createCabinetCalendar,
    formatDateLong,
    formatDateShort,
    toIsoDate,
    parseIsoDate,
} from './include/cabinet/calendar';

// Доступен из инлайновых скриптов страниц, которые собирают формы через innerHTML
window.cabinetCalendar = {
    init: cabinetCalendarInit,
    create: createCabinetCalendar,
    formatLong: formatDateLong,
    formatShort: formatDateShort,
    toIso: toIsoDate,
    parseIso: parseIsoDate,
};

cabinetCalendarInit();


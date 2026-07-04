import { imask } from './include/imask';
import { close_flash } from './include/flash';
/*import {tooltip} from './include/tooltip';*/

// import {yandex_map_object} from "./include/site/yandex_map";

// import {swiper} from "./include/site/swiper";
// import {mobileMenuComponent} from "./include/site/mobile/mobile-menu-component";
// import {removeErrors} from "./include/fancybox/form/removeErrors";
import {flash_message} from "./include/flash_message/flash_message";

// import {datepicker_accountant_ticket_date, datepicker_date_birthday} from "./include/datepicker/datepicker";
// import {trix} from "./include/editor/trix";
// import {faqAccordion} from "./include/site/faq";
// import {citySelector} from "./include/site/city-selector";



document.addEventListener('DOMContentLoaded', function () {
    imask()
    close_flash() // закрытие flash
   /* tooltip() // tooltip */
    // yandex_map_object('43db27ba-be61-4e84-b139-ff37ad4802b8') // карта в объект
    // swiper()
   // mobileMenuComponent() // мобильное меню
    // removeErrors() // убрать ошибки с input`s
    flash_message() // закрытие модального окна
   // datepicker_date_birthday() // календарик день рождения
   // datepicker_accountant_ticket_date() // календарик (Дата выдачи сертификата профессионального бухгалтера)
    // trix() //редактор
    // faqAccordion() // FAQ аккордеон
    // citySelector() // выбор города
});

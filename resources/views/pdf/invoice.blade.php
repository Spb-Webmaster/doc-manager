<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<style>
/* DejaVu Sans — единственный шрифт DomPDF с полной поддержкой кириллицы */
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 9pt;
    color: #000;
    line-height: 1.4;
    margin: 10mm 8mm 10mm 12mm;
}
.page-frame {
    padding: 4mm;
}

/* ── Блок банковских реквизитов вверху страницы ── */
.bank-block {
    border: 1px solid #000;
    margin-bottom: 2mm;
}
.bank-table {
    width: 100%;
    border-collapse: collapse;
}
.bank-table td {
    padding: 2mm 3mm;
    vertical-align: top;
    font-size: 8.5pt;
}
/* Вертикальный разделитель внутри блока реквизитов */
.bank-table .divider {
    border-left: 1px solid #000;
    border-right: 1px solid #000;
    width: 1px;
    padding: 0;
}
.bank-cell-label {
    color: #555;
    font-size: 7.5pt;
    margin-bottom: 1mm;
}
.bank-cell-value {
    font-weight: bold;
}

/* ── Заголовок документа ── */
.doc-title {
    font-size: 14pt;
    font-weight: bold;
    text-align: center;
    margin: 4mm 0 1mm;
}
.doc-subtitle {
    font-size: 9pt;
    text-align: center;
    margin-bottom: 4mm;
    color: #333;
}

/* ── Блок сторон (поставщик / покупатель) ── */
.parties-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 4mm;
}
.parties-table td {
    padding: 1mm 0;
    vertical-align: top;
    font-size: 9pt;
}
.parties-label {
    font-weight: bold;
    width: 42mm;
    padding-right: 3mm;
}

/* ── Разделительная линия ── */
.hr {
    border: none;
    border-top: 1px solid #000;
    margin: 2mm 0;
}

/* ── Таблица позиций ── */
.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 3mm;
    font-size: 8.5pt;
}
.items-table th, .items-table td {
    border: 1px solid #000;
    padding: 1.5mm 2mm;
    vertical-align: middle;
}
.items-table th {
    font-weight: bold;
    text-align: center;
    background-color: #f5f5f5;
    padding: 4.5mm 2mm;
}
.col-num    { width: 7mm;  text-align: center; }
.col-name   { /* auto */ }
.col-qty    { width: 16mm; text-align: center; }
.col-unit   { width: 14mm; text-align: center; }
.col-price  { width: 24mm; text-align: right; }
.col-amount { width: 26mm; text-align: right; }

/* ── Итоги ── */
.totals-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 4mm;
}
.totals-table td {
    padding: 1mm 0;
    font-size: 9pt;
}
.totals-label {
    text-align: right;
    padding-right: 3mm;
    color: #333;
}
.totals-value {
    text-align: right;
    width: 36mm;
    font-weight: bold;
}
.totals-final .totals-label,
.totals-final .totals-value {
    font-size: 11pt;
    font-weight: bold;
    padding-top: 2mm;
}

/* ── Сумма прописью ── */
.amount-words {
    font-size: 9pt;
    margin-bottom: 6mm;
    padding-top: 2mm;
}

/* ── Подписи ── */
.signature-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10mm;
}
.signature-table td {
    font-size: 9pt;
    padding: 0 5mm 0 0;
    vertical-align: bottom;
}
.sig-line {
    border-bottom: 1px solid #000;
    display: inline-block;
    width: 40mm;
    margin: 0 2mm;
}
</style>
</head>
<body>
<div class="page-frame">

{{-- ═══════════════════════════════════════════════
     БЛОК БАНКОВСКИХ РЕКВИЗИТОВ
     Стандарт: Банк получателя → ИНН/КПП → Получатель
     ═══════════════════════════════════════════════ --}}
@php
    $bank = $invoice->bankAccount;
    $seller = $seller; // массив из PdfService::resolveSeller()
@endphp

<div class="bank-block">
    <table class="bank-table">
        <tr>
            {{-- Левая колонка: реквизиты банка --}}
            <td style="width:50%;">
                <div class="bank-cell-label">Банк получателя</div>
                <div class="bank-cell-value">{{ $bank?->bank ?? '—' }}</div>
            </td>
            <td class="divider"></td>
            {{-- Правая колонка: БИК --}}
            <td style="width:20%;">
                <div class="bank-cell-label">БИК</div>
                <div class="bank-cell-value">{{ $bank?->bik ?? '—' }}</div>
            </td>
            <td class="divider"></td>
            {{-- Правая колонка: расчётный счёт --}}
            <td style="width:30%;">
                <div class="bank-cell-label">Сч. №</div>
                <div class="bank-cell-value">{{ $bank?->payment_account ?? '—' }}</div>
            </td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td>
                <div class="bank-cell-label">ИНН</div>
                <div class="bank-cell-value">{{ $seller['inn'] }}{{ $seller['kpp'] ? ' / КПП ' . $seller['kpp'] : '' }}</div>
            </td>
            <td class="divider"></td>
            <td colspan="3">
                <div class="bank-cell-label">Кор. сч. №</div>
                <div class="bank-cell-value">{{ $bank?->correspondent_account ?? '—' }}</div>
            </td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td colspan="5">
                <div class="bank-cell-label">Получатель</div>
                <div class="bank-cell-value">{{ $seller['full_name'] }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ═══════════════════════════════════════════════
     ЗАГОЛОВОК ДОКУМЕНТА
     ═══════════════════════════════════════════════ --}}
<div class="doc-title">Счёт на оплату № {{ $invoice->number }}</div>
<div class="doc-subtitle">
    от {{ $invoice->date->day }} {{ $months[$invoice->date->month] }} {{ $invoice->date->year }} г.
    @if($invoice->due_date)
        &nbsp;&nbsp;|&nbsp;&nbsp; Срок оплаты: до {{ $invoice->due_date->day }} {{ $months[$invoice->due_date->month] }} {{ $invoice->due_date->year }} г.
    @endif
</div>

{{-- ═══════════════════════════════════════════════
     СТОРОНЫ ДОГОВОРА
     ═══════════════════════════════════════════════ --}}
@php $contractor = $invoice->contractor; @endphp

<table class="parties-table">
    <tr>
        <td class="parties-label">Поставщик (исполнитель):</td>
        <td>
            @if($seller['type'] === 'ip')
                {{ $seller['full_name'] }}@if($seller['inn']), ИНН {{ $seller['inn'] }}@endif
            @else
                {{ $seller['full_name'] }}
                @if($seller['inn']), ИНН {{ $seller['inn'] }}@endif
                @if($seller['kpp']), КПП {{ $seller['kpp'] }}@endif
                @if($seller['address']), {{ $seller['address'] }}@endif
            @endif
        </td>
    </tr>
    <tr>
        <td class="parties-label">Покупатель (заказчик):</td>
        <td>
            {{ $contractor->name }}
            @if($contractor->inn), ИНН {{ $contractor->inn }}@endif
            @if($contractor->kpp), КПП {{ $contractor->kpp }}@endif
            @if($contractor->legal_address), {{ $contractor->legal_address }}@endif
        </td>
    </tr>
    <tr>
        <td class="parties-label">Основание:</td>
        <td>
            @if($invoice->contract)
                {{ $invoice->contract->name }}
                @if($invoice->contract->number) № {{ $invoice->contract->number }}@endif
                @if($invoice->contract->date) от {{ $invoice->contract->date->format('d.m.Y') }}@endif
            @elseif($invoice->basis)
                {{ $invoice->basis }}
            @else
                Без договора
            @endif
        </td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════════
     ТАБЛИЦА ПОЗИЦИЙ
     ═══════════════════════════════════════════════ --}}
<table class="items-table">
    <thead>
        <tr>
            <th class="col-num">№</th>
            <th class="col-name">Наименование товара (работ, услуг)</th>
            <th class="col-qty">Кол-во</th>
            <th class="col-unit">Ед.</th>
            <th class="col-price">Цена, ₽</th>
            <th class="col-amount">Сумма, ₽</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $i => $item)
        <tr>
            <td class="col-num">{{ $i + 1 }}</td>
            <td class="col-name">{{ $item->name }}</td>
            <td class="col-qty">{{ rtrim(rtrim(number_format((float)$item->quantity, 3, ',', ' '), '0'), ',') }}</td>
            <td class="col-unit">{{ $item->unit }}</td>
            <td class="col-price">{{ number_format((float)$item->price, 2, ',', ' ') }}</td>
            <td class="col-amount">{{ number_format((float)$item->amount, 2, ',', ' ') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ═══════════════════════════════════════════════
     ИТОГИ
     ═══════════════════════════════════════════════ --}}
<table class="totals-table">
    <tr>
        <td class="totals-label">Итого:</td>
        <td class="totals-value">{{ number_format((float)$invoice->subtotal, 2, ',', ' ') }} ₽</td>
    </tr>
    <tr>
        <td class="totals-label">НДС:</td>
        <td class="totals-value">
            @if((float)$invoice->nds_amount > 0)
                {{ number_format((float)$invoice->nds_amount, 2, ',', ' ') }} ₽
            @else
                Без НДС
            @endif
        </td>
    </tr>
    <tr class="totals-final">
        <td class="totals-label">Всего к оплате:</td>
        <td class="totals-value">{{ number_format((float)$invoice->total, 2, ',', ' ') }} ₽</td>
    </tr>
</table>

{{-- Сумма прописью --}}
@php use App\Helpers\MoneyHelper; @endphp
<div class="amount-words">
    Всего наименований {{ $invoice->items->count() }},
    на сумму <strong>{{ MoneyHelper::rubles((float)$invoice->total) }}</strong>
</div>

{{-- ═══════════════════════════════════════════════
     ПОДПИСИ
     Структура для ИП:
       Левая ячейка:  [изображение подписи]  ← над линией
                      [————————————————————]  ← линия (border-bottom)
                      [Инд. предп. /ФИО/  ]  ← под линией
       Правая ячейка: [печать]               ← выровнена по низу

     DomPDF: object-fit не поддерживается — изображение задаём
     квадратным (width = height), чтобы не было искажений.
     ═══════════════════════════════════════════════ --}}
@php
    $hasSig   = !empty($sigSrc);
    $hasStamp = !empty($stampSrc);
@endphp

@if($seller['type'] === 'ip')
<table style="width:100%; border-collapse:collapse; margin-top:10mm;">
    {{-- Строка 1: изображение подписи --}}
    <tr>
        <td style="vertical-align:bottom;">
            @if($hasSig)
            <img src="{{ $sigSrc }}"
                 style="height:{{ $sigHeight }}mm; width:auto; display:block;">
            @else
            <div style="height:{{ $sigHeight }}mm;"></div>
            @endif
        </td>
    </tr>
    {{-- Строка 2: линия + роль --}}
    <tr>
        <td>
            <div style="border-bottom:1px solid #000;"></div>
            <div style="padding-top:2mm; font-size:9pt;">
                Индивидуальный предприниматель &nbsp;&nbsp; /{{ $seller['director'] }}/
            </div>
        </td>
    </tr>
    {{-- Строка 3: печать по центру --}}
    @if($hasStamp)
    <tr>
        <td style="text-align:center; padding-top:5mm;">
            <img src="{{ $stampSrc }}"
                 style="width:{{ $stampSize }}mm; height:auto;
                        display:block; margin:0 auto; opacity:0.9;">
        </td>
    </tr>
    @endif
</table>

@else
{{-- ЮЛ: две колонки — Руководитель (с подписью) | Главный бухгалтер (с печатью) --}}
<table style="width:100%; border-collapse:collapse; margin-top:10mm;">
    {{-- Строка 1: подпись (слева) | пусто (справа) --}}
    <tr>
        <td style="width:50%; vertical-align:bottom; padding-right:8mm;">
            @if($hasSig)
            <img src="{{ $sigSrc }}"
                 style="height:{{ $sigHeight }}mm; width:auto; display:block;">
            @else
            <div style="height:{{ $sigHeight }}mm;"></div>
            @endif
        </td>
        <td style="width:50%;"></td>
    </tr>
    {{-- Строка 2: линия + роль (обе колонки) --}}
    <tr>
        <td style="width:50%; padding-right:8mm; vertical-align:top;">
            <div style="border-bottom:1px solid #000;"></div>
            <div style="padding-top:2mm; font-size:9pt;">
                Руководитель &nbsp;&nbsp; /{{ $seller['director'] }}/
            </div>
        </td>
        <td style="width:50%; vertical-align:top;">
            <div style="border-bottom:1px solid #000;"></div>
            <div style="padding-top:2mm; font-size:9pt;">
                Главный бухгалтер &nbsp;&nbsp; /{{ $seller['director'] }}/
            </div>
        </td>
    </tr>
    {{-- Строка 3: печать по центру --}}
    @if($hasStamp)
    <tr>
        <td colspan="2" style="text-align:center; padding-top:5mm;">
            <img src="{{ $stampSrc }}"
                 style="width:{{ $stampSize }}mm; height:auto;
                        display:block; margin:0 auto; opacity:0.9;">
        </td>
    </tr>
    @endif
</table>
@endif

</div>
</body>
</html>

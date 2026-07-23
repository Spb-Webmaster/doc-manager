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

/* ── Блок сторон ── */
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

/* ── Подписи двух сторон ── */
.sign-block {
    margin-top: 8mm;
}
.sign-heading {
    font-weight: bold;
    margin-bottom: 3mm;
    font-size: 10pt;
}
</style>
</head>
<body>
<div class="page-frame">

{{-- ═══════════════════════════════════════════════
     ЗАГОЛОВОК ДОКУМЕНТА
     ═══════════════════════════════════════════════ --}}
@php $contractor = $act->contractor; @endphp

<div class="doc-title">Акт выполненных работ (оказанных услуг) № {{ $act->number }}</div>
<div class="doc-subtitle">
    от {{ $act->date->day }} {{ $months[$act->date->month] }} {{ $act->date->year }} г.
    @if($act->invoice)
        &nbsp;&nbsp;|&nbsp;&nbsp; К счёту № {{ $act->invoice->number }} от {{ $act->invoice->date->format('d.m.Y') }}
    @endif
</div>

<hr class="hr">

{{-- ═══════════════════════════════════════════════
     СТОРОНЫ ДОГОВОРА
     ═══════════════════════════════════════════════ --}}
<table class="parties-table">
    <tr>
        <td class="parties-label">Исполнитель:</td>
        <td>
            {{ $seller['full_name'] }}
            @if($seller['inn']), ИНН {{ $seller['inn'] }}@endif
            @if($seller['kpp']), КПП {{ $seller['kpp'] }}@endif
            @if($seller['address']), {{ $seller['address'] }}@endif
        </td>
    </tr>
    <tr>
        <td class="parties-label">Заказчик:</td>
        <td>
            {{ $contractor->name }}
            @if($contractor->inn), ИНН {{ $contractor->inn }}@endif
            @if($contractor->kpp), КПП {{ $contractor->kpp }}@endif
            @if($contractor->legal_address), {{ $contractor->legal_address }}@endif
        </td>
    </tr>
    <tr>
        <td class="parties-label">Основание:</td>
        <td>{{ $act->basis ?? 'Без договора' }}</td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════════
     ТАБЛИЦА ПОЗИЦИЙ
     ═══════════════════════════════════════════════ --}}
<table class="items-table">
    <thead>
        <tr>
            <th class="col-num">№</th>
            <th class="col-name">Наименование работ (услуг)</th>
            <th class="col-qty">Кол-во</th>
            <th class="col-unit">Ед.</th>
            <th class="col-price">Цена, ₽</th>
            <th class="col-amount">Сумма, ₽</th>
        </tr>
    </thead>
    <tbody>
        @foreach($act->items as $i => $item)
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
        <td class="totals-value">{{ number_format((float)$act->subtotal, 2, ',', ' ') }} ₽</td>
    </tr>
    <tr>
        <td class="totals-label">НДС:</td>
        <td class="totals-value">
            @if((float)$act->nds_amount > 0)
                {{ number_format((float)$act->nds_amount, 2, ',', ' ') }} ₽
            @else
                Без НДС
            @endif
        </td>
    </tr>
    <tr class="totals-final">
        <td class="totals-label">Итого к оплате:</td>
        <td class="totals-value">{{ number_format((float)$act->total, 2, ',', ' ') }} ₽</td>
    </tr>
</table>

{{-- Сумма прописью --}}
@php use App\Helpers\MoneyHelper; @endphp
<div class="amount-words">
    Всего наименований {{ $act->items->count() }},
    на сумму <strong>{{ MoneyHelper::rubles((float)$act->total) }}</strong>
</div>

{{-- ═══════════════════════════════════════════════
     ПОДПИСИ ДВУХ СТОРОН
     Акт подписывается с обеих сторон, в отличие от счёта.
     У Исполнителя может быть загружено изображение подписи/печати
     (см. предпросмотр в кабинете) — печать справа, подпись слева.
     ═══════════════════════════════════════════════ --}}
@php
    $hasSig   = !empty($sigSrc);
    $hasStamp = !empty($stampSrc);
@endphp

<div class="sign-block">
    <table style="width:100%; border-collapse:collapse;">
        {{-- Исполнитель: ФИО --}}
        <tr>
            <td style="padding-bottom:2mm;">
                <div class="sign-heading">Исполнитель</div>
                <div>{{ $seller['name'] }}</div>
            </td>
        </tr>
        {{-- Исполнитель: подпись (слева) и печать (справа) --}}
        <tr>
            <td style="padding-bottom:8mm;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:60%; vertical-align:bottom;">
                            @if($hasSig)
                            <img src="{{ $sigSrc }}" style="height:{{ $sigHeight }}mm; width:auto; display:block; margin-top:-50px;">
                            @else
                            <div style="height:{{ $sigHeight }}mm;"></div>
                            @endif
                        </td>
                        <td style="width:40%; text-align:right; vertical-align:bottom;">
                            @if($hasStamp)
                            <img src="{{ $stampSrc }}" style="width:{{ $stampSize }}mm; height:auto; display:inline-block; opacity:0.9; margin-top:-50px;">
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- Заказчик: ФИО --}}
        <tr>
            <td style="padding-bottom:2mm;">
                <div class="sign-heading">Заказчик</div>
                <div>{{ $contractor->name }}</div>
            </td>
        </tr>
        {{-- Заказчик подписывает бумажный экземпляр вручную --}}
        <tr>
            <td style="padding-bottom:8mm;">
                <div style="height:{{ $sigHeight }}mm;"></div>
            </td>
        </tr>
    </table>

    {{-- Итоговая строка о приёмке --}}
    <p style="font-size:9pt; margin-top:4mm;">
        Вышеперечисленные работы (услуги) выполнены полностью и в срок.
        Заказчик претензий по объёму, качеству и срокам оказания услуг не имеет.
    </p>
</div>

</div>
</body>
</html>

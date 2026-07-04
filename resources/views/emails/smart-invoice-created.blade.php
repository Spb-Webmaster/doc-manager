<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; color:#0F1628; font-size:14px; line-height:1.5;">
    <p>Здравствуйте!</p>

    <p>
        По расписанию умного счёта автоматически создан счёт
        № {{ $invoice->number }} от {{ $invoice->date->format('d.m.Y') }}
        на сумму {{ number_format((float) $invoice->total, 2, ',', ' ') }} ₽
        @if($invoice->contractor)
            для контрагента «{{ $invoice->contractor->name }}»
        @endif.
    </p>

    @if($act)
    <p>
        Вместе со счётом также создан акт
        № {{ $act->number }} от {{ $act->date->format('d.m.Y') }}
        на сумму {{ number_format((float) $act->total, 2, ',', ' ') }} ₽.
    </p>
    @endif

    <p>Документ создан в статусе «Черновик» — при необходимости вы можете отредактировать его в личном кабинете.</p>
</body>
</html>

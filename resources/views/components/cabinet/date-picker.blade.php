{{--
  Поле выбора даты кабинета.

  Разметку и поведение целиком строит JS-модуль resources/js/include/cabinet/calendar.js —
  компонент задаёт только параметры. Благодаря этому статичные формы и формы,
  собираемые на лету в JS, используют один и тот же календарь.

  Пример:
    <x-cabinet.date-picker id="inv-date" name="date" :value="now()" format="long" />

  @param string|null $name    name скрытого поля с датой в формате Y-m-d (для обычной отправки формы)
  @param mixed       $value   начальная дата: Carbon, DateTime, строка Y-m-d или null
  @param string|null $id      id видимого поля (нужен, если к нему обращается сторонний JS)
  @param string      $format  'long' — 13 ноября 2026 г., 'short' — 13.11.2026
--}}
@props([
    'name' => null,
    'value' => null,
    'id' => null,
    'format' => 'long',
    'placeholder' => 'Выберите дату',
])

@php
    $iso = $value instanceof \DateTimeInterface
        ? $value->format('Y-m-d')
        : trim((string) ($value ?? ''));
@endphp

<div {{ $attributes->merge(['class' => 'date-wrap']) }}
     data-calendar
     data-format="{{ $format }}"
     data-placeholder="{{ $placeholder }}"
     @if($id) data-input-id="{{ $id }}" @endif
     @if($name) data-name="{{ $name }}" @endif
     @if($iso !== '') data-value="{{ $iso }}" @endif></div>

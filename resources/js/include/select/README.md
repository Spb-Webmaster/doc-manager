# Standalone Styled Select

Автономный компонент стилизованного `select` без подключения Materialize целиком.

## Файлы

- `index.html` - демо-страница.
- `css/mz-select.css` - стили компонента.
- `js/mz-select.js` - JavaScript-инициализация.

## Как использовать

Подключите CSS и JS:

```html
<link rel="stylesheet" href="css/mz-select.css">
<script src="js/mz-select.js"></script>
```

Добавьте HTML:

```html
<div class="mz-select" data-mz-select>
  <label class="mz-select__label" for="city">Город</label>
  <select id="city" name="city">
    <option value="" disabled selected>Выберите город</option>
    <option value="msk">Москва</option>
    <option value="spb">Санкт-Петербург</option>
  </select>
</div>
```

Значение остается в настоящем `select`, поэтому поле нормально отправляется в форме и генерирует событие `change`.

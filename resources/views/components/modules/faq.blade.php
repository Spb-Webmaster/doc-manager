@props(['items' => []])

@php
    /**
     * Json-поле «Вопрос/Ответ» в админке почти всегда содержит мусорные строки:
     * блок добавили и не заполнили, пару добавили и оставили пустой. Раньше такие
     * записи доезжали до вёрстки как <details></details> — браузер рисует его
     * своим заголовком «Сведения».
     *
     * Поэтому чистим данные до вывода: пара живёт, только если заполнен вопрос
     * (без него у аккордеона нет заголовка), блок — только если в нём осталась
     * хоть одна пара. Если после чистки не осталось ничего, показываем тот же
     * запасной список вопросов, что и при незаполненном поле.
     */
    $faqBlocks = collect($items)
        ->map(fn ($block) => [
            'title'   => trim((string) data_get($block, 'title', '')),
            'options' => collect(data_get($block, 'options', []))
                ->filter(fn ($qa) => trim((string) data_get($qa, 'question', '')) !== '')
                ->values(),
        ])
        ->filter(fn ($block) => $block['options']->isNotEmpty())
        ->values();
@endphp

<section class="faq" id="faq">
    <div class="container faq__content">

        @if($faqBlocks->isNotEmpty())
            @foreach($faqBlocks as $block)
                @if($block['title'] !== '')
                    <h2>{{ $block['title'] }}</h2>
                @endif

                <div class="faq-list">
                    @foreach($block['options'] as $index => $qa)
                        <details {{ $index === 0 ? 'open' : '' }}>
                            <summary>{{ data_get($qa, 'question') }}</summary>

                            @if(filled(data_get($qa, 'answer')))
                                <div>{!! data_get($qa, 'answer') !!}</div>
                            @endif
                        </details>
                    @endforeach
                </div>
            @endforeach
        @else
            <h2>Частые вопросы?</h2>
            <div class="faq-list">
                <details open>
                    <summary>Когда проводится обучение?</summary>
                    <p>Обучение проходит по факту укомплектования групп, время согласовывается с участниками.</p>
                </details>
                <details>
                    <summary>Какова стоимость обучения?</summary>
                    <p>Стоимость зависит от программы и формата. Мы подберём подходящий вариант после заявки.</p>
                </details>
                <details>
                    <summary>Кто проводит обучение?</summary>
                    <p>Курсы ведут практикующие бухгалтеры, аудиторы и отраслевые эксперты.</p>
                </details>
                <details>
                    <summary>Где проводится обучение?</summary>
                    <p>Доступны онлайн, очные и смешанные форматы в Алматы и дистанционно.</p>
                </details>
                <details>
                    <summary>Продолжительность курса?</summary>
                    <p>Зависит от интенсивности. Базовые программы идут от 2 до 8 недель.</p>
                </details>
            </div>
        @endif

    </div>
</section>

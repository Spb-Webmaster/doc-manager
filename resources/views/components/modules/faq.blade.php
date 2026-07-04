@props(['items' => []])

<section class="faq" id="faq">
    <div class="container faq__content">

        @if(!empty($items))
            @foreach($items as $block)
                @if(!empty($block['options']))
                    @if(!empty($block['title']))
                        <h2>{{ $block['title'] }}</h2>
                    @endif
                    <div class="faq-list">
                        @foreach($block['options'] as $index => $qa)
                            <details {{ $index === 0 ? 'open' : '' }}>
                                @if(!empty($qa['question']))
                                    <summary>{{ $qa['question'] }}</summary>
                                @endif
                                @if(!empty($qa['answer']))
                                    <div>{!! $qa['answer'] !!}</div>
                                @endif
                            </details>
                        @endforeach
                    </div>
                @endif
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

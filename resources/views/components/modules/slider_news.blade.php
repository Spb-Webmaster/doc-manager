<section class="block block_3">
    <div class="flex_block flex_3">

        {{-- СЛАЙДЕР --}}
        <div class="block_3_slider">
            <div class="swiper block3-swiper">
                <div class="swiper-wrapper">



                    <div class="swiper-slide">
                        <a href=""><span style="background-image: url('{{ Storage::url('images/slider/new2020/baner_kap_1.png') }}')"></span></a>
                    </div>
                    <div class="swiper-slide">
                        <a href=""><span style="background-image: url('{{ Storage::url('images/slider/new2020/baner_prof_bukh_2.png') }}')"></span></a>
                    </div>
                    <div class="swiper-slide">
                        <a href=""><span style="background-image: url('{{ Storage::url('images/slider/new2020/baner_dipifr_3.png') }}')"></span></a>
                    </div>
                    <div class="swiper-slide">
                        <a href=""><span style="background-image: url('{{ Storage::url('images/slider/new2020/baner_kategorii_4.png') }}')"></span></a>
                    </div>
                    <div class="swiper-slide">
                        <a href=""><span style="background-image: url('{{ Storage::url('images/slider/new2020/baner_fin_menedzher_5.png') }}')"></span></a>
                    </div>
                    <div class="swiper-slide">
                        <a href=""><span style="background-image: url('{{ Storage::url('images/slider/new2020/Banner_MSFOOS_6.png') }}')"></span></a>
                    </div>
                    <div class="swiper-slide">
                        <a href=""><span style="background-image: url('{{ Storage::url('images/slider/new2020/baner_1s_obuchenie_7.png') }}')"></span></a>
                    </div>
                    <div class="swiper-slide">
                        <a href=""><span style="background-image: url('{{ Storage::url('images/slider/new2020/baner_prof_mediator_8.png') }}')"></span></a>
                    </div>


                </div>

                <button class="swiper-button-prev" aria-label="Предыдущий слайд"></button>
                <button class="swiper-button-next" aria-label="Следующий слайд"></button>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        {{-- НОВОСТИ --}}
        <div class="block_3_news">
            <div class="scroll-block block_mod_news">

                @foreach([
                    [
                        'href'  => '/poleznoe/novosti/strategy',
                        'title' => 'Утверждена стратегия',
                        'desc'  => 'Целью является внедрение международных стандартов и создание условий для активной профессиональной деятельности бухгалтеров.',
                    ],
                    [
                        'href'  => '/poleznoe/novosti/profbuh',
                        'title' => 'Профессиональный бухгалтер РК',
                        'desc'  => 'Вид сертификации который является обязательным для ряда бухгалтеров в Казахстане с 1 января 2012 года.',
                    ],
                    [
                        'href'  => '/poleznoe/novosti/poleznye-ssylki',
                        'title' => 'Полезные ссылки',
                        'desc'  => 'МСФО, Финансы, Управленческий учёт и другие полезные профессиональные ресурсы.',
                    ],
                    [
                        'href'  => '/poleznoe/novosti/cipa',
                        'title' => 'Уважаемые участники программы CIPA',
                        'desc'  => 'В последнее время участились случаи рассылок информации о необходимости получения сертификата профессионального бухгалтера.',
                    ],
                    [
                        'href'  => '/poleznoe/novosti/msfogu',
                        'title' => 'Обучение по программе МСФООС для Г.У. РК',
                        'desc'  => 'Бухгалтера государственных учреждений должны иметь сертификат о прохождении обучения по программе МСФООС.',
                    ],
                    [
                        'href'  => '/poleznoe/novosti/finmeng',
                        'title' => 'Сертифицированный финансовый менеджер',
                        'desc'  => 'Американский или Британский сертификат. Профессиональные организации, представляющие интересы бухгалтеров на всех уровнях.',
                    ],
                    [
                        'href'  => '/poleznoe/novosti/traiding',
                        'title' => 'Трейдинг и Инвестиционная стратегия',
                        'desc'  => 'Уважаемые инвесторы, мы объявляем о старте курса "Trading & Investing strategy".',
                    ],
                ] as $news)
                <div class="bl_one_new">
                    <div class="teaser_title_600">
                        <h4><a class="color_282828" href="{{ $news['href'] }}">{{ $news['title'] }}</a></h4>
                    </div>
                    <div class="teaser_shortdesc desc_static">
                        <a href="{{ $news['href'] }}" class="desc_p">{{ $news['desc'] }}</a>
                    </div>
                </div>
                <div class="new_line"></div>
                @endforeach

            </div>
        </div>

    </div>
</section>

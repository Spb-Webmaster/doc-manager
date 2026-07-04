@extends('layouts.cabinet')

@section('title', 'Кабинет — СчётОк')

@push('styles')
<style>
  .empty-req {
    display: flex; flex-direction: column; align-items: center;
    text-align: center; padding: 32px 24px; gap: 10px;
  }
  .empty-req-ico {
    width: 54px; height: 54px; border-radius: 14px;
    background: var(--accent-lt);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 4px;
  }
  .empty-req-title { font-size: 14.5px; font-weight: 700; color: var(--text-h); }
  .empty-req-desc  { font-size: 12.5px; color: var(--text-s); line-height: 1.65; max-width: 230px; }
  .empty-req-btn   { font-size: 13px; padding: 9px 20px; margin-top: 6px; text-decoration: none; }

  .cp-empty {
    display: flex; flex-direction: column; align-items: center;
    text-align: center; padding: 52px 24px 56px; gap: 12px;
  }
  .cp-empty-ico {
    width: 72px; height: 72px; border-radius: 20px;
    background: var(--accent-lt);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 8px;
  }
  .cp-empty-title { font-size: 16px; font-weight: 700; color: var(--text-h); letter-spacing: -.2px; }
  .cp-empty-desc  { font-size: 13.5px; color: var(--text-s); line-height: 1.7; max-width: 320px; }
  .cp-empty-actions { display: flex; align-items: center; gap: 10px; margin-top: 8px; flex-wrap: wrap; justify-content: center; }
</style>
@endpush

@section('content')

  <!-- Topbar -->
  <header class="topbar">
    <div class="tb-left">
      <span class="tb-title">Кабинет</span>
      <span class="tb-date">{{ now()->translatedFormat('d F Y') }}</span>
    </div>
    <div class="tb-right">
      <a href="#" class="btn btn-outline">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8 2H4a1.5 1.5 0 0 0-1.5 1.5v7A1.5 1.5 0 0 0 4 12h6a1.5 1.5 0 0 0 1.5-1.5V5.5M8 2v3.5H11.5M5 8l2 2 3-3"/>
        </svg>
        Создать акт
      </a>
      <a href="{{ route('cabinet.invoices.create') }}" class="btn btn-primary">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8 2H4a1.5 1.5 0 0 0-1.5 1.5v7A1.5 1.5 0 0 0 4 12h6a1.5 1.5 0 0 0 1.5-1.5V5.5M8 2v3.5H11.5M7 8v3M5.5 9.5h3"/>
        </svg>
        Создать счёт
      </a>
    </div>
  </header>

  <!-- Scrollable content -->
  <div class="content">

    <!-- Quick actions -->
    <div class="qa-grid">
      <a class="qa-card qa-prime" href="{{ route('cabinet.invoices.create') }}">
        <div class="qa-ico" style="background:rgba(255,255,255,.15);">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11.5 3H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8.5z"/>
            <path d="M11.5 3v5.5H17M10 11v4M8 13h4"/>
          </svg>
        </div>
        <div>
          <div class="qa-title">Создать счёт</div>
          <div class="qa-desc">Два клика по ИНН</div>
        </div>
      </a>
      <a class="qa-card" href="#">
        <div class="qa-ico" style="background:var(--green-lt);">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="#159B6A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11.5 3H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8.5z"/>
            <path d="M11.5 3v5.5H17M6.5 13.5l2.5 2.5 4.5-4.5"/>
          </svg>
        </div>
        <div>
          <div class="qa-title">Создать акт</div>
          <div class="qa-desc">Акт выполненных работ</div>
        </div>
      </a>
      <a class="qa-card" href="#">
        <div class="qa-ico" style="background:var(--purple-lt);">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="#6B45D8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8" cy="7" r="3.5"/>
            <path d="M1.5 18c0-3.5 2.9-6.5 6.5-6.5M14.5 12.5v5M12 15h5"/>
          </svg>
        </div>
        <div>
          <div class="qa-title">Добавить контрагента</div>
          <div class="qa-desc">Автозаполнение по ИНН</div>
        </div>
      </a>
    </div>

    <!-- 2-column: Реквизиты + Сводка -->
    <div class="col2">

      <!-- Мои реквизиты -->
      <div class="cab-card">
        @if($reqData)
          <div class="card-head">
            <div class="card-title">Мои реквизиты</div>
            <a href="{{ route('cabinet.settings') }}" class="card-link">Редактировать</a>
          </div>
          <div class="card-body">
            <div class="req-row">
              <div class="req-key">ИНН</div>
              <div class="req-val mono">{{ $reqData['inn'] }}</div>
            </div>
            @if($reqData['name'])
            <div class="req-row">
              <div class="req-key">Наименование</div>
              <div class="req-val">{{ $reqData['name'] }}</div>
            </div>
            @endif
            @if($reqData['ogrn'])
            <div class="req-row">
              <div class="req-key">{{ $reqData['ogrn_label'] }}</div>
              <div class="req-val mono">{{ $reqData['ogrn'] }}</div>
            </div>
            @endif
            @if($reqData['address'])
            <div class="req-row">
              <div class="req-key">Адрес</div>
              <div class="req-val">{{ $reqData['address'] }}</div>
            </div>
            @endif
            @if($bank)
            <div class="req-row">
              <div class="req-key">Р/счёт</div>
              <div class="req-val mono">
                {{ substr($bank->payment_account, 0, 4) }} •••• •••• {{ substr($bank->payment_account, -4) }}
                <span class="req-dim"> · {{ $bank->bank }}</span>
              </div>
            </div>
            @endif
            <div class="req-row">
              <div class="req-key">Почта</div>
              <div class="req-val">{{ auth()->user()->email }}</div>
            </div>
          </div>
        @else
          <div class="card-head">
            <div class="card-title">Мои реквизиты</div>
          </div>
          <div class="card-body" style="padding:0;">
            <div class="empty-req">
              <div class="empty-req-ico">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="3"/>
                  <path d="M7 8h10M7 12h10M7 16h6"/>
                </svg>
              </div>
              <div class="empty-req-title">Реквизиты не заполнены</div>
              <div class="empty-req-desc">Добавьте ИНН и данные организации — они будут автоматически подставляться в счета и акты.</div>
              <a href="{{ route('cabinet.settings') }}" class="btn btn-primary empty-req-btn">Заполнить реквизиты</a>
            </div>
          </div>
        @endif
      </div>

      <!-- Сводка -->
      <div class="cab-card">
        <div class="card-head">
          <div class="card-title">Сводка</div>
          <span class="card-meta">с января {{ now()->year }}</span>
        </div>
        <div class="card-body">
          <div class="stat-row">
            <div class="stat-lhs">
              <div class="stat-ico" style="background:var(--accent-lt);">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#2550E2" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 2H4a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 4 14h8a1.5 1.5 0 0 0 1.5-1.5V7z"/>
                  <path d="M9 2v5h5M5.5 9.5h5M5.5 11.5h3"/>
                </svg>
              </div>
              Счетов выставлено
            </div>
            <div class="stat-num">0</div>
          </div>
          <div class="stat-row">
            <div class="stat-lhs">
              <div class="stat-ico" style="background:var(--green-lt);">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#159B6A" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 2H4a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 4 14h8a1.5 1.5 0 0 0 1.5-1.5V7z"/>
                  <path d="M9 2v5h5M5 9.5l2 2 4-4"/>
                </svg>
              </div>
              Актов выставлено
            </div>
            <div class="stat-num">0</div>
          </div>
          <div class="stat-row">
            <div class="stat-lhs">
              <div class="stat-ico" style="background:var(--purple-lt);">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#6B45D8" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="5.5" cy="5" r="2.5"/>
                  <circle cx="11" cy="5" r="2.5"/>
                  <path d="M1 13.5c0-2.5 2-4.5 4.5-4.5h4c2.5 0 4.5 2 4.5 4.5"/>
                </svg>
              </div>
              Контрагентов
            </div>
            <div class="stat-num">{{ $contractors->count() }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Контрагенты -->
    <div class="sec-hd">
      <div class="sec-title">Контрагенты</div>
      @if($contractors->isNotEmpty())
      <a href="{{ route('cabinet.contractors') }}" class="btn btn-outline" style="font-size:13px; padding:7px 14px;">
        Все контрагенты
      </a>
      @endif
    </div>

    @if($contractors->isEmpty())
      <div class="tbl-wrap">
        <div class="cp-empty">
          <div class="cp-empty-ico">
            <svg width="34" height="34" viewBox="0 0 34 34" fill="none" stroke="var(--accent)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="13" cy="12" r="5"/>
              <circle cx="24" cy="12" r="5"/>
              <path d="M2 29c0-5 4-9 11-9h8c7 0 11 4 11 9"/>
            </svg>
          </div>
          <div class="cp-empty-title">Контрагентов пока нет</div>
          <div class="cp-empty-desc">Добавьте первого контрагента — его реквизиты будут автоматически подставляться в счета и акты. Достаточно ввести ИНН.</div>
          <div class="cp-empty-actions">
            <a href="{{ route('cabinet.contractors.create') }}" class="btn btn-primary" style="font-size:13.5px;">
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <path d="M6.5 1.5v10M1.5 6.5h10"/>
              </svg>
              Добавить контрагента
            </a>
            <a href="{{ route('cabinet.contractors') }}" class="btn btn-outline" style="font-size:13.5px;">
              К контрагентам
            </a>
          </div>
        </div>
      </div>
    @else
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>Контрагент</th>
              <th class="c">Счетов</th>
              <th class="c">Актов</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($contractors->take(10) as $c)
            <tr>
              <td>
                <div class="td-name">{{ $c->name }}</div>
                <div class="td-inn">ИНН {{ $c->inn }}</div>
              </td>
              <td class="c">
                <span class="badge {{ $c->invoices_count > 0 ? 'badge-inv' : 'badge-zero' }}">{{ $c->invoices_count }}</span>
              </td>
              <td class="c">
                <span class="badge {{ $c->acts_count > 0 ? 'badge-act' : 'badge-zero' }}">{{ $c->acts_count }}</span>
              </td>
              <td>
                <a href="{{ route('cabinet.contractors') }}?open={{ $c->id }}" class="row-btn">
                  <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 2H3.5A1.5 1.5 0 0 0 2 3.5v5A1.5 1.5 0 0 0 3.5 10h5A1.5 1.5 0 0 0 10 8.5V5M7 2v3h3M6 6v2.5M5 7h2"/>
                  </svg>
                  Открыть
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

  </div>

@endsection


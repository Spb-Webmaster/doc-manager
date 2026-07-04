{{-- Компонент: модальное окно загрузки изображения (подпись / печать) --}}
{{-- Использование:
  <x-cabinet.upload-modal prefix="sig" title="Поставить подпись" subtitle="..." drop-text="..." hint-text="..." alt="Подпись">
    <svg>...</svg>
  </x-cabinet.upload-modal>
--}}
{{-- JS API: setupUpload(zoneId, fileId, previewWrapId, previewImgId, reId, applyBtnId, openBtnId, onApply) --}}
{{-- Зависимости страницы: openModal(id) / closeModal(id) должны быть объявлены в скриптах страницы --}}
{{-- CSS: resources/css/components/cabinet/upload-modal.scss --}}

@props([
    'prefix',
    'title',
    'subtitle',
    'dropText',
    'hintText',
    'alt',
    'previewStyle' => null,
])

<div class="modal-overlay" id="{{ $prefix }}-modal">
  <div class="inv-modal">
    <div class="modal-head">
      <div>
        <div class="modal-title">{{ $title }}</div>
        <div class="modal-sub">{{ $subtitle }}</div>
      </div>
      <button class="modal-x" data-close="{{ $prefix }}-modal">×</button>
    </div>
    <div class="modal-body">
      <div class="upload-zone" id="{{ $prefix }}-zone">
        <input type="file" id="{{ $prefix }}-file" accept="image/*" style="display:none">
        {{ $slot }}
        <p>{{ $dropText }}</p>
        <small>{{ $hintText }}</small>
      </div>
      <div class="upload-preview" id="{{ $prefix }}-preview-wrap" style="display:none">
        <div class="upload-preview-img-wrap">
          <img id="{{ $prefix }}-preview-img" alt="{{ $alt }}"@if($previewStyle) style="{{ $previewStyle }}"@endif>
        </div>
        <div class="upload-scale">
          <span class="upload-scale-label">Размер</span>
          <button type="button" class="scale-btn" id="{{ $prefix }}-scale-minus">−</button>
          <input type="range" class="scale-range" id="{{ $prefix }}-scale" min="50" max="200" value="100" step="5">
          <button type="button" class="scale-btn" id="{{ $prefix }}-scale-plus">+</button>
          <span class="scale-val" id="{{ $prefix }}-scale-val">100%</span>
        </div>
        <button class="upload-re" id="{{ $prefix }}-re">Загрузить другую</button>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-outline" data-close="{{ $prefix }}-modal">Отмена</button>
      <button class="btn btn-primary" id="{{ $prefix }}-apply" disabled>Применить</button>
    </div>
  </div>
</div>

@once
@push('scripts')
<script>
  function setupUpload(zoneId, fileId, previewWrapId, previewImgId, reId, applyBtnId, openBtnId, onApply) {
    const zone    = document.getElementById(zoneId);
    const file    = document.getElementById(fileId);
    const wrap    = document.getElementById(previewWrapId);
    const img     = document.getElementById(previewImgId);
    const re      = document.getElementById(reId);
    const apply   = document.getElementById(applyBtnId);
    const openBtn = document.getElementById(openBtnId);
    const modal   = zone.closest('.modal-overlay');

    const prefix     = zoneId.replace('-zone', '');
    const scaleRange = document.getElementById(prefix + '-scale');
    const scaleMinus = document.getElementById(prefix + '-scale-minus');
    const scalePlus  = document.getElementById(prefix + '-scale-plus');
    const scaleVal   = document.getElementById(prefix + '-scale-val');
    let currentScale = 100;
    let appliedSrc   = null;
    let appliedScale = 100;

    function setScale(val) {
      currentScale = Math.min(200, Math.max(50, val));
      if (scaleRange) scaleRange.value = currentScale;
      if (scaleVal)   scaleVal.textContent = currentScale + '%';
      img.style.transform = 'scale(' + (currentScale / 100) + ')';
    }
    function showZone() {
      wrap.style.display = 'none'; zone.style.display = '';
      apply.disabled = true; file.value = ''; setScale(100);
    }
    function showPreview(src, scale) {
      img.src = src; zone.style.display = 'none'; wrap.style.display = '';
      apply.disabled = false; setScale(scale);
    }
    if (scaleRange) scaleRange.addEventListener('input', () => setScale(+scaleRange.value));
    if (scaleMinus) scaleMinus.addEventListener('click', () => setScale(currentScale - 5));
    if (scalePlus)  scalePlus.addEventListener('click',  () => setScale(currentScale + 5));
    openBtn.addEventListener('click', () => {
      appliedSrc ? showPreview(appliedSrc, appliedScale) : showZone();
      openModal(modal.id);
    });
    function dismiss() {
      closeModal(modal.id);
      appliedSrc ? showPreview(appliedSrc, appliedScale) : showZone();
    }
    modal.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', dismiss));
    zone.addEventListener('click', () => file.click());
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
    zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag'); handleFile(e.dataTransfer.files[0]); });
    file.addEventListener('change', () => handleFile(file.files[0]));
    re.addEventListener('click', () => showZone());
    function handleFile(f) {
      if (!f || !f.type.startsWith('image/')) return;
      const reader = new FileReader();
      reader.onload = e => showPreview(e.target.result, 100);
      reader.readAsDataURL(f);
    }
    apply.addEventListener('click', () => {
      appliedSrc = img.src; appliedScale = currentScale;
      onApply(appliedSrc, appliedScale);
      openBtn.classList.add('applied');
      closeModal(modal.id);
    });
  }
</script>
@endpush
@endonce

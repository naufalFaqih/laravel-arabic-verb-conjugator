{{--
    Arabic on-screen keyboard layout.

    Rendered once near the bottom of the layout (so it's available on every
    page that uses the layout component). The actual show/hide behaviour is
    handled by `resources/js/arabic-keyboard.js`, which targets inputs marked
    with class `arabic-input` (or attribute `data-arabic-keyboard`).

    The layout follows a typical Arabic keyboard arrangement plus a row of
    common harakat (diacritics) used in classical Arabic verb morphology.
--}}
<div id="arabic-keyboard" class="ak-hidden" aria-hidden="true">
    <div class="ak-panel">
        <div class="ak-header">
            <span class="ak-title">⌨ لوحة المفاتيح العربية</span>
            <button type="button" class="ak-key ak-key--close" data-action="close" aria-label="Tutup">
                ✕
            </button>
        </div>

        @php
            $rows = [
                ['ض', 'ص', 'ث', 'ق', 'ف', 'غ', 'ع', 'ه', 'خ', 'ح', 'ج', 'د'],
                ['ش', 'س', 'ي', 'ب', 'ل', 'ا', 'ت', 'ن', 'م', 'ك', 'ط'],
                ['ئ', 'ء', 'ؤ', 'ر', 'ى', 'ة', 'و', 'ز', 'ظ', 'ذ'],
                ['أ', 'إ', 'آ', 'ـ'],
            ];
            $harakat = [
                ['char' => "\u{064E}", 'label' => 'ـَ', 'name' => 'fathah'],
                ['char' => "\u{064F}", 'label' => 'ـُ', 'name' => 'dhammah'],
                ['char' => "\u{0650}", 'label' => 'ـِ', 'name' => 'kasrah'],
                ['char' => "\u{0651}", 'label' => 'ـّ', 'name' => 'syaddah'],
                ['char' => "\u{0652}", 'label' => 'ـْ', 'name' => 'sukun'],
                ['char' => "\u{064B}", 'label' => 'ـً', 'name' => 'fathatain'],
                ['char' => "\u{064C}", 'label' => 'ـٌ', 'name' => 'dhammatain'],
                ['char' => "\u{064D}", 'label' => 'ـٍ', 'name' => 'kasratain'],
            ];
        @endphp

        @foreach($rows as $row)
            <div class="ak-row">
                @foreach($row as $char)
                    <button type="button" class="ak-key" data-char="{{ $char }}">{{ $char }}</button>
                @endforeach
            </div>
        @endforeach

        <div class="ak-row ak-row--harakat" aria-label="Harakat">
            @foreach($harakat as $h)
                <button type="button" class="ak-key ak-key--harakat" data-char="{{ $h['char'] }}" title="{{ $h['name'] }}">{{ $h['label'] }}</button>
            @endforeach
        </div>

        <div class="ak-row ak-row--controls">
            <button type="button" class="ak-key ak-key--space" data-action="space">مسافة (space)</button>
            <button type="button" class="ak-key ak-key--backspace" data-action="backspace" aria-label="Backspace">
                ← Hapus
            </button>
        </div>
    </div>
</div>

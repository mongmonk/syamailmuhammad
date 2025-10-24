@extends('layouts.app')

@section('title', 'Admin - Tambah Hadits')

@section('content')
<div class="bg-white py-8">
  <div class="max-w-5xl mx-auto px-4">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
      <ol class="list-reset inline-flex items-center space-x-2">
        <li><a class="text-gray-700 hover:text-emerald-700" href="{{ route('admin.index') }}">Admin</a></li>
        <li class="text-gray-400">/</li>
        <li><a class="text-gray-700 hover:text-emerald-700" href="{{ route('admin.hadiths.index') }}">Hadits</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-gray-800">Tambah</li>
      </ol>
    </nav>

    <div class="flex items-start justify-between mb-3">
      <div>
        <h1 class="text-2xl font-bold">Tambah Hadits</h1>
        <p class="text-sm text-gray-600">Masukkan data hadits secara rapi. Pastikan nomor unik per bab.</p>
      </div>
      <a href="{{ route('admin.hadiths.index') }}" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-gray-400 text-gray-700 rounded hover:bg-gray-400/20 transition-colors">Kembali</a>
    </div>

    @if(session('status'))
      <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 rounded">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
        <p class="font-semibold mb-1">Periksa kembali isian Anda:</p>
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Form Card -->
      <div class="md:col-span-2">
        <div class="bg-white border rounded shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b">
            <h2 class="text-sm font-semibold text-gray-800">Form Isian Hadits</h2>
          </div>

          <form method="POST" action="{{ route('admin.hadiths.store') }}" class="px-6 py-6 space-y-6" enctype="multipart/form-data">
            @csrf

            <!-- Bab -->
            <div>
              <label for="chapter_id" class="block text-sm font-medium text-gray-700 mb-1">Bab <span class="text-red-600">*</span></label>
              <select
                id="chapter_id"
                name="chapter_id"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                required
                aria-describedby="help_chapter"
              >
                <option value="" disabled {{ old('chapter_id') ? '' : 'selected' }}>Pilih bab...</option>
                @foreach ($chapters as $c)
                  <option value="{{ $c->id }}" {{ (string)old('chapter_id') === (string)$c->id ? 'selected' : '' }}>
                    Bab {{ $c->chapter_number }} — {{ $c->title }}
                  </option>
                @endforeach
              </select>
              <p id="help_chapter" class="mt-1 text-xs text-gray-500">Wajib pilih bab tujuan.</p>
              @error('chapter_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Nomor Hadits -->
            <div>
              <label for="hadith_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Hadits <span class="text-red-600">*</span></label>
              <input
                type="number"
                min="1"
                id="hadith_number"
                name="hadith_number"
                value="{{ old('hadith_number') }}"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Masukkan nomor hadits (angka)"
                required
                aria-describedby="help_hadith_number"
              >
              <p id="help_hadith_number" class="mt-1 text-xs text-gray-500">Harus angka ≥ 1 dan unik dalam bab terpilih.</p>
              @error('hadith_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Teks Arab -->
            <div>
              <label for="arabic_text" class="block text-sm font-medium text-gray-700 mb-1">Teks Arab <span class="text-red-600">*</span></label>
              <textarea
                id="arabic_text"
                name="arabic_text"
                rows="5"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-right"
                style="font-family: 'Amiri Quran','Noto Naskh Arabic','KFGQPC Uthman Taha Naskh', serif; font-size: 1.15rem;"
                placeholder="ادخل نص الحديث هنا"
                required
                aria-describedby="help_arabic"
              >{{ old('arabic_text') }}</textarea>
              <p id="help_arabic" class="mt-1 text-xs text-gray-500">Gunakan penulisan Arab yang benar. Area ini rata kanan dan menggunakan font Arab.</p>
              @error('arabic_text')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Terjemahan -->
            <div>
              <label for="translation" class="block text-sm font-medium text-gray-700 mb-1">Terjemahan <span class="text-red-600">*</span></label>
              <textarea
                id="translation"
                name="translation"
                rows="4"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Masukkan terjemahan hadits"
                required
                aria-describedby="help_trans"
              >{{ old('translation') }}</textarea>
              <p id="help_trans" class="mt-1 text-xs text-gray-500">Terjemahan bahasa Indonesia.</p>
              @error('translation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Catatan Kaki (opsional) -->
            <div>
              <label for="footnotes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Kaki (opsional)</label>
              <textarea
                id="footnotes"
                name="footnotes"
                rows="3"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Keterangan sumber, takhrij, atau catatan lain (opsional)"
                aria-describedby="help_footnotes"
              >{{ old('footnotes') }}</textarea>
              <p id="help_footnotes" class="mt-1 text-xs text-gray-500">Opsional. Bisa berisi catatan rujukan.</p>
              @error('footnotes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Audio File (opsional) -->
            <div>
              <label for="audio_file" class="block text-sm font-medium text-gray-700 mb-1">File Audio (opsional)</label>
              <input
                type="file"
                id="audio_file"
                name="audio_file"
                accept="audio/*"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                aria-describedby="help_audio"
              >
              <p id="help_audio" class="mt-1 text-xs text-gray-500">
                Unggah file audio untuk hadits ini. Format umum: MP3, OGG, WAV. Maksimal 15MB.
              </p>
              @error('audio_file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Actions -->
            <div class="pt-2 flex items-center gap-3">
              <button type="submit" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-emerald-600 text-emerald-700 rounded hover:bg-emerald-600/50 hover:text-white transition-colors">Simpan</button>
              <a href="{{ route('admin.hadiths.index') }}" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-gray-400 text-gray-700 rounded hover:bg-gray-400/20 transition-colors">Batal</a>
            </div>
          </form>
        </div>
      </div>

      <!-- Side Panel -->
      <aside class="md:col-span-1">
        <div class="bg-white border rounded shadow-sm">
          <div class="px-5 py-4 border-b">
            <h2 class="text-sm font-semibold text-gray-800">Panduan Singkat</h2>
          </div>
          <div class="px-5 py-4 space-y-3 text-sm text-gray-700">
            <p>Pastikan memilih bab yang tepat sebelum mengisi nomor hadits.</p>
            <p>Nomor hadits harus unik dalam bab terpilih (tidak boleh duplikat).</p>
            <p>Teks Arab sebaiknya diperiksa kembali harakat dan ejaan.</p>
          </div>
        </div>
      </aside>
    </div>

    <div class="mt-6">
      <a href="{{ route('admin.index') }}" class="text-emerald-700 hover:underline">Kembali ke Dashboard Admin</a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<style>
/* Styling konten editor CKEditor */
.ck-content.rtl { font-family: 'Amiri Quran','Noto Naskh Arabic','KFGQPC Uthman Taha Naskh', serif; font-size: 18px; line-height: 2; text-align: right; }
.ck-content.ltr { font-family: 'Mulish', system-ui, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans'; font-size: 16px; line-height: 1.6; }

/* Pastikan list (angka/bullet) tampil di CKEditor */
.ck-content.rtl ol { list-style: decimal; list-style-position: outside; padding-inline-start: 1.25rem; margin: .5rem 0; }
.ck-content.rtl ul { list-style: disc; list-style-position: outside; padding-inline-start: 1.25rem; margin: .5rem 0; }
.ck-content.ltr ol { list-style: decimal; list-style-position: outside; padding-inline-start: 1.25rem; margin: .5rem 0; }
.ck-content.ltr ul { list-style: disc; list-style-position: outside; padding-inline-start: 1.25rem; margin: .5rem 0; }

/* Fallback SimpleEditor (non-CDN, lokal) */
.simple-editor { border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; min-height: 140px; background: #fff; }
.simple-editor.rtl { font-family: 'Amiri Quran','Noto Naskh Arabic','KFGQPC Uthman Taha Naskh', serif; font-size: 18px; line-height: 2; text-align: right; }
.simple-editor.ltr { font-family: 'Mulish', system-ui, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans'; font-size: 16px; line-height: 1.6; text-align: left; }
.simple-editor-toolbar { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: .5rem; }
.simple-editor-toolbar button { border: 1px solid #d1d5db; padding: .25rem .5rem; border-radius: .375rem; background: #f9fafb; font-size: .875rem; }

/* Pastikan list (angka/bullet) tampil di SimpleEditor */
.simple-editor ol { list-style: decimal; list-style-position: outside; padding-inline-start: 1.25rem; margin: .5rem 0; }
.simple-editor ul { list-style: disc; list-style-position: outside; padding-inline-start: 1.25rem; margin: .5rem 0; }

/* Fallback penyesuaian RTL untuk list */
.simple-editor.rtl ol,
.simple-editor.rtl ul {
  padding-inline-start: 0;
  padding-inline-end: 1.25rem; /* modern */
  padding-right: 1.25rem;      /* fallback untuk browser lama */
}
</style>
<script src="https://cdn.ckeditor.com/ckeditor5/44.2.0/classic/ckeditor.js"></script>
<script>
// Inisialisasi editor dengan fallback lokal bila CDN diblokir oleh CSP
document.addEventListener('DOMContentLoaded', function () {
  function useCK() { return typeof ClassicEditor !== 'undefined'; }

  // Sinkronisasi nilai textarea saat submit (CKEditor 5 tidak auto sinkron)
  function attachSubmitSync(el, editor) {
    try {
      var form = el && el.form;
      if (form) {
        form.addEventListener('submit', function () {
          editor.updateSourceElement();
        }, { once: false });
      }
    } catch (e) {
      console.warn('Gagal attach submit sync:', e);
    }
  }

  // Fallback: SimpleEditor berbasis contenteditable (tanpa CDN)
  function initFallbackSimpleEditor(textarea, opts) {
    var isRtl = !!(opts && opts.rtl);
    try {
      var toolbar = document.createElement('div');
      toolbar.className = 'simple-editor-toolbar';
      var editorDiv = document.createElement('div');
      editorDiv.className = 'simple-editor ' + (isRtl ? 'rtl' : 'ltr');
      editorDiv.setAttribute('contenteditable', 'true');
      editorDiv.setAttribute('dir', isRtl ? 'rtl' : 'ltr');
      editorDiv.innerHTML = textarea.value || '';

      function makeBtn(label, handler) {
        var b = document.createElement('button');
        b.type = 'button';
        b.textContent = label;
        b.addEventListener('click', function (e) {
          e.preventDefault();
          try { handler(); } catch (err) { console.warn('Toolbar error:', err); }
          editorDiv.focus();
        });
        return b;
      }

      toolbar.appendChild(makeBtn('B', function(){ document.execCommand('bold'); }));
      toolbar.appendChild(makeBtn('I', function(){ document.execCommand('italic'); }));
      toolbar.appendChild(makeBtn('U', function(){ document.execCommand('underline'); }));
      // Superscript/Subscript untuk penulisan angka footnote
      toolbar.appendChild(makeBtn('Sup', function(){ document.execCommand('superscript'); }));
      toolbar.appendChild(makeBtn('Sub', function(){ document.execCommand('subscript'); }));
      // List bullet dan numbering
      toolbar.appendChild(makeBtn('•', function(){ document.execCommand('insertUnorderedList'); }));
      toolbar.appendChild(makeBtn('1.', function(){ document.execCommand('insertOrderedList'); }));
      toolbar.appendChild(makeBtn('Link', function(){
        var url = prompt('Masukkan URL:', 'https://');
        if (url) document.execCommand('createLink', false, url);
      }));
      toolbar.appendChild(makeBtn('Clear', function(){ document.execCommand('removeFormat'); }));
      toolbar.appendChild(makeBtn('⯈', function(){ document.execCommand('justifyLeft'); }));
      toolbar.appendChild(makeBtn('⯀', function(){ document.execCommand('justifyCenter'); }));
      toolbar.appendChild(makeBtn('⯇', function(){ document.execCommand('justifyRight'); }));

      var container = document.createElement('div');
      container.appendChild(toolbar);
      container.appendChild(editorDiv);

      textarea.style.display = 'none';
      textarea.parentNode.insertBefore(container, textarea);

      var form = textarea.form;
      if (form) {
        form.addEventListener('submit', function(){
          // Kirim sebagai HTML agar format (sup/sub, list, link) tersimpan
          textarea.value = editorDiv.innerHTML;
        });
      }
    } catch (e) {
      console.warn('Init fallback SimpleEditor gagal:', e);
    }
  }

  // Editor Arab RTL
  var arabEl = document.getElementById('arabic_text');
  if (arabEl) {
    if (useCK()) {
      ClassicEditor.create(arabEl, {
        language: { ui: 'id', content: 'ar' },
        toolbar: {
          items: [
            'undo','redo','|','bold','italic','underline','link','alignment','|','bulletedList','numberedList','insertTable','removeFormat','codeBlock'
          ]
        },
        removePlugins: [
          'CKBox','CKFinder','EasyImage','CloudServices','RealTimeCollaborativeComments','RealTimeCollaborativeTrackChanges','RealTimeCollaborativeRevisionHistory','PresenceList','Comments','TrackChanges','RevisionHistory','WProofreader','MathType','SlashCommand','Template','DocumentOutline','FormatPainter','TableOfContents','ExportPdf','ExportWord'
        ]
      }).then(function(editor){
        try {
          editor.ui.view.editable.element.classList.add('rtl');
          editor.editing.view.change(function(writer){
            writer.setAttribute('dir','rtl', editor.editing.view.document.getRoot());
          });
        } catch (e) { console.warn('Set RTL gagal:', e); }
        attachSubmitSync(arabEl, editor);
      }).catch(function(e){ console.warn('CKEditor init arabic failed:', e); });
    } else {
      initFallbackSimpleEditor(arabEl, { rtl: true });
    }
  }

  // Editor LTR untuk translation dan footnotes
  document.querySelectorAll('#translation, #footnotes').forEach(function(el){
    if (useCK()) {
      ClassicEditor.create(el, {
        language: { ui: 'id', content: 'id' },
        toolbar: {
          items: [
            'undo','redo','|','bold','italic','underline','link','alignment','|','bulletedList','numberedList','insertTable','removeFormat','codeBlock'
          ]
        },
        removePlugins: [
          'CKBox','CKFinder','EasyImage','CloudServices','RealTimeCollaborativeComments','RealTimeCollaborativeTrackChanges','RealTimeCollaborativeRevisionHistory','PresenceList','Comments','TrackChanges','RevisionHistory','WProofreader','MathType','SlashCommand','Template','DocumentOutline','FormatPainter','TableOfContents','ExportPdf','ExportWord'
        ]
      }).then(function(editor){
        try { editor.ui.view.editable.element.classList.add('ltr'); } catch (e) {}
        attachSubmitSync(el, editor);
      }).catch(function(e){ console.warn('CKEditor init ltr failed:', e); });
    } else {
      initFallbackSimpleEditor(el, { rtl: false });
    }
  });
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Hadits ' . $hadith->hadith_number . ' - Bab ' . $hadith->chapter->chapter_number . ' - Buku ' . config('app.name', 'Syamail Muhammadiyah'))

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Navigation Header -->
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('chapters.show', $hadith->chapter->id) }}" class="text-emerald-600 hover:text-emerald-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke Bab {{ $hadith->chapter->chapter_number }}
                </a>
                
                <div class="flex items-center space-x-4">
                    <span class="bg-emerald-100 text-emerald-800 text-sm font-medium px-3 py-1 rounded-full">
                        Bab {{ $hadith->chapter->chapter_number }}
                    </span>
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                        Hadits {{ $hadith->hadith_number }}
                    </span>
                </div>
            </div>
            
            <!-- Font Size Controls -->
            <x-font-size-controls />
            
            <!-- Hadith Content -->
            <x-hadith-display :hadith="$hadith" :bookmark="$bookmark" :userNote="$userNote" />
            
            <!-- User Note (if exists) -->
            @auth
            <div id="user-note-section" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8 {{ $userNote ? '' : 'hidden' }}">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan Saya</h3>
                <p id="user-note-content" class="text-gray-700">{{ $userNote ? $userNote->note_content : '' }}</p>
            </div>
            @endauth
            
            <!-- Hadith Navigation -->
            <x-hadith-navigation
                :previousHadith="$previousHadith"
                :nextHadith="$nextHadith"
                :chapter="$hadith->chapter"
            />
        </div>
    </div>
</div>

<!-- Bookmark & Note Modals (hidden by default) -->
<div id="bookmark-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Bookmark</h3>
            <form id="bookmark-form">
                @csrf
                <input type="hidden" id="bookmark-hadith-id" name="hadith_id">
                <div class="mb-4">
                    <label for="bookmark-notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                    <textarea id="bookmark-notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Tambahkan catatan untuk bookmark ini..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-bookmark" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="note-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Catatan</h3>
            <form id="note-form">
                @csrf
                <input type="hidden" id="note-hadith-id" name="hadith_id">
                <div class="mb-4">
                    <label for="note-content" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea id="note-content" name="note_content" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Tulis catatan Anda di sini..." required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-note" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tipografi Arab dipusatkan di resources/css/app.css (kelas .arabic-text, .arabic-heading, .translation-text) -->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bookmark functionality
    const bookmarkBtn = document.getElementById('bookmark-btn');
    const bookmarkModal = document.getElementById('bookmark-modal');
    const bookmarkForm = document.getElementById('bookmark-form');
    const cancelBookmark = document.getElementById('cancel-bookmark');
    const bookmarkHadithId = document.getElementById('bookmark-hadith-id');
    
    bookmarkBtn.addEventListener('click', function() {
        const hadithId = this.getAttribute('data-hadith-id');
        const isBookmarked = this.textContent.trim() === 'Hapus Bookmark';
        
        if (isBookmarked) {
            // Remove bookmark
            if (confirm('Apakah Anda yakin ingin menghapus bookmark ini?')) {
                fetch(`/bookmarks/${hadithId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bookmarkBtn.textContent = 'Bookmark';
                        // UI diperbarui tanpa reload
                    }
                });
            }
        } else {
            // Show bookmark modal
            bookmarkHadithId.value = hadithId;
            bookmarkModal.classList.remove('hidden');
        }
    });
    
    cancelBookmark.addEventListener('click', function() {
        bookmarkModal.classList.add('hidden');
    });
    
    bookmarkForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/bookmarks', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bookmarkModal.classList.add('hidden');
                bookmarkBtn.textContent = 'Hapus Bookmark';
                // UI diperbarui tanpa reload
            }
        });
    });
    
    // Note functionality
    const noteBtn = document.getElementById('note-btn');
    const noteModal = document.getElementById('note-modal');
    const noteForm = document.getElementById('note-form');
    const cancelNote = document.getElementById('cancel-note');
    const noteHadithId = document.getElementById('note-hadith-id');
    
    noteBtn.addEventListener('click', function() {
        const hadithId = this.getAttribute('data-hadith-id');
        const hasNote = this.textContent.trim() === 'Edit Catatan';
        
        noteHadithId.value = hadithId;
        
        if (hasNote) {
            // Load existing note
            fetch(`/notes/${hadithId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('note-content').value = data.note_content;
                    noteModal.classList.remove('hidden');
                });
        } else {
            // Show empty note modal
            document.getElementById('note-content').value = '';
            noteModal.classList.remove('hidden');
        }
    });
    
    cancelNote.addEventListener('click', function() {
        noteModal.classList.add('hidden');
    });
    
    noteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const hadithId = noteHadithId.value;
        const hasNote = noteBtn.textContent.trim() === 'Edit Catatan';
        const url = hasNote ? `/notes/${hadithId}` : '/notes';
        const method = hasNote ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                noteModal.classList.add('hidden');
                noteBtn.textContent = 'Edit Catatan';
                const noteContentEl = document.getElementById('note-content');
                const userNoteContentEl = document.getElementById('user-note-content');
                const userNoteSectionEl = document.getElementById('user-note-section');
                if (userNoteContentEl) {
                    userNoteContentEl.textContent = noteContentEl.value;
                }
                if (userNoteSectionEl) {
                    userNoteSectionEl.classList.remove('hidden');
                }
            }
        });
    });
});
</script>
@endpush
@endsection
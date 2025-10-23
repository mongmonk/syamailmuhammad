<div class="bg-gray-50 p-3 sm:p-4 rounded-lg mb-6">        
    <div class="flex flex-wrap w-full justify-between items-center">
        
        <div class="flex flex-col flex-row items-center gap-2">
            <label for="arabic-font-size" class="text-sm font-medium text-gray-700 whitespace-nowrap">Font Arab:</label>
            <div class="flex items-center space-x-2">
                <button id="arabic-font-decrease" class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <span id="arabic-font-size-value" class="text-sm font-medium text-gray-700 w-8 text-center">24</span>
                <button id="arabic-font-increase" class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="flex flex-col flex-row items-center gap-2">
            <label for="translation-font-size" class="text-sm font-medium text-gray-700 whitespace-nowrap">Font Terjemahan:</label>
            <div class="flex items-center space-x-2">
                <button id="translation-font-decrease" class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <span id="translation-font-size-value" class="text-sm font-medium text-gray-700 w-8 text-center">20</span>
                <button id="translation-font-increase" class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>   
    <div class="w-full md:w-auto flex justify-end mt-2">
        <button id="reset-font-sizes" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm whitespace-nowrap">
            Reset Ukuran
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get saved font sizes or use defaults
    let arabicFontSize = parseInt(localStorage.getItem('arabicFontSize') || '24', 10);
    let translationFontSize = parseInt(localStorage.getItem('translationFontSize') || '18', 10);
    
    // Update display values
    document.getElementById('arabic-font-size-value').textContent = arabicFontSize;
    document.getElementById('translation-font-size-value').textContent = translationFontSize;
    
    // Apply font sizes
    applyFontSizes();
    
    // Arabic font size controls
    document.getElementById('arabic-font-increase').addEventListener('click', function() {
        if (arabicFontSize < 40) {
            arabicFontSize += 2;
            updateFontSize('arabic', arabicFontSize);
        }
    });
    
    document.getElementById('arabic-font-decrease').addEventListener('click', function() {
        if (arabicFontSize > 16) {
            arabicFontSize -= 2;
            updateFontSize('arabic', arabicFontSize);
        }
    });
    
    // Translation font size controls
    document.getElementById('translation-font-increase').addEventListener('click', function() {
        if (translationFontSize < 32) {
            translationFontSize += 2;
            updateFontSize('translation', translationFontSize);
        }
    });
    
    document.getElementById('translation-font-decrease').addEventListener('click', function() {
        if (translationFontSize > 14) {
            translationFontSize -= 2;
            updateFontSize('translation', translationFontSize);
        }
    });
    
    // Reset font sizes
    document.getElementById('reset-font-sizes').addEventListener('click', function() {
        arabicFontSize = 24;
        translationFontSize = 18;
        updateFontSize('arabic', arabicFontSize);
        updateFontSize('translation', translationFontSize);
    });
    
    function updateFontSize(type, size) {
        size = parseInt(size, 10);
        if (type === 'arabic') {
            arabicFontSize = size;
            localStorage.setItem('arabicFontSize', String(size));
            document.getElementById('arabic-font-size-value').textContent = size;
        } else {
            translationFontSize = size;
            localStorage.setItem('translationFontSize', String(size));
            document.getElementById('translation-font-size-value').textContent = size;
        }
        applyFontSizes();
    }
    
    function applyFontSizes() {
        // Apply to Arabic text
        const arabicTextElements = document.querySelectorAll('.arabic-text');
        arabicTextElements.forEach(element => {
            element.style.fontSize = arabicFontSize + 'px';
        });
        
        // Apply to translation text
        const translationTextElements = document.querySelectorAll('.translation-text');
        translationTextElements.forEach(element => {
            element.style.fontSize = translationFontSize + 'px';
        });
    }
});
</script>
@endpush
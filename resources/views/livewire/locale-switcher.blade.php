<div class="flex items-center gap-1">
    <button wire:click="switchLocale('en')"
            class="px-1.5 py-0.5 rounded text-xs font-medium transition {{ $locale === 'en' ? 'bg-blue-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        EN
    </button>
    <button wire:click="switchLocale('de')"
            class="px-1.5 py-0.5 rounded text-xs font-medium transition {{ $locale === 'de' ? 'bg-blue-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        DE
    </button>
    <button wire:click="switchLocale('hr')"
            class="px-1.5 py-0.5 rounded text-xs font-medium transition {{ $locale === 'hr' ? 'bg-blue-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        HR
    </button>
</div>

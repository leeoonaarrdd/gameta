@extends('layouts.app')

@section('title', 'Pertanyaan Umum')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-12 animate-on-scroll-bounce">
        <h1 class="text-4xl font-bold text-white uppercase tracking-wide animate-on-scroll">PERTANYAAN UMUM</h1>
    </div>
    
    <!-- Navigation Tabs -->
    @php
        $categoriesWithFaqs = $categories->filter(function($category) use ($faqsByCategory) {
            return isset($faqsByCategory[$category->name]) && $faqsByCategory[$category->name]->count() > 0;
        });
    @endphp
    
    @if($categoriesWithFaqs->count() > 0)
    <div class="flex justify-center mb-8 animate-on-scroll-fade">
        <div class="flex bg-gray-800/50 rounded-lg p-1 overflow-x-auto animate-on-scroll-slide-up">
            @foreach($categoriesWithFaqs as $index => $category)
            <button id="tab-{{ $category->name }}" class="px-6 py-3 cursor-pointer text-sm font-medium text-gray-300 rounded-lg hover:text-white transition-colors duration-200 {{ $index === 0 ? 'bg-purple-500 text-white' : '' }} animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}" onclick="showCategory('{{ $category->name }}')">
                {{ $category->name }}
            </button>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Questions Container -->
    @foreach($categoriesWithFaqs as $index => $category)
    <div id="questions-{{ $category->name }}" class="space-y-4 {{ $index === 0 ? '' : 'hidden' }} animate-on-scroll-fade">
        @if(isset($faqsByCategory[$category->name]))
            @foreach($faqsByCategory[$category->name] as $faqIndex => $faq)
            <div class="bg-gray-800/50 rounded-lg p-6 cursor-pointer hover:bg-gray-700/50 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($faqIndex + 1, 4) }}" onclick="toggleAnswer('answer-{{ $category->name }}-{{ $faqIndex }}')">
                <div class="flex items-center justify-between">
                    <p class="text-white text-lg">{{ $faq->pertanyaan }}</p>
                    <svg class="w-5 h-5 text-white transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                <div id="answer-{{ $category->name }}-{{ $faqIndex }}" class="hidden mt-4 pt-4 border-t border-gray-600/30">
                    <p class="text-gray-300 leading-relaxed">
                        {{ $faq->konten }}
                    </p>
                </div>
            </div>
            @endforeach
        @endif
    </div>
    @endforeach
    
    @if($categoriesWithFaqs->count() === 0)
    <div class="text-center py-8 animate-on-scroll-zoom">
        <div class="w-20 h-20 bg-gray-800/30 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 animate-on-scroll-rotate">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-gray-400 animate-on-scroll">Belum ada pertanyaan yang tersedia.</p>
    </div>
    @endif
</div>

<script>
function showCategory(categoryName) {
    // Hide all question containers
    const allContainers = document.querySelectorAll('[id^="questions-"]');
    allContainers.forEach(container => {
        container.classList.add('hidden');
    });
    
    // Show selected category container
    const selectedContainer = document.getElementById('questions-' + categoryName);
    if (selectedContainer) {
        selectedContainer.classList.remove('hidden');
    }
    
    // Update tab styles
    const allTabs = document.querySelectorAll('[id^="tab-"]');
    allTabs.forEach(tab => {
        tab.classList.remove('bg-purple-500', 'text-white');
        tab.classList.add('text-gray-300');
    });
    
    const selectedTab = document.getElementById('tab-' + categoryName);
    if (selectedTab) {
        selectedTab.classList.remove('text-gray-300');
        selectedTab.classList.add('bg-purple-500', 'text-white');
    }
}

function toggleAnswer(answerId) {
    const answer = document.getElementById(answerId);
    const questionDiv = answer.parentElement;
    const arrow = questionDiv.querySelector('svg');
    
    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        arrow.style.transform = 'rotate(90deg)';
    } else {
        answer.classList.add('hidden');
        arrow.style.transform = 'rotate(0deg)';
    }
}
</script>
@endsection 
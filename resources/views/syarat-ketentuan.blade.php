@extends('layouts.app')

@section('title', 'Syarat & Ketentuan')

@section('content')
@php
    $syaratKetentuan = \App\Models\Configuration::getValue('syarat_ketentuan', '');
@endphp

<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-12 animate-on-scroll-bounce">
        <h1 class="text-4xl font-bold text-white uppercase tracking-wide animate-on-scroll">SYARAT & KETENTUAN</h1>
    </div>
    
    <!-- Content -->
    <div class="space-y-8">
        @if(!empty($syaratKetentuan))
            <div class="prose prose-invert max-w-none animate-on-scroll-fade">
                <div class="text-gray-300 leading-relaxed animate-on-scroll-slide-up">{!! $syaratKetentuan !!}</div>
            </div>
        @else
            <div class="text-center py-12 animate-on-scroll-zoom">
                <div class="w-20 h-20 bg-gray-800/30 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 animate-on-scroll-rotate">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="text-gray-400 text-lg animate-on-scroll">Konten sedang dalam pengembangan</div>
                <p class="text-gray-500 mt-2 animate-on-scroll animate-on-scroll-delay-1">Silakan hubungi administrator untuk informasi lebih lanjut</p>
            </div>
        @endif
    </div>
</div>
@endsection 
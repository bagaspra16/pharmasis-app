@extends('layouts.app')

@section('title', 'Database Unavailable — Pharmasis')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-24 text-center">
    <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Database Temporarily Unavailable</h1>
    <p class="text-slate-500 mb-8 leading-relaxed">
        We're having trouble connecting to our database right now.<br>
        This is usually temporary — please try again in a moment.
    </p>
    <div class="flex gap-3 justify-center">
        <a href="{{ route('home') }}"
            class="px-6 py-2.5 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors">
            Go to Home
        </a>
        <button onclick="window.location.reload()"
            class="px-6 py-2.5 border border-slate-200 text-slate-600 font-medium rounded-xl hover:bg-slate-50 transition-colors">
            Try Again
        </button>
    </div>
</div>
@endsection
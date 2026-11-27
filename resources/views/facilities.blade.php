@extends('layouts.app')

@section('title', 'Nearby Healthcare Facilities')

@section('content')
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 pt-28 pb-10">
        @if (config('facilities.enabled'))
            @include('partials.facility-finder', ['category' => request('type') === 'emergency' ? 'emergency' : 'care'])
            @include('partials.facility-finder-script')
        @else
            <p class="text-sm text-slate-700">{{ trans('facilities.errors.unavailable') }}</p>
        @endif
    </div>
</div>
@endsection

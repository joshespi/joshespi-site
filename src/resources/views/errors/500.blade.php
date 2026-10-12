@extends('layouts.app')

@section('title', 'Something Broke')
@section('meta_description', 'Something went wrong on this end.')

@section('content')

<x-error-hero code="500" heading="Something broke on my end." :primary-href="url()->current()" primary-label="Try again">
    It's been logged. Try again in a minute — and if you were sending the intake form, it's worth checking your inbox for a confirmation before resending.
</x-error-hero>

@endsection

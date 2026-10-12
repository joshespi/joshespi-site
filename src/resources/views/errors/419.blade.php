@extends('layouts.app')

@section('title', 'Page Expired')
@section('meta_description', 'This page sat open too long and its session expired.')

@section('content')

<x-error-hero code="419" heading="This page expired." :primary-href="url()->previous('/')" primary-label="Go back">
    It was open long enough that its security token ran out. Go back, refresh, and send it again.
</x-error-hero>

@endsection

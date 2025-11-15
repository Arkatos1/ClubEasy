@extends('layouts.master')

@section('title', 'Club Calendar')

@section('content')
    <div id="app" class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6">Club Calendar</h1>
        <Calendar />
    </div>
@endsection

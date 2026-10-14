@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title)
@section('description', (string) $page->meta_description)

@section('content')
    @foreach ($page->visibleSections as $section)
        <x-section :section="$section" />
    @endforeach
@endsection

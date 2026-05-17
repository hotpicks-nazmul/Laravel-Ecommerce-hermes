@extends('install.layout')
@section('title', 'Welcome - Installer')
@php $currentStep = 1; @endphp

@section('content')
<div class="text-center step-content">
    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ config('app.name') }}</h1>
    <p class="text-gray-500 mb-8 max-w-md mx-auto">Welcome to the installation wizard. This guide will help you set up your application in just a few minutes.</p>

    <div class="bg-gray-50 rounded-xl p-6 mb-8 text-left">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Installation steps
        </h3>
        <ul class="space-y-2.5">
            <li class="flex items-start gap-3 text-sm text-gray-600">
                <span class="text-indigo-500 mt-0.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                <span>Check server requirements and file permissions</span>
            </li>
            <li class="flex items-start gap-3 text-sm text-gray-600">
                <span class="text-indigo-500 mt-0.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                <span>Configure database connection</span>
            </li>
            <li class="flex items-start gap-3 text-sm text-gray-600">
                <span class="text-indigo-500 mt-0.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                <span>Set app name, URL, environment, and debug mode</span>
            </li>
            <li class="flex items-start gap-3 text-sm text-gray-600">
                <span class="text-indigo-500 mt-0.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                <span>Create admin account</span>
            </li>
            <li class="flex items-start gap-3 text-sm text-gray-600">
                <span class="text-indigo-500 mt-0.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                <span>Run migrations, seed data, and finalize</span>
            </li>
        </ul>
    </div>

    <a href="{{ route('install.requirements') }}" class="btn-primary inline-flex">
        Start installation
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>
@endsection

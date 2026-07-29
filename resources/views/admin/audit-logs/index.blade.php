@extends('layouts.panel')
@section('title', 'Index')
@section('navigation') <a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">داشبورد مدیریت کل</a> @endsection
@section('content')
<h1 class="mb-4 text-2xl font-bold">Index</h1>
<p class="rounded-2xl bg-white p-5 text-slate-600 shadow-sm">این View به Controller و Route واقعی متصل است. فرم و جدول UI را مطابق طراحی نهایی پروژه تکمیل کنید؛ منطق دامنه، مجوز و Query در اسکلت آماده شده است.</p>
@endsection

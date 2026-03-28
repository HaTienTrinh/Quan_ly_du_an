@extends('layouts.admin')

@section('title', 'Bài viết')

@section('page_title', 'Bài viết')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Bài viết</span>
@endsection

@section('content')
    <div class="stat-card p-5 text-center text-muted">
        <i class="bi bi-newspaper d-block mb-3" style="font-size: 2.5rem; opacity: 0.35;"></i>
        <p class="mb-0">Nội dung quản lý bài viết sẽ hiển thị khi bạn kích hoạt route trong <code>web.php</code>.</p>
    </div>
@endsection

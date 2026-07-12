@extends('layouts.admin')

@section('title', 'Liên hệ')
@section('page_title', 'Quản lý liên hệ')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Liên hệ</span>
@endsection

@section('content')
    {{-- @if (session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif --}}

    @if ($unreadCount > 0)
        <div class="alert alert-info rounded-3 small">
            <i class="bi bi-envelope-fill me-1"></i>
            Có <strong>{{ $unreadCount }}</strong> liên hệ chưa đọc.
        </div>
    @endif

    <div class="d-flex gap-3 mb-4 flex-wrap align-items-center">
        <form method="get" action="{{ route('admin.contacts.index') }}" class="d-flex gap-2 flex-grow-1">
            <div class="input-group" style="max-width: 360px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                       placeholder="Tìm tên, email, nội dung...">
            </div>
            <select name="status" class="form-select" style="max-width: 180px;" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="unread"  @selected(request('status') === 'unread')>Chưa đọc</option>
                <option value="read"    @selected(request('status') === 'read')>Đã đọc</option>
                <option value="replied" @selected(request('status') === 'replied')>Đã phản hồi</option>
            </select>
            <button type="submit" class="btn btn-dark">Lọc</button>
            @if (request()->hasAny(['q', 'status']))
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </form>
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Người gửi</th>
                        <th>Tiêu đề</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="pe-4 text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contacts as $contact)
                        <tr class="{{ $contact->isUnread() ? 'fw-semibold' : '' }}">
                            <td class="ps-4">
                                <div>{{ $contact->name }}</div>
                                <div class="small text-muted">{{ $contact->email }}</div>
                                @if ($contact->phone)
                                    <div class="small text-muted">{{ $contact->phone }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 280px;">
                                    {{ $contact->subject ?: '(Không có tiêu đề)' }}
                                </div>
                                <div class="small text-muted text-truncate" style="max-width: 280px;">
                                    {{ $contact->message }}
                                </div>
                            </td>
                            <td class="small text-muted text-nowrap">
                                {{ $contact->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if ($contact->status === 'unread')
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="bi bi-envelope-fill me-1"></i>Chưa đọc
                                    </span>
                                @elseif ($contact->status === 'read')
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        <i class="bi bi-envelope-open me-1"></i>Đã đọc
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-reply-fill me-1"></i>Đã phản hồi
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.contacts.show', $contact) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Xem
                                    </a>
                                    <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                                          onsubmit="return confirm('Xóa liên hệ này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                Chưa có liên hệ nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contacts->hasPages())
            <div class="px-4 py-3 border-top">{{ $contacts->links() }}</div>
        @endif
    </div>
@endsection

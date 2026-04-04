@extends('layouts.admin')

@section('title', 'Tài khoản')

@section('page_title', 'Quản lý tài khoản')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Tài khoản</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center mb-4">
        <form method="get" action="{{ route('admin.users.index') }}" class="row g-2 flex-grow-1 flex-md-grow-0">
            <div class="col-auto flex-grow-1" style="min-width: 240px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                           placeholder="Tìm theo tên, email hoặc SĐT…" aria-label="Tìm kiếm tài khoản">
                </div>
            </div>
            <div class="col-auto">
                <select name="role" class="form-select" aria-label="Lọc vai trò" onchange="this.form.submit()">
                    <option value="">Mọi vai trò</option>
                    <option value="admin" @selected(request('role') === 'admin')>Quản trị</option>
                    <option value="customer" @selected(request('role') === 'customer')>Khách hàng</option>
                </select>
            </div>
            <div class="col-auto">
                <select name="active" class="form-select" aria-label="Lọc hoạt động" onchange="this.form.submit()">
                    <option value="">Mọi trạng thái</option>
                    <option value="1" @selected(request('active') === '1')>Đang hoạt động</option>
                    <option value="0" @selected(request('active') === '0')>Đã khóa</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dark">Lọc</button>
            </div>
            @if(request()->hasAny(['q', 'role', 'active']))
                <div class="col-auto">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                </div>
            @endif
        </form>
        <a href="{{ route('admin.users.trashed') }}" class="btn btn-outline-secondary align-self-start align-self-md-center">
            <i class="bi bi-trash3 me-1"></i> Đã xóa
        </a>
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 56px;">Ảnh</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th class="pe-4 text-end" style="min-width: 140px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="ps-4">
                                @if($u->avatar_url)
                                    <img src="{{ $u->avatar_url }}" alt="" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-muted" style="width:40px;height:40px;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $u->name }}</div>
                                @if($u->phone)
                                    <div class="small text-muted">{{ $u->phone }}</div>
                                @endif
                            </td>
                            <td class="small font-monospace">{{ $u->email }}</td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle">Quản trị</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Khách hàng</span>
                                @endif
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Hoạt động</span>
                                @else
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">Đã khóa</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-outline-primary" title="Chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="post" class="d-inline"
                                              onsubmit="return confirm('Chuyển tài khoản «{{ $u->name }}» vào thùng rác?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa mềm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                Không có tài khoản nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-4 py-3 border-top">{{ $users->links() }}</div>
        @endif
    </div>
@endsection

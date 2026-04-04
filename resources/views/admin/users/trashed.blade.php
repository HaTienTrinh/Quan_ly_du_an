@extends('layouts.admin')

@section('title', 'Tài khoản đã xóa')

@section('page_title', 'Tài khoản đã xóa')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none">Tài khoản</a>
    <span class="text-muted">/</span>
    <span>Đã xóa</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center mb-4">
        <form method="get" action="{{ route('admin.users.trashed') }}" class="d-flex gap-2 flex-grow-1 flex-md-grow-0" style="max-width: 420px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                       placeholder="Tìm trong thùng rác…">
            </div>
            <button type="submit" class="btn btn-dark">Tìm</button>
            @if(request()->filled('q'))
                <a href="{{ route('admin.users.trashed') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
            @endif
        </form>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary align-self-start align-self-md-center">
            <i class="bi bi-arrow-left me-1"></i> Về danh sách chính
        </a>
    </div>

    <div class="alert alert-warning border-0 rounded-3 small mb-4" role="alert">
        <i class="bi bi-info-circle me-1"></i>
        Tài khoản <strong>xóa mềm</strong> có thể <strong>khôi phục</strong> hoặc <strong>xóa vĩnh viễn</strong> (nếu không còn ràng buộc dữ liệu).
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Xóa lúc</th>
                        <th class="pe-4 text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $u->name }}</td>
                            <td class="small font-monospace">{{ $u->email }}</td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle">Quản trị</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Khách hàng</span>
                                @endif
                            </td>
                            <td class="small text-muted text-nowrap">{{ $u->deleted_at?->format('d/m/Y H:i') }}</td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.users.restore', $u->id) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success me-1">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Khôi phục
                                    </button>
                                </form>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.force-destroy', $u->id) }}" method="post" class="d-inline"
                                          onsubmit="return confirm('Xóa vĩnh viễn «{{ $u->email }}»? Không thể hoàn tác.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-octagon me-1"></i> Xóa vĩnh viễn
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-trash d-block mb-2" style="font-size: 2rem;"></i>
                                Thùng rác trống. <a href="{{ route('admin.users.index') }}">Quay lại danh sách</a>
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

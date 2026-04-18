@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('page_title', 'Sửa sản phẩm')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.products.index') }}" class="text-muted text-decoration-none">Sản phẩm</a>
    <span class="text-muted">/</span>
    <span>Sửa</span>
@endsection

@section('content')
    <div class="stat-card p-4 p-md-5" style="max-width: 720px;">
        <form action="{{ route('admin.products.update', $product) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="category_id" class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-medium">Mô tả</label>
                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="price" class="form-label fw-medium">Giá gốc <span class="text-danger">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01"
                           class="form-control @error('price') is-invalid @enderror" required min="0">
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="sale_price" class="form-label fw-medium">Giá khuyến mãi</label>
                    <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01"
                           class="form-control @error('sale_price') is-invalid @enderror" min="0">
                    @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="stock" class="form-label fw-medium">Tồn kho</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}"
                           class="form-control bg-light" readonly
                           title="Tự động tính tổng từ số lượng các size">
                    <div class="form-text">Tự động cập nhật theo tổng số lượng các size.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="thumbnail" class="form-label fw-medium">Ảnh sản phẩm</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="form-control @error('thumbnail') is-invalid @enderror">
                    @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">PNG, JPG, WebP — tối đa 4MB.</div>
                </div>

                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                               @checked(old('is_active', $product->is_active))>
                        <label class="form-check-label" for="is_active">Hiển thị sản phẩm</label>
                    </div>
                </div>

                @php
                    $oldSizes = old('sizes', $product->sizes->map(fn ($s) => ['name' => $s->name, 'stock' => $s->stock])->all());
                    if ($oldSizes === []) {
                        $oldSizes = [['name' => '', 'stock' => 0]];
                    }
                @endphp

                <div class="col-12">
                    <div class="border rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="form-label fw-medium mb-1">Size sản phẩm</label>
                                <div class="form-text">Danh sách size khách có thể chọn khi mua hoặc đổi hàng.</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-size-row">Thêm size</button>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-7"><small class="text-muted fw-semibold">Tên size</small></div>
                            <div class="col-md-3"><small class="text-muted fw-semibold">Số lượng</small></div>
                        </div>

                        <div id="size-rows" class="d-flex flex-column gap-2">
                            @foreach ($oldSizes as $index => $size)
                                <div class="row g-2 align-items-center size-row">
                                    <div class="col-md-7">
                                        <input type="text" name="sizes[{{ $index }}][name]"
                                            value="{{ $size['name'] ?? '' }}"
                                            class="form-control @error('sizes.' . $index . '.name') is-invalid @enderror"
                                            placeholder="Tên size, ví dụ: S, M, L, XL, 38, 39...">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="sizes[{{ $index }}][stock]"
                                            value="{{ $size['stock'] ?? 0 }}"
                                            class="form-control @error('sizes.' . $index . '.stock') is-invalid @enderror"
                                            placeholder="Số lượng" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-size-row">Xóa</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($product->thumbnail_url)
                    <div class="col-12">
                        <p class="mb-2 fw-medium">Ảnh hiện tại</p>
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="rounded border" style="max-width: 240px; object-fit: cover;">
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                    Cập nhật sản phẩm
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>

    <template id="size-row-template">
        <div class="row g-2 align-items-center size-row">
            <div class="col-md-7">
                <input type="text" data-field="name" class="form-control" placeholder="Tên size, ví dụ: S, M, L, XL, 38, 39...">
            </div>
            <div class="col-md-3">
                <input type="number" data-field="stock" value="0" class="form-control" placeholder="Số lượng" min="0">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 remove-size-row">Xóa</button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rowsContainer = document.getElementById('size-rows');
            const template = document.getElementById('size-row-template');
            const addButton = document.getElementById('add-size-row');
            const stockInput = document.getElementById('stock');

            function calcTotalStock() {
                let total = 0;
                rowsContainer.querySelectorAll('input[type="number"]').forEach(input => {
                    total += parseInt(input.value) || 0;
                });
                stockInput.value = total;
            }

            function reindexRows() {
                Array.from(rowsContainer.querySelectorAll('.size-row')).forEach((row, index) => {
                    row.querySelector('input[type="text"]').setAttribute('name', `sizes[${index}][name]`);
                    row.querySelector('input[type="number"]').setAttribute('name', `sizes[${index}][stock]`);
                });
            }

            addButton.addEventListener('click', function () {
                const fragment = template.content.cloneNode(true);
                rowsContainer.appendChild(fragment);
                reindexRows();
                calcTotalStock();
            });

            rowsContainer.addEventListener('click', function (event) {
                if (!event.target.classList.contains('remove-size-row')) return;

                const rows = rowsContainer.querySelectorAll('.size-row');

                if (rows.length === 1) {
                    rows[0].querySelector('input[type="text"]').value = '';
                    rows[0].querySelector('input[type="number"]').value = 0;
                    calcTotalStock();
                    return;
                }

                event.target.closest('.size-row')?.remove();
                reindexRows();
                calcTotalStock();
            });

            rowsContainer.addEventListener('input', function (event) {
                if (event.target.type === 'number') calcTotalStock();
            });

            reindexRows();
            calcTotalStock();
        });
    </script>
@endsection

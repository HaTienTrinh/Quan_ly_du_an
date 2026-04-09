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
                    <label for="stock" class="form-label fw-medium">Tồn kho <span class="text-danger">*</span></label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}"
                           class="form-control @error('stock') is-invalid @enderror" required min="0">
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    $oldColors = old('colors', $product->colors->map(fn ($color) => [
                        'name' => $color->name,
                        'hex_code' => $color->hex_code ?: '#000000',
                    ])->all());

                    if ($oldColors === []) {
                        $oldColors = [['name' => '', 'hex_code' => '#000000']];
                    }
                @endphp

                <div class="col-12">
                    <div class="border rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="form-label fw-medium mb-1">Màu máy</label>
                                <div class="form-text">Danh sách màu khách có thể chọn khi mua hoặc đổi hàng.</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-color-row">Thêm màu</button>
                        </div>

                        <div id="color-rows" class="d-flex flex-column gap-2">
                            @foreach ($oldColors as $index => $color)
                                <div class="row g-2 align-items-center color-row">
                                    <div class="col-md-6">
                                        <input type="text" name="colors[{{ $index }}][name]"
                                            value="{{ $color['name'] ?? '' }}"
                                            class="form-control @error('colors.' . $index . '.name') is-invalid @enderror"
                                            placeholder="Tên màu, ví dụ: Titan tự nhiên">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="color" name="colors[{{ $index }}][hex_code]"
                                            value="{{ $color['hex_code'] ?: '#000000' }}"
                                            class="form-control form-control-color w-100 @error('colors.' . $index . '.hex_code') is-invalid @enderror"
                                            title="Mã màu">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-color-row">Xóa</button>
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

    <template id="color-row-template">
        <div class="row g-2 align-items-center color-row">
            <div class="col-md-6">
                <input type="text" data-field="name" class="form-control" placeholder="Tên màu, ví dụ: Titan tự nhiên">
            </div>
            <div class="col-md-4">
                <input type="color" data-field="hex_code" value="#000000" class="form-control form-control-color w-100" title="Mã màu">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 remove-color-row">Xóa</button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rowsContainer = document.getElementById('color-rows');
            const template = document.getElementById('color-row-template');
            const addButton = document.getElementById('add-color-row');

            if (!rowsContainer || !template || !addButton) {
                return;
            }

            function reindexRows() {
                Array.from(rowsContainer.querySelectorAll('.color-row')).forEach((row, index) => {
                    row.querySelector('input[type="text"]').setAttribute('name', `colors[${index}][name]`);
                    row.querySelector('input[type="color"]').setAttribute('name', `colors[${index}][hex_code]`);
                });
            }

            addButton.addEventListener('click', function () {
                const fragment = template.content.cloneNode(true);
                rowsContainer.appendChild(fragment);
                reindexRows();
            });

            rowsContainer.addEventListener('click', function (event) {
                if (!event.target.classList.contains('remove-color-row')) {
                    return;
                }

                const rows = rowsContainer.querySelectorAll('.color-row');

                if (rows.length === 1) {
                    rows[0].querySelector('input[type="text"]').value = '';
                    rows[0].querySelector('input[type="color"]').value = '#000000';
                    return;
                }

                event.target.closest('.color-row')?.remove();
                reindexRows();
            });

            reindexRows();
        });
    </script>
@endsection

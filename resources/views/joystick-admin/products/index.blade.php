@extends('joystick-admin.layout')

@section('content')
  <h2 class="page-header">Продукты</h2>

  @include('joystick-admin.partials.alerts')

  <p class="text-right">
    <a href="/admin/products/create" class="btn btn-success btn-sm">Добавить</a>
  </p>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <th>Картинка</th>
          <th>Название</th>
          <th>Категория</th>
          <th>Компания</th>
          <th>Номер</th>
          <th>Язык</th>
          <th>Статус</th>
          <th class="text-right">Функции</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($products as $key => $product)
          <tr>
            <?php $images = unserialize($product->images); ?>
            <td><img src="/img/products/{{ $product->path.'/'.$images[$key]['mini_image'] }}" class="img-responsive" style="width:80px;height:80px;"></td>
            <td>{{ $product->title }}</td>
            <td>{{ $product->category->title }}</td>
            <td>{{ $product->company->title }}</td>
            <td>{{ $product->sort_id }}</td>
            <td>{{ $product->lang }}</td>
            @if ($product->status != 0)
              <td class="text-success">Активен</td>
            @else
              <td class="text-danger">Неактивен</td>
            @endif
            <td class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="#" title="Просмотр товара" target="_blank"><i class="material-icons md-18">link</i></a>
              <a class="btn btn-link btn-xs" href="{{ route('products.edit', $product->id) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form class="btn-delete" method="POST" action="{{ route('products.destroy', $product->id) }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4">Нет записи</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $products->links() }}

@endsection

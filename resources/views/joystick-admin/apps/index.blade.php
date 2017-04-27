@extends('joystick-admin.layout')

@section('content')
  <h1 class="page-header">Заявки</h1>

  @include('joystick-admin.partials.alerts')

  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <th>№</th>
          <th>Имя</th>
          <th>Email</th>
          <th>Номер</th>
          <th>Текст</th>
          <th class="text-right">Функции</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @forelse ($apps as $app)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $app->name }}</td>
            <td>{{ $app->email }}</td>
            <td>{{ $app->number }}</td>
            <td>{{ $app->text }}</td>
            <td class="text-right">
              <a class="btn btn-link btn-xs" href="{{ url($page->slug) }}" title="Просмотр страницы" target="_blank"><i class="material-icons md-18">link</i></a>
              <form method="POST" action="{{ route('apps.destroy', $app->id) }}" accept-charset="UTF-8" class="btn-delete">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5">Нет записи</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $apps->links() }}

@endsection
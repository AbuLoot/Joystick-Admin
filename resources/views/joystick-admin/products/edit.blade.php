@extends('joystick-admin.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('joystick-admin.partials.alerts')

  <p class="text-right">
    <a href="/admin/products" class="btn btn-primary btn-sm">Назад</a>
  </p>
  <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="PUT">
    {!! csrf_field() !!}

    <div class="form-group">
      <label for="title">Название</label>
      <input type="text" class="form-control" id="title" name="title" minlength="5" maxlength="80" value="{{ (old('title')) ? old('title') : $product->title }}" required>
    </div>
    <div class="form-group">
      <label for="slug">Slug</label>
      <input type="text" class="form-control" id="slug" name="slug" minlength="2" maxlength="80" value="{{ (old('slug')) ? old('slug') : $product->slug }}">
    </div>
    <div class="form-group">
      <label for="category_id">Категории</label>
      <select id="category_id" name="category_id" class="form-control">
        <option value=""></option>
        <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $product) { ?>
          <?php foreach ($nodes as $node) : ?>
            <?php if ($node->id == $product->category_id) : ?>
              <option value="{{ $node->id }}" selected>{{ PHP_EOL.$prefix.' '.$node->title }}</option>
            <?php else : ?>
              <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
            <?php endif; ?>
            <?php $traverse($node->children, $prefix.'&nbsp;&nbsp;'); ?>
          <?php endforeach; ?>
        <?php }; ?>
        <?php $traverse($categories); ?>
      </select>
    </div>
    <div class="form-group">
      <label for="company_id">Компания</label>
      <select id="company_id" name="company_id" class="form-control" required>
        <option value=""></option>
        @foreach($companies as $company)
          @if ($company->id == $product->company)
            <option value="{{ $company->id }}" selected>{{ $company->title }}</option>
          @else
            <option value="{{ $company->id }}">{{ $company->title }}</option>
          @endif
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="price">Цена</label>
      <div class="input-group">
        <input type="text" class="form-control" id="price" name="price" maxlength="10" value="{{ (old('price')) ? old('price') : $product->price }}" required>
        <div class="input-group-addon">〒</div>
      </div>
    </div>
    <div class="form-group">
      <label for="days">Срок доставки</label>
      <div class="input-group">
        <input type="text" class="form-control" id="days" name="days" maxlength="10" value="{{ (old('days')) ? old('days') : $product->days}}">
        <div class="input-group-addon">дней</div>
      </div>
    </div>
    <div class="form-group">
      <label for="count">Количество товара</label>
      <input type="number" class="form-control" id="count" name="count" minlength="5" maxlength="80" value="{{ (old('count')) ? old('count') : 0 }}">
    </div>
    <div class="form-group">
      <label for="condition">Состояние товара</label>
      <select class="form-control" name="condition" id="condition">
        <option value="1">Новый</option>
        <option value="2">Бывший в употреблении</option>
      </select>
    </div>
    <div class="form-group">
      <label for="presense">Статус товара</label>
      <select class="form-control" name="presense" id="presense">
        <option value="1">В наличии</option>
        <option value="2">В наличии нет</option>
      </select>
    </div>
    <div class="form-group">
      <label for="options_id">Опции</label>
      <select id="options_id" name="options_id[]" class="form-control" multiple>
        <option value=""></option>
        @foreach($options as $option)
          @if ($product->options->contains($option->id))
            <option value="{{ $option->id }}" selected>{{ $option->title }}</option>
          @else
            <option value="{{ $option->id }}">{{ $option->title }}</option>
          @endif
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="description">Описание</label>
      <textarea class="form-control" id="description" name="description" rows="6" maxlength="2000">{{ (old('description')) ? old('description') : $product->description }}</textarea>
    </div>
    <div class="form-group">
      <label for="characteristic">Характеристика</label>
      <textarea class="form-control" id="characteristic" name="characteristic" rows="6" maxlength="2000">{{ (old('characteristic')) ? old('characteristic') : $product->characteristic }}</textarea>
    </div>
    <div class="form-group">
      <label>Галерея</label><br>
      <?php $images = unserialize($product->images); ?>
      @for ($i = 0; $i < 6; $i++)
        @if (isset($images[$i]))
          <div class="fileinput fileinput-new" data-provides="fileinput">
            <div class="fileinput-new thumbnail" style="width:300px;height:200px;">
              <img src="/img/products/{{ $product->path.'/'.$images[$i]['mini_image'] }}">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="width:300px;height:200px;" data-trigger="fileinput"></div>
            <div>
              <span class="btn btn-default btn-sm btn-file">
                <span class="fileinput-new"><i class="glyphicon glyphicon-folder-open"></i>&nbsp; Изменить</span>
                <span class="fileinput-exists"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;</span>
                <input type="file" name="images[]" accept="image/*">
              </span>
              <a href="#" class="btn btn-default btn-sm fileinput-exists" data-dismiss="fileinput"><i class="glyphicon glyphicon-trash"></i> Удалить</a>
            </div>
          </div>
        @else
          <div class="fileinput fileinput-new" data-provides="fileinput">
            <div class="fileinput-preview thumbnail" style="width:300px;height:200px;" data-trigger="fileinput"></div>
            <div>
              <span class="btn btn-default btn-sm btn-file">
                <span class="fileinput-new"><i class="glyphicon glyphicon-folder-open"></i>&nbsp; Выбрать</span>
                <span class="fileinput-exists"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;</span>
                <input type="file" name="images[]" accept="image/*">
              </span>
              <a href="#" class="btn btn-default btn-sm fileinput-exists" data-dismiss="fileinput"><i class="glyphicon glyphicon-trash"></i> Удалить</a>
            </div>
          </div>
        @endif
      @endfor
    </div>
    <div class="form-group">
      <label for="lang">Язык</label>
      <select id="lang" name="lang" class="form-control" required>
        <option value=""></option>
        @foreach($languages as $language)
          @if ($product->lang == $language->slug)
            <option value="{{ $language->slug }}" selected>{{ $language->title }}</option>
          @else
            <option value="{{ $language->slug }}">{{ $language->title }}</option>
          @endif
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="status">Статус:</label>
      <label>
        <input type="checkbox" id="status" name="status" @if ($product->status == 1) checked @endif> Активен
      </label>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Обновить</button>
    </div>
  </form>
@endsection

@section('head')
  <link href="/joystick/css/jasny-bootstrap.min.css" rel="stylesheet">

  <script src='//cdn.tinymce.com/4/tinymce.min.js'></script>
  <script>
    tinymce.init({
      selector: 'textarea',
      height: 300,
      menubar: false,
      plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste code'
      ],
      toolbar: 'code undo redo | table insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
      // content_css: '//www.tinymce.com/css/codepen.min.css'
    });
  </script>
@endsection

@section('scripts')
  <script src="/joystick/js/jasny-bootstrap.js"></script>
@endsection

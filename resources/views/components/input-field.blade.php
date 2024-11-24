@isset($label)
    <label @isset($id) for="{{ $id }}" @endisset>{{ $label }}</label>
@endisset

<input type="{{ $type }}" 
       class="form-control" 
       @isset($name) name="{{ $name }}" @endisset
       @isset($id) id="{{ $id }}" @endisset 
       @isset($value) value="{{ $value }}" @endisset 
       @isset($attr) {{ $attr }} @endisset>
    
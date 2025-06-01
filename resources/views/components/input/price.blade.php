@props([
    'price' => '',
    'span' => 'руб.',
])
<div class="input-group">
    <span class="input-group-text">{{ $span }}</span>
    <input type="number" name="price" id="price" required step="1" min="0" max="65000" value="{{ old('price')?? $price?? '' }}" class="form-control text-end {{ $errors->has('price') ? 'is-invalid' : '' }}" list="datalistOptions">
    <datalist id="datalistOptions">
        <option value="500">
        <option value="600">
        <option value="700">
        <option value="800">
        <option value="900">
    </datalist>
</div>

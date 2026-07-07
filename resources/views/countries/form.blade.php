<div class="mb-2">
    <label>Código</label>
    <input type="text" name="code" class="form-control"
           value="{{ old('code', $country->code ?? '') }}">
</div>

<div class="mb-2">
    <label>ISO3</label>
    <input type="text" name="iso3" class="form-control"
           value="{{ old('iso3', $country->iso3 ?? '') }}">
</div>

<div class="mb-2">
    <label>Nombre</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $country->name ?? '') }}">
</div>

<div class="mb-2">
    <label>Nacionalidad</label>
    <input type="text" name="nationality" class="form-control"
           value="{{ old('nationality', $country->nationality ?? '') }}">
</div>

<div class="mb-2">
    <label>Código telefónico</label>
    <input type="text" name="phone_code" class="form-control"
           value="{{ old('phone_code', $country->phone_code ?? '') }}">
</div>

<div class="mb-2">
    <label>Moneda</label>
    <input type="text" name="currency_code" class="form-control"
           value="{{ old('currency_code', $country->currency_code ?? '') }}">
</div>

<div class="mb-2">
    <label>Idioma</label>
    <input type="text" name="language" class="form-control"
           value="{{ old('language', $country->language ?? 'es') }}">
</div>

<div class="form-check mb-2">
    <input type="checkbox" name="active" value="1"
        class="form-check-input"
        {{ old('active', $country->active ?? true) ? 'checked' : '' }}>

    <label class="form-check-label">Activo</label>
</div>

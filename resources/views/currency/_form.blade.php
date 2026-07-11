<x-cn.group columns="2">

    <x-cn.field>

        <x-cn.label for="code" required>
            Código
        </x-cn.label>

        <x-cn.input id="code" name="code" :value="old('code', $currency->code ?? '')" maxlength="10" required />

        <x-cn.error for="code" />

    </x-cn.field>

    <x-cn.field>

        <x-cn.label for="name" required>
            Nombre
        </x-cn.label>

        <x-cn.input id="name" name="name" :value="old('name', $currency->name ?? '')" required />

        <x-cn.error for="name" />

    </x-cn.field>

    <x-cn.field>

        <x-cn.label for="symbol">
            Símbolo
        </x-cn.label>

        <x-cn.input id="symbol" name="symbol" :value="old('symbol', $currency->symbol ?? '')" maxlength="5" />

        <x-cn.help>
            Ejemplo: $, €, ¥
        </x-cn.help>

        <x-cn.error for="symbol" />

    </x-cn.field>

    <x-cn.field>

        <x-cn.label for="status">
            Estado
        </x-cn.label>

        <x-cn.select id="status" name="status">
            <option value="1" @selected(old('status', $currency->status ?? true))>
                Activa
            </option>

            <option value="0" @selected(!old('status', $currency->status ?? true))>
                Inactiva
            </option>

        </x-cn.select>

        <x-cn.error for="status" />

    </x-cn.field>

</x-cn.group>

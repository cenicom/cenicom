<?php

declare(strict_types=1);

namespace Tests\Feature\Database\Seeders;

//se App\Models\Currency;
use App\Modules\Currency\Models\Currency;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CurrencySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_currency_seeder_creates_cop(): void
    {
        $this->seed(CurrencySeeder::class);

        $this->assertDatabaseHas('currencies', [
            'code' => 'COP',
            'name' => 'Peso colombiano',
            'precision' => 2,
            'symbol' => '$',
            'decimal_mark' => ',',
            'thousands_separator' => '.',
        ]);
    }

    public function test_currency_seeder_creates_usd(): void
    {
        $this->seed(CurrencySeeder::class);

        $this->assertDatabaseHas('currencies', [
            'code' => 'USD',
            'name' => 'Dólar estadounidense',
            'precision' => 2,
            'symbol' => '$',
            'decimal_mark' => '.',
            'thousands_separator' => ',',
        ]);
    }

    public function test_currency_seeder_is_idempotent(): void
    {
        $this->seed(CurrencySeeder::class);

        $firstCount = Currency::query()->count();

        $this->seed(CurrencySeeder::class);

        $secondCount = Currency::query()->count();

        $this->assertSame($firstCount, $secondCount);
        $this->assertSame(2, $secondCount);
    }

    public function test_currency_seeder_reconciles_master_attributes(): void
    {
        Currency::query()->create([
            'name' => 'Nombre incorrecto',
            'code' => 'COP',
            'precision' => 0,
            'symbol' => 'X',
            'decimal_mark' => '.',
            'thousands_separator' => ',',
        ]);

        $this->seed(CurrencySeeder::class);

        $currency = Currency::query()
            ->where('code', 'COP')
            ->firstOrFail();

        $this->assertSame('Peso colombiano', $currency->name);
        $this->assertSame(2, $currency->precision);
        $this->assertSame('$', $currency->symbol);
        $this->assertSame(',', $currency->decimal_mark);
        $this->assertSame('.', $currency->thousands_separator);

        $this->assertSame(
            1,
            Currency::query()->where('code', 'COP')->count()
        );
    }
}

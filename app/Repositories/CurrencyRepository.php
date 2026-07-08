<?php
class CurrencyRepository
{
    public function paginate()
    {
        return Currency::paginate();
    }

    public function create(array $data)
    {
        return Currency::create($data);
    }

    public function update(Currency $currency,array $data)
    {
        $currency->update($data);

        return $currency;
    }

    public function delete(Currency $currency)
    {
        return $currency->delete();
    }
}

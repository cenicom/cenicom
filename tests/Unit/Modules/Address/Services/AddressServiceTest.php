<?php

declare(strict_types=1);

use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Domain\Contracts\GeographicReferenceInterface;
use App\Modules\Address\Models\Address;
use App\Modules\Address\Services\AddressService;
use Illuminate\Database\Eloquent\Model;
use Mockery\MockInterface;

beforeEach(function (): void {
    $this->repository = Mockery::mock(AddressRepositoryInterface::class);
    $this->geographicReference = Mockery::mock(GeographicReferenceInterface::class);

    $this->service = new AddressService(
        $this->repository,
        $this->geographicReference,
    );
});

afterEach(function (): void {
    Mockery::close();
});

it('creates an address when the geographic combination is coherent', function (): void {
    $attributes = [
        'country_id' => 1,
        'state_id' => 1,
        'city_id' => 1,
        'address' => 'Calle 1',
        'neighborhood' => 'Centro',
    ];

    $this->geographicReference
        ->shouldReceive('isCoherent')
        ->once()
        ->with(1, 1, 1)
        ->andReturn(true);

    $created = new Address();

    $this->repository
        ->shouldReceive('create')
        ->once()
        ->with($attributes)
        ->andReturn($created);

    $result = $this->service->create($attributes);

    expect($result)->toBe($created);
});

it('rejects create when the geographic combination is incoherent', function (): void {
    $attributes = [
        'country_id' => 1,
        'state_id' => 1,
        'city_id' => 7,
    ];

    $this->geographicReference
        ->shouldReceive('isCoherent')
        ->once()
        ->with(1, 1, 7)
        ->andReturn(false);

    $this->repository
        ->shouldNotReceive('create');

    expect(fn () => $this->service->create($attributes))
        ->toThrow(InvalidArgumentException::class);
});

it('updates an address when the resulting geographic combination is coherent', function (): void {
    $id = 'address-1';

    $current = new Address([
        'country_id' => 1,
        'state_id' => 1,
        'city_id' => 1,
    ]);

    $attributes = [
        'city_id' => 2,
    ];

    $this->repository
        ->shouldReceive('findOrFail')
        ->once()
        ->with($id)
        ->andReturn($current);

    $this->geographicReference
        ->shouldReceive('isCoherent')
        ->once()
        ->with(1, 1, 2)
        ->andReturn(true);

    $this->repository
        ->shouldReceive('update')
        ->once()
        ->with($id, $attributes)
        ->andReturn(true);

    expect(
        $this->service->update($id, $attributes)
    )->toBeTrue();
});

it('rejects update when the resulting geographic combination is incoherent', function (): void {
    $id = 'address-1';

    $current = new Address([
        'country_id' => 1,
        'state_id' => 1,
        'city_id' => 1,
    ]);

    $attributes = [
        'city_id' => 7,
    ];

    $this->repository
        ->shouldReceive('findOrFail')
        ->once()
        ->with($id)
        ->andReturn($current);

    $this->geographicReference
        ->shouldReceive('isCoherent')
        ->once()
        ->with(1, 1, 7)
        ->andReturn(false);

    $this->repository
        ->shouldNotReceive('update');

    expect(fn () => $this->service->update($id, $attributes))
        ->toThrow(InvalidArgumentException::class);
});

it('validates the complete resulting combination on partial update', function (): void {
    $id = 'address-1';

    $current = new Address([
        'country_id' => 1,
        'state_id' => 1,
        'city_id' => 1,
    ]);

    $attributes = [
        'state_id' => 2,
    ];

    $this->repository
        ->shouldReceive('findOrFail')
        ->once()
        ->with($id)
        ->andReturn($current);

    $this->geographicReference
        ->shouldReceive('isCoherent')
        ->once()
        ->with(1, 2, 1)
        ->andReturn(true);

    $this->repository
        ->shouldReceive('update')
        ->once()
        ->with($id, $attributes)
        ->andReturn(true);

    expect(
        $this->service->update($id, $attributes)
    )->toBeTrue();
});

it('propagates geographic reference technical failures', function (): void {
    $attributes = [
        'country_id' => 1,
        'state_id' => 1,
        'city_id' => 1,
    ];

    $failure = new RuntimeException(
        'Geographic reference infrastructure failure.'
    );

    $this->geographicReference
        ->shouldReceive('isCoherent')
        ->once()
        ->with(1, 1, 1)
        ->andThrow($failure);

    $this->repository
        ->shouldNotReceive('create');

    expect(fn () => $this->service->create($attributes))
        ->toThrow(RuntimeException::class);
});

it('accepts numeric string identifiers when creating an address', function (): void {
    $attributes = [
        'country_id' => '1',
        'state_id' => '1',
        'city_id' => '1',
    ];

    $this->geographicReference
        ->shouldReceive('isCoherent')
        ->once()
        ->with(1, 1, 1)
        ->andReturn(true);

    $created = new Address();

    $this->repository
        ->shouldReceive('create')
        ->once()
        ->with($attributes)
        ->andReturn($created);

    expect($this->service->create($attributes))->toBe($created);
});

it('accepts numeric string identifiers on partial update', function (): void {
    $id = 'address-1';

    $current = new Address([
        'country_id' => 1,
        'state_id' => 1,
        'city_id' => 1,
    ]);

    $attributes = [
        'city_id' => '2',
    ];

    $this->repository
        ->shouldReceive('findOrFail')
        ->once()
        ->with($id)
        ->andReturn($current);

    $this->geographicReference
        ->shouldReceive('isCoherent')
        ->once()
        ->with(1, 1, 2)
        ->andReturn(true);

    $this->repository
        ->shouldReceive('update')
        ->once()
        ->with($id, $attributes)
        ->andReturn(true);

    expect($this->service->update($id, $attributes))->toBeTrue();
});

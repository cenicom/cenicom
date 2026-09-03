<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Http\Controllers;

use App\Modules\Institution\Actions\InstitutionAction;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use App\Modules\Institution\Domain\Entity\Institution as DomainInstitution;
use App\Modules\Institution\Http\Controllers\InstitutionController;
use App\Modules\Institution\Http\Requests\StoreInstitutionRequest;
use App\Modules\Institution\Http\Requests\UpdateInstitutionRequest;
use App\Modules\Institution\Models\Institution;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Routing\Redirector;
use Mockery;
use Tests\TestCase;

final class InstitutionControllerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        Route::get(
            '/institutions',
            static fn() => 'ok',
        )->name('institutions.index');
    }
    public function test_index_delegates_pagination_to_service(): void
    {
        $paginator = new LengthAwarePaginator(
            items: [],
            total: 0,
            perPage: 15,
            currentPage: 1,
        );

        $service = Mockery::mock(
            InstitutionServiceInterface::class,
        );

        $service
            ->shouldReceive('paginate')
            ->once()
            ->with(15)
            ->andReturn($paginator);

        $view = $this->mockView();

        $viewFactory = Mockery::mock(ViewFactory::class);

        $viewFactory
            ->shouldReceive('make')
            ->once()
            ->with(
                'institutions.index',
                [
                    'institutions' => $paginator,
                ],
                [],
            )
            ->andReturn($view);

        $this->app->instance(
            ViewFactory::class,
            $viewFactory,
        );

        $controller = $this->controller(
            service: $service,
        );

        $result = $controller->index();

        $this->assertSame($view, $result);
    }

    public function test_create_returns_create_view(): void
    {
        $view = $this->mockView();

        $viewFactory = Mockery::mock(ViewFactory::class);

        $viewFactory
            ->shouldReceive('make')
            ->once()
            ->with(
                'institutions.create',
                [],
                [],
            )
            ->andReturn($view);

        $this->app->instance(
            ViewFactory::class,
            $viewFactory,
        );

        $controller = $this->controller();

        $result = $controller->create();

        $this->assertSame($view, $result);
    }

    public function test_store_delegates_validated_data_to_action(): void
    {
        $creator = Mockery::mock(
            InstitutionCreatorInterface::class,
        );

        $repository = Mockery::mock(
            InstitutionRepositoryInterface::class,
        );

        $service = Mockery::mock(
            InstitutionServiceInterface::class,
        );

        $domainInstitution = new DomainInstitution(
            id: '01JTEST00000000000000000001',
            name: 'Colegio Central',
            code: 'CEN-000001',
        );

        $creator
            ->shouldReceive('create')
            ->once()
            ->withArgs(
                static function ($data): bool {
                    return $data->name === 'Colegio Central'
                        && $data->officialRegistration === null;
                },
            )
            ->andReturn($domainInstitution);

        $repository
            ->shouldReceive('save')
            ->once()
            ->with($domainInstitution)
            ->andReturn($domainInstitution);

        $controller = $this->controller(
            service: $service,
            creator: $creator,
            repository: $repository,
        );

        $request = $this->validatedStoreRequest([
            'name' => 'Colegio Central',
        ]);

        $this->mockRedirect(
            'Institution creado correctamente.',
        );


        $response = $controller->store($request);



        $this->assertInstanceOf(
            RedirectResponse::class,
            $response,
        );
    }

    public function test_show_returns_show_view_with_institution(): void
    {
        $institution = $this->institutionModel();

        $view = $this->mockView();

        $viewFactory = Mockery::mock(ViewFactory::class);

        $viewFactory
            ->shouldReceive('make')
            ->once()
            ->with(
                'institutions.show',
                [
                    'institution' => $institution,
                ],
                [],
            )
            ->andReturn($view);

        $this->app->instance(
            ViewFactory::class,
            $viewFactory,
        );

        $controller = $this->controller();

        $result = $controller->show($institution);

        $this->assertSame($view, $result);
    }

    public function test_edit_returns_edit_view_with_institution(): void
    {
        $institution = $this->institutionModel();

        $view = $this->mockView();

        $viewFactory = Mockery::mock(ViewFactory::class);

        $viewFactory
            ->shouldReceive('make')
            ->once()
            ->with(
                'institutions.edit',
                [
                    'institution' => $institution,
                ],
                [],
            )
            ->andReturn($view);

        $this->app->instance(
            ViewFactory::class,
            $viewFactory,
        );

        $controller = $this->controller();

        $result = $controller->edit($institution);

        $this->assertSame($view, $result);
    }

    public function test_update_delegates_id_and_validated_data_to_action(): void
    {
        $service = Mockery::mock(
            InstitutionServiceInterface::class,
        );

        $service
            ->shouldReceive('update')
            ->once()
            ->with(
                '01JTEST00000000000000000003',
                [
                    'name' => 'Institución actualizada',
                ],
            )
            ->andReturnTrue();

        $controller = $this->controller(
            service: $service,
        );

        $institution = $this->institutionModel(
            id: '01JTEST00000000000000000003',
        );

        $request = $this->validatedUpdateRequest([
            'name' => 'Institución actualizada',
        ]);

        $this->mockRedirect(
            'Institution actualizado correctamente.',
        );

        $response = $controller->update(
            $request,
            $institution,
        );

        $this->assertInstanceOf(
            RedirectResponse::class,
            $response,
        );
    }

    public function test_destroy_delegates_model_key_to_action(): void
    {
        $service = Mockery::mock(
            InstitutionServiceInterface::class,
        );

        $service
            ->shouldReceive('delete')
            ->once()
            ->with('01JTEST00000000000000000005')
            ->andReturnTrue();

        $controller = $this->controller(
            service: $service,
        );

        $institution = $this->institutionModel(
            id: '01JTEST00000000000000000005',
        );

        $this->mockRedirect(
            'Institution eliminado correctamente.',
        );

        $response = $controller->destroy($institution);

        $this->assertInstanceOf(
            RedirectResponse::class,
            $response,
        );
    }

    private function controller(
        ?InstitutionServiceInterface $service = null,
        ?InstitutionCreatorInterface $creator = null,
        ?InstitutionRepositoryInterface $repository = null,
    ): InstitutionController {
        $service ??= Mockery::mock(
            InstitutionServiceInterface::class,
        );

        $creator ??= Mockery::mock(
            InstitutionCreatorInterface::class,
        );

        $repository ??= Mockery::mock(
            InstitutionRepositoryInterface::class,
        );

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
        );

        return new InstitutionController(
            service: $service,
            action: $action,
        );
    }

    private function mockView(): View
    {
        return Mockery::mock(View::class);
    }

    private function validatedStoreRequest(
        array $data,
    ): StoreInstitutionRequest {
        $request = StoreInstitutionRequest::create(
            '/institutions',
            'POST',
            $data,
        );

        $validator = Validator::make(
            $request->all(),
            $request->rules(),
        );

        $request->setValidator($validator);

        return $request;
    }

    private function validatedUpdateRequest(
        array $data,
    ): UpdateInstitutionRequest {
        $request = UpdateInstitutionRequest::create(
            '/institutions/01JTEST00000000000000000003',
            'PUT',
            $data,
        );

        $validator = Validator::make(
            $request->all(),
            $request->rules(),
        );

        $request->setValidator($validator);

        return $request;
    }

    private function institutionModel(
        string $id = '01JTEST00000000000000000001',
    ): Institution {
        $institution = new Institution();

        $institution->setAttribute('id', $id);
        $institution->setAttribute('code', 'CEN-000001');
        $institution->setAttribute(
            'name',
            'Institución de prueba',
        );
        $institution->setAttribute(
            'status',
            'draft',
        );

        return $institution;
    }

    private function mockRedirect(
        string $message,
    ): void {
        $redirectResponse = Mockery::mock(
            RedirectResponse::class,
        );

        $redirectResponse
            ->shouldReceive('with')
            ->once()
            ->with('success', $message)
            ->andReturnSelf();

        $redirector = Mockery::mock(
            Redirector::class,
        );

        $redirector
            ->shouldReceive('route')
            ->once()
            ->with('institutions.index')
            ->andReturn($redirectResponse);

        $this->app->forgetInstance('redirect');

        $this->app->instance(
            'redirect',
            $redirector,
        );
    }
}

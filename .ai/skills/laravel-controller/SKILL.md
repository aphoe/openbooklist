---
name: laravel-controller
description: >
  Generate production-ready Laravel controller classes following strict architectural conventions.
  Use this skill whenever the user asks to create, scaffold, generate, or write a Laravel controller,
  API endpoint, route handler, or any PHP controller class. Also trigger when the user says things like
  "make me a controller for X", "I need an endpoint to handle Y", "scaffold the Z resource controller",
  or "add a controller that does X". Always use this skill for Laravel controller generation — do not
  improvise the structure without consulting this skill first.
---

# Laravel Controller Skill

Generate clean, testable, single-responsibility Laravel controllers following strict conventions.

---

## Conventions (Non-Negotiable)

1. **Invokable controllers only** — every controller has a single `__invoke` method. No resourceful multi-method controllers.
2. **Correct namespace** — derive from the route/feature context:
   - API controllers → `App\Http\Controllers\Api\{Sub\Namespace}`
   - Web controllers → `App\Http\Controllers\{Sub\Namespace}`
   - Admin controllers → `App\Http\Controllers\Admin\{Sub\Namespace}`
3. **No inline validation** — always extract a `FormRequest` class:
   - Mirror the controller's namespace under `App\Http\Requests\{Matching\Namespace}`
   - Example: `App\Http\Controllers\Api\Users\StoreUserController` → `App\Http\Requests\Api\Users\StoreUserRequest`
4. **Import every dependency** — always use `use` statements; never reference classes by bare string or omit imports.
5. **Type-hint everything** — constructor dependencies, method parameters, return types.
6. **Delegate model logic to service classes**:
   - Service lives at `App\Services\{ModelName}Service`
   - Example: `User` model → `App\Services\UserService`
   - If the service doesn't exist yet, generate it with the needed method.
   - If a required method is missing from an existing service, add it.
7. **No complex business rules in controllers** — controllers orchestrate only: validate → call service → return response.
8. **Route model binding** — use implicit binding (type-hinted `Model $model`) or explicit binding; never `Model::find($id)` in a controller.
9. **Feature tests required** — generate a Pest (preferred) or PHPUnit test covering:
   - Authentication (unauthenticated requests return 401/403)
   - Validation errors (missing/invalid fields return 422 with correct structure)
   - Successful operation (correct status code + response structure)
   - Response structure assertions (`assertJsonStructure` or `assertJson`)

---

## Output Structure

For every controller request, generate **all** of the following files:

```
app/Http/Controllers/{Namespace}/{ActionName}Controller.php
app/Http/Requests/{Namespace}/{ActionName}Request.php        (if request body exists)
app/Services/{ModelName}Service.php                          (create or show additions)
tests/Feature/{Namespace}/{ActionName}ControllerTest.php
```

---

## Controller Template

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\{Namespace};

use App\Http\Controllers\Controller;
use App\Http\Requests\{Namespace}\{ActionName}Request;
use App\Models\{ModelName};
use App\Services\{ModelName}Service;
use Illuminate\Http\JsonResponse;

final class {ActionName}Controller extends Controller
{
    public function __construct(
        private readonly {ModelName}Service $service,
    ) {}

    public function __invoke({ActionName}Request $request, {ModelName} ${modelVar}): JsonResponse
    {
        $result = $this->service->{serviceMethod}(${modelVar}, $request->validated());

        return response()->json($result, {statusCode});
    }
}
```

---

## FormRequest Template

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests\{Namespace};

use Illuminate\Foundation\Http\FormRequest;

final class {ActionName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Or: $this->user()->can('action', $this->route('model'));
    }

    public function rules(): array
    {
        return [
            'field' => ['required', 'string', 'max:255'],
            // add fields from context
        ];
    }
}
```

---

## Service Template (create or extend as needed)

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\{ModelName};

class {ModelName}Service
{
    public function {methodName}({ModelName} $model, array $data): {ModelName}
    {
        // business logic here
        return $model;
    }
}
```

---

## Test Template (Pest)

```php
<?php

use App\Models\{ModelName};
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('{ActionName}Controller', function () {

    it('requires authentication', function () {
        $response = $this->post{Or{Get}}(route('{route.name}'));
        $response->assertUnauthorized();
    });

    it('validates required fields', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('{route.name}'), []);

        $response->assertUnprocessableEntity()
            ->assertJsonValidationErrors(['field_name']);
    });

    it('successfully {performs action}', function () {
        $user = User::factory()->create();
        $model = {ModelName}::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('{route.name}', $model), [
                'field' => 'value',
            ]);

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'field',
                'created_at',
            ]);
    });

});
```

---

## Decision Guide

| Scenario | Action |
|---|---|
| GET with model ID in URL | Use implicit route model binding: `{ModelName} $model` |
| POST/PUT with body | Generate FormRequest, delegate to service |
| Service class missing | Create `App\Services\{ModelName}Service` with stub method |
| Service method missing | Add the method to the existing service file |
| No auth needed | Set `authorize()` to `true`, but still test for 401 in test file |
| Returns collection | Use `JsonResource` or paginated `->json($collection)` |
| Nested resource (e.g., `/users/{user}/posts/{post}`) | Bind both models in `__invoke` signature |

---

## Example — Full Output

**Request:** "Create a controller to update a blog post"

**Files to generate:**

1. `app/Http/Controllers/Api/Posts/UpdatePostController.php`
2. `app/Http/Requests/Api/Posts/UpdatePostRequest.php`
3. `app/Services/PostService.php` (with `update` method)
4. `tests/Feature/Api/Posts/UpdatePostControllerTest.php`

---

## Checklist Before Outputting

- [ ] Controller is invokable (single `__invoke`)
- [ ] Correct namespace matches file path
- [ ] FormRequest handles all validation (no `$request->validate()` inline)
- [ ] All classes have `use` import statements
- [ ] Dependencies are constructor-injected and type-hinted
- [ ] No `Model::find()` — using route model binding
- [ ] Service class exists and has the needed method
- [ ] Test covers: auth, validation, success, response structure

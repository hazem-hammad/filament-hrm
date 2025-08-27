# Project Instructions — Laravel APIs + Filament Dashboard

**Scope**: This repo uses Laravel for REST APIs (Sanctum auth) and Filament for admin.  
**Architecture**: Route → Controller → DTO → Service → Repository (IF + Eloquent) → API Resource → DataResponse/ErrorResponse.

**Assistant behavior (for all AI tools):**
- Keep controllers thin (validate → DTO → service → response).
- Business logic only in services/repositories.
- Use Form Requests for validation; never trust raw input.
- Transactions for multi-write operations.
- Map domain exceptions to HTTP (see table below).

# Claude Instructions – Laravel APIs + Filament Dashboard

> **Scope**: This repository uses **Laravel** for REST APIs with **Sanctum** auth and **Filament** for the admin/dashboard. Claude should generate code that fits this architecture:
>
> **Route (api.php)** → **Controller** → **DTO** → **Service** → **Repository (Interface + Eloquent impl)** → **API Resource** → **DataResponse / ErrorResponse**.

---

## How Claude should behave

-   Prefer **Laravel 12+** conventions, **PHP 8.3+** features (typed properties, return types, enums, readonly where sensible), and **PSR-12** style.
-   Follow the **thin controller** rule: Controllers only validate, construct DTOs, call Services, and return Response wrappers.
-   **Never** put business logic in Controllers, Form Requests, or API Resources. Keep heavy queries in Repositories.
-   Use **Form Request** classes for validation. **Never** use `$request->all()` or trust raw input.
-   Use our wrappers: `DataResponse($data, ?$message)` on success, `ErrorResponse($message, $errors, $status)` on failures.
-   For authentication use **Sanctum** with the `api` guard. Create tokens in Services via `$user->createToken('auth_token')->plainTextToken`.
-   For API output use **Resource** classes; collections via `Resource::collection($paginator)`.
-   Prefer **transactions** (`DB::transaction`) when a use case writes to multiple tables.
-   **Exceptions → HTTP**: map domain exceptions to appropriate HTTP codes (see table below). Bubble unexpected errors as 500 with a generic message.
-   Use `app('custom.logger')->error(__METHOD__, $exception)` for error logging like in the existing code.
-   In Filament, keep **Resources** focused: Fields in `form()`, columns in `table()`, authorization via policies or `->authorize()` calls, eager load as needed.

---

## Folder & Layering Conventions

```
app/
  DTOs/
    Common/AbstractDTO.php
    V1/User/Auth/RegisterUserDTO.php
    ...
  Services/
    User/Auth/UserService.php
  Repositories/
    Interfaces/UserRepositoryInterface.php
    UserRepository.php
  Http/
    Controllers/Api/V1/...
    Requests/V1/...
    Resources/... (API Resources)
    Responses/DataResponse.php
    Responses/ErrorResponse.php
  Models/User.php
routes/
  api.php
```

### Route groups

-   Version routes with prefix `/v1`.
-   Throttle groups (`throttle:auth`, `throttle:otp`, `throttle:upload`, `throttle:post_data`).
-   Protected groups use `['middleware' => ['auth:api', 'is_user_active']]` as appropriate.

**Template**:

```php
Route::prefix('v1')->group(function () {
    Route::middleware('throttle:auth')->prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        // ...
    });

    Route::middleware(['auth:api', 'is_user_active'])->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'get']);
            Route::put('/', [ProfileController::class, 'update']);
        });
    });
});
```

---

## Controller Pattern

**Rules**

-   Inject Service via constructor (readonly property promotion).
-   Wrap success in `DataResponse`, failures in `ErrorResponse`.
-   Catch domain-level `CustomException` to return a **400** unless a more specific status is needed.
-   Instantiate DTOs from `$request->validated()` only.

**Template** (`App\Http\Controllers\Api\V1\Feature\ThingController`):

```php
final class ThingController extends Controller
{
    public function __construct(private readonly ThingService $service) {}

    public function store(StoreThingRequest $request): JsonResponse
    {
        try {
            $dto = new StoreThingDTO($request->validated());
            $model = $this->service->create($dto);
            return (new DataResponse(new ThingResource($model), __('created')))->toJson();
        } catch (CustomException $e) {
            app('custom.logger')->error(__METHOD__, $e);
            return (new ErrorResponse($e->getMessage(), [], Response::HTTP_BAD_REQUEST))->toJson();
        } catch (\Throwable $e) {
            app('custom.logger')->error(__METHOD__, $e);
            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
```

---

## DTO Pattern

-   All DTOs **extend** `AbstractDTO`.
-   Implement `protected function map(array $data): bool` and set typed properties.
-   Expose **getters** only (immutability by default). Add `toArray()` where the Repository needs raw values.

**Template**:

```php
namespace App\DTOs\V1\Feature;

use App\DTOs\Common\AbstractDTO;

final class StoreThingDTO extends AbstractDTO
{
    protected string $name;
    protected ?string $description = null;

    protected function map(array $data): bool
    {
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        return true;
    }

    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
```

### `AbstractDTO` rules

-   Respect `search`, `filters`, `paginated`, `sort_by`, `sort_order`, `limit`, `page` from the parent.
-   Use `formatDate()` helpers in getters when needed.

---

## Service Pattern

-   Orchestrate use cases; combine repositories and domain services.
-   Use transactions for multi-write operations.
-   Enforce guardrails (e.g., user suspension, ownership checks).
-   Generate Sanctum tokens **only** here.

**Template**:

```php
final class ThingService
{
    public function __construct(private ThingRepositoryInterface $repo) {}

    public function create(StoreThingDTO $dto): Thing
    {
        return DB::transaction(fn () => $this->repo->create($dto));
    }
}
```

**Auth snippet (Sanctum)**:

```php
public function generateToken(User $user): string
{
    return $user->createToken('auth_token')->plainTextToken;
}
```

---

## Repository Pattern

-   **Interface-first**. Controller/Service depend on the interface.
-   Only Eloquent here. Methods accept DTOs/typed params, return Models/Collections/Paginators.
-   Use model **scopes** (`active()`, domain filters) and avoid N+1 with `with()`.

**Interface**:

```php
interface ThingRepositoryInterface
{
    public function create(StoreThingDTO $dto): Thing;
    public function findById(int $id): ?Thing;
    public function list(ListThingDTO $dto): LengthAwarePaginator;
}
```

**Implementation**:

```php
final class ThingRepository implements ThingRepositoryInterface
{
    public function create(StoreThingDTO $dto): Thing
    {
        return Thing::create($dto->toArray());
    }

    public function findById(int $id): ?Thing
    {
        return Thing::query()->find($id);
    }

    public function list(ListThingDTO $dto): LengthAwarePaginator
    {
        return Thing::query()
            ->when($dto->getSearch(), fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->orderBy($dto->getSortBy(), $dto->getSortOrder())
            ->paginate($dto->getLimit(), ['*'], 'page', $dto->getPage());
    }
}
```

---

## API Resources

-   Convert models to transport shape. Avoid queries in Resources.
-   For auth flows, it’s acceptable to pass extra context (e.g., token) into the Resource constructor.

**Template**:

```php
final class ThingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
```

**With token example**:

```php
final class ProfileResource extends JsonResource
{
    public function __construct($resource, private ?string $token = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'token' => $this->when($this->token !== null, $this->token),
        ];
    }
}
```

---

## Error Handling & HTTP Status Mapping

**Guidance**

-   Throw domain exceptions (e.g., `InvalidCredentialsException`, `UserSuspendedException`, `CannotUseOldPasswordException`).
-   Controllers map expected domain errors; unexpected → 500.
-   Prefer central handling in `app/Exceptions/Handler.php` so controllers can stay lean.

### Tailored map for this codebase

| Exception                          | HTTP | Message key (suggested)      |
| ---------------------------------- | ---- | ---------------------------- |
| AuthenticationException            | 401  | `unauthenticated`            |
| InvalidCredentialsException        | 401  | `invalid_credentials`        |
| WrongPasswordException             | 401  | `wrong_password`             |
| TokenNotFoundException             | 401  | `invalid_or_missing_token`   |
| TokenExpiredException              | 401  | `token_expired`              |
| AuthorizationException             | 403  | `forbidden`                  |
| UserSuspendedException             | 403  | `user_suspended`             |
| InvalidProfileException            | 403  | `invalid_profile_access`     |
| InvalidUserException               | 404  | `user_not_found`             |
| ModelNotFoundException             | 404  | `resource_not_found`         |
| DifferentSocialMethodException     | 409  | `different_social_method`    |
| CannotUseOldPasswordException      | 409  | `password_reuse_not_allowed` |
| InvalidTokenTypeException          | 422  | `invalid_token_type`         |
| CannotResetPasswordException       | 422  | `cannot_reset_password`      |
| ValidationException (Form Request) | 422  | validation errors payload    |
| ThrottleRequestsException          | 429  | `too_many_requests`          |
| CustomException (generic domain)   | 400  | exception message            |
| Other Throwables                   | 500  | `something_went_wrong`       |

> Tip: keep controller `try/catch` for auth-related actions where you want custom messages (e.g., login). For most cases, rely on the global handler.

---

## Requests (Validation)

-   Each endpoint has a **Form Request** in `App\Http\Requests\V1\...`.
-   Authorize in the request when resource-level permission is required.
-   Always return `validated()` to DTO.

**Template**:

```php
final class StoreThingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
```

---

## Filament (Admin/Dashboard) Guidelines

-   Use **Resources** for CRUD. Pages auto-generated: List, Create, Edit, View.
-   Apply **policies** or guards; don’t rely solely on UI.
-   Keep uploads compatible with **Spatie Media Library** when models implement `HasMedia`.
-   Optimize tables: use `->searchable()` only on indexed fields; eager load relations in `Table::query()`.

**Resource Template**:

```php
class ThingResource extends Resource
{
    protected static ?string $model = Thing::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(120),
            Textarea::make('description')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
```

**Media example** (model implements `HasMedia`):

```php
FileUpload::make('image')
    ->image()
    ->disk('public') // or configured disk
    ->directory('things')
    ->preserveFilenames()
    ->visibility('public');
```

---

## Auth & Middleware

-   Guard: `auth:api` (Sanctum). Use `auth('api')` helper.
-   Throttle endpoints with named limiters.
-   Add `is_user_active` middleware for post-login protection.

---

## Logging

-   Use `app('custom.logger')->error(__METHOD__, $exception)` in catch blocks.
-   Add context: user id, request id, or DTO key fields when helpful.

---

## Testing Guidance (Claude should suggest Pest/PHPUnit tests)

-   Use **database transactions** or `RefreshDatabase` trait.
-   Authenticate with Sanctum: `Sanctum::actingAs($user, ['*'], 'api');`
-   Example feature test outline:

```php
it('registers a user', function () {
    $payload = [/* ... */];
    $resp = $this->postJson('/api/v1/auth/register', $payload);
    $resp->assertCreated();
    $resp->assertJsonStructure(['data' => ['id', 'token']]);
});
```

---

## Do / Don’t

**Do**

-   Use enums for fixed sets (statuses, types).
-   Use `when()` in Resources to include optional fields.
-   Make Repository methods **narrow**: each does one job.

**Don’t**

-   Don’t query inside Resources.
-   Don’t expose internal ids when a uuid/slug is intended.
-   Don’t catch `\Throwable` without logging.

---

## Claude Chat – Ready Prompts

Use these in VS Code Chat from this repo (Claude will automatically load this file):

-   **Scaffold a new module**

    > Create endpoints for `Thing` (index, store, show, update, destroy). Use Route→Controller→DTO→Service→Repository→Resource. Generate Form Requests, DTOs, interfaces, implementations, and a Filament Resource. Apply sanctum `auth:api` to write endpoints, throttle reads with `throttle:api`.

-   **Add new auth flow**

    > Implement `POST /v1/auth/change-email` using a `ChangeEmailRequest`, `ChangeEmailDTO`, `UserService::changeEmail()`, and `UserRepository::changeEmail()`. Return `DataResponse(null, 'email_changed')` and map validation errors to 422.

-   **Make a paginated listing**

    > Build `GET /v1/things` that accepts `search`, `sort_by`, `sort_order`, `page`, `limit`. Use `ListThingDTO` extending `AbstractDTO`, repository `list()` method, and return `ThingResource::collection($paginator)`.

-   **Filament table filter**

    > Add a dependent Select filter: `Track` → `SubTrack`, with SubTrack disabled until Track is selected, then populated via a closure that queries related rows.

-   **Code review**

    > Review `UserService` and flag violations of the layering rules, missing transactions, repository leaks, or places where exceptions should be mapped to specific HTTP status codes.

---

## Commit Message Style (optional)

-   Conventional Commits (`feat:`, `fix:`, `refactor:`, `test:`, `docs:`).
-   Mention layer in scope: `feat(service): calculate wallet fees`.

---

## Checklist for New Endpoints

-   ***

## Notes for This Codebase (seeded from your samples)

-   `AuthenticationController` shows the standard try/catch, DTO creation from `validated()`, and `ProfileResource($user, $token)` pattern. **Replicate that style** for new endpoints.
-   `AbstractDTO` already handles pagination/sorting/search; **re-use getters** in repositories.
-   `UserService` centralizes auth, registration, and profile logic; always call it from controllers.
-   `UserRepository` encapsulates all user queries, status and relations; **don’t** bypass it from services.
-   Use `User::active()` and `->with()` to avoid N+1.

---

## Tiny `.github/instructions/` Pack

> Drop these files to guide Claude Chat & the Coding Agent with focused tasks. Keep them short and imperative.

**1) **``

-   Scaffold endpoint flow: **Route → Controller → Form Request → DTO → Service → Repository (IF + Impl) → Resource**.
-   Use `auth:api` for write ops; throttle reads with `throttle:api` or named limiters.
-   Controllers only: validate → DTO → Service → `DataResponse`.
-   Include pagination/sorting via `AbstractDTO` for index endpoints.
-   Return collections via `Resource::collection($paginator)`.

**Ready prompt**

```
Create endpoints for <Entity> (index, store, show, update, destroy) using our layering.
Generate: Form Requests, DTOs, Repository IF + Eloquent impl, Service, API Resource, tests.
Auth: sanctum `auth:api` for writes. Validation: strict. Responses: DataResponse/ErrorResponse.
```

**2) **``

-   Use the tailored map in `Handler` (see repo root instructions).
-   In controllers handling auth flows, still catch domain exceptions to customize messages.
-   Never expose raw exception messages to clients (use keys + translations where possible).

**Ready prompt**

```
Wire domain exceptions to HTTP in Handler using the provided map. Ensure Validation/Throttle/ModelNotFound are JSON via ErrorResponse.
Add tests for each mapped exception path.
```

**3) **``

-   Generate Filament Resource with clean `form()` and `table()`.
-   Use `->searchable()` only on indexed columns; eager-load in `table()` query.
-   Media uploads compatible with Spatie Media Library if model implements `HasMedia`.
-   Add policies or `->authorize()` checks.

**Ready prompt**

```
Create a Filament Resource for <Entity> with fields <...> and columns <...>.
Ensure policies are enforced and queries are eager-loaded.
```

**4) **``

-   Prefer Pest; use factories; authenticate with Sanctum for protected routes.
-   Cover: success, validation 422, unauthorized 401, forbidden 403, not found 404.

**Ready prompt**

```
Write Pest feature tests for <Entity> endpoints covering happy path, validation errors, 401/403/404, and pagination.
```

> Optional: add more task-focused files later (e.g., `repository-pattern.instructions.md`).

---

### Fin

If any ambiguity arises, prefer the patterns and templates above. Keep controllers lean, services orchestrated, repositories focused, and resources clean.

- use spatie media library class instead of filament classes for images compoment \
eg: SpatieMediaLibraryImageColumn instead of ImageColumn
- apply that to all upcoming filament resource which have status, it should be an action with confirmation message
- in filament resource, the table should be in List page not in the resource itself\
the actions should be grouped into dropdown\
the resource should have status column active/inactive with change status in actions dropdown
- since we upgraded to laravel 12, please use #[Scope] attribute instead of prefix scopeEnabled\
\
the following\
public function scopeEnabled(Builder $query): void
    {
        $query->where('is_enabled', true);
    }\
\
should be \
\
#[Scope]
    public function enabled(Builder $query): void
    {
        $query->where('is_enabled', true);
    }
- consider showing/hiding in filament resource if the module introduced into ModuleSeeder
- all images should in responses should use MediaResource instead retreiving it as a string
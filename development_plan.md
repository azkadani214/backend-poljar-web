## Polinema Mengajar - Backend Development with Service Repository Pattern

**Project Stack:**

- Laravel 11.x (Backend)
- Vue 3 (Frontend)
- Axios (API Communication)
- Pinia (State Management)
- Service Repository Pattern

---

## 📋 TABLE OF CONTENTS

1. [Project Overview](#project-overview)
2. [Development Phases](#development-phases)
3. [Phase Details](#phase-details)
4. [Implementation Checklist](#implementation-checklist)
5. [Testing Strategy](#testing-strategy)
6. [Deployment Guide](#deployment-guide)
7. [Troubleshooting](#troubleshooting)

---

## 🎯 PROJECT OVERVIEW

### Authentication & Authorization Flow

```
┌─────────────────────────────────────────────────────────────┐
│                   USER ACCESS FLOW                           │
└─────────────────────────────────────────────────────────────┘

PUBLIC ACCESS:
User → Website (/) → Browse content → No login required

ADMIN PANEL ACCESS (Multi-Layer Protection):
User → /admin or /admin/login
  ↓
[LAYER 1: Token Gate Middleware]
  ├─ Check session: admin_token_verified?
  │   ├─ YES → Proceed to Layer 2
  │   └─ NO → Redirect to /admin/token
  │
  └─ /admin/token (Token Input Page)
      ├─ Enter ADMIN_ACCESS_TOKEN from .env
      ├─ Rate Limiting: 5 attempts/minute
      ├─ IP Tracking & Logging
      └─ Valid?
          ├─ YES → Set session → Redirect to /admin/login
          └─ NO → Show error

[LAYER 2: Email/Password Authentication]
  └─ /admin/login
      ├─ Enter Email & Password
      ├─ Check user status (active/inactive)
      └─ Valid?
          ├─ YES → Create Sanctum token → Access admin panel
          └─ NO → Show error

IMPORTANT NOTES:
- NO public registration (users created by admin only)
- Token session expires after 24 hours (configurable)
- Failed attempts are logged for security audit
- IP validation prevents session hijacking
```

### Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                        Frontend (Vue 3)                      │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  Pages   │  │Components│  │  Stores  │  │  Services│   │
│  │          │  │          │  │  (Pinia) │  │  (Axios) │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
                              ↓ HTTP/JSON
┌─────────────────────────────────────────────────────────────┐
│                    Backend (Laravel API)                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                    API Controllers                    │  │
│  │              (Thin - Route to Services)              │  │
│  └──────────────────────────────────────────────────────┘  │
│                              ↓                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                  Services Layer                       │  │
│  │            (Business Logic & Validation)             │  │
│  └──────────────────────────────────────────────────────┘  │
│                              ↓                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │               Repositories Layer                      │  │
│  │              (Data Access & Queries)                 │  │
│  └──────────────────────────────────────────────────────┘  │
│                              ↓                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                  Models & Database                    │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Key Benefits of This Architecture

- **Separation of Concerns**: Each layer has specific responsibility
- **Testability**: Easy to mock and test each layer independently
- **Maintainability**: Changes in one layer don't affect others
- **Scalability**: Easy to add new features
- **Reusability**: Services and repositories can be reused

---

## 🚀 DEVELOPMENT PHASES

### **PHASE 0: Setup & Foundation** (COMPLETED ✓)

- ✅ Base Repository Interface & Implementation
- ✅ Repository Service Provider
- ✅ Helper Classes (Response, Image, Slug)
- ✅ Model Traits (UUID, Slug, Status)
- ✅ Exception Handlers
- ✅ Config Files
- ✅ News Models

### **PHASE 1: Core Services** (COMPLETED ✓)

- ✅ Auth Service (Login only - No public registration)
- ✅ Token Service (Sanctum API tokens)
- ✅ Password Reset Service
- ✅ User Service
- ✅ Profile Service
- ✅ Division Service
- ✅ Position Service
- ✅ Membership Service
- ✅ Image Upload Service
- ✅ File Upload Service
- ✅ Token Gate Middleware (Admin panel protection)

### **PHASE 2: News Services** (COMPLETED ✓)

- ✅ News Post Service
- ✅ News Category Service
- ✅ News Tag Service
- ✅ News Comment Service
- ✅ Newsletter Service
- ✅ News SEO Service

### **PHASE 3: API Resources & Requests** (COMPLETED ✓)

- ✅ API Resources (Data Transformation) - 15 files
- ✅ Form Requests (Validation) - 18 files
- ✅ API Response Standardization

### **PHASE 4: API Controllers** (COMPLETED ✓)

- ✅ Auth Controllers (Login, Logout, Password Reset)
- ✅ User Controllers (CRUD, Profile)
- ✅ Division Controllers
- ✅ Position Controllers
- ✅ Membership Controllers
- ✅ News Controllers (Post, Category, Tag, Comment)
- ✅ Newsletter Controller
- ✅ Blog Controller

### **PHASE 5: API Routes & Middleware** (CURRENT PHASE)

- API Versioning Setup
- Route Definitions (api_v1.php)
- Middleware Configuration
- CORS Configuration
- Rate Limiting

### **PHASE 6: Frontend Integration**

- Axios Configuration
- Pinia Stores
- API Services (Frontend)
- Vue Components

### **PHASE 7: Testing**

- Unit Tests (Services & Repositories)
- Feature Tests (API Endpoints)
- Integration Tests

### **PHASE 8: Documentation & Deployment**

- API Documentation
- Postman Collection
- Deployment Scripts

---

## 📖 PHASE DETAILS

### PHASE 0: Setup & Foundation ✅ COMPLETED

**Objective**: Set up the foundation for Service Repository Pattern

**Files Created:**

```
✅ app/Repositories/Contracts/BaseRepositoryInterface.php
✅ app/Repositories/Eloquent/BaseRepository.php
✅ app/Helpers/ResponseHelper.php
✅ app/Helpers/ImageHelper.php
✅ app/Helpers/SlugHelper.php
✅ app/Providers/RepositoryServiceProvider.php
✅ app/Models/Traits/HasUuid.php
✅ app/Models/Traits/HasSlug.php
✅ app/Models/Traits/HasStatus.php
✅ app/Exceptions/Api/*.php
✅ config/repository.php
✅ config/api.php
✅ config/admin.php (Admin token configuration)
✅ app/Models/News/*.php (All News Models)
✅ app/Repositories/Contracts/*RepositoryInterface.php (All Interfaces)
✅ app/Repositories/Eloquent/*Repository.php (All Implementations)
✅ app/Http/Middleware/ValidateAdminToken.php (Token gate)
✅ app/Http/Controllers/TokenAuthController.php (Token verification)
✅ resources/views/admin/token-login.blade.php (Token input page)
✅ routes/admin.php (Admin routes with token gate)
✅ database/migrations/xxxx_add_last_login_to_users_table.php
```

**Post-Setup Tasks:**

1. ✅ Register RepositoryServiceProvider in `bootstrap/providers.php`
2. ✅ Add helpers to composer.json autoload
3. Run `composer dump-autoload`
4. ✅ Verify all models have proper relationships
5. ✅ Add admin.token middleware alias in bootstrap/app.php
6. Generate admin access token and add to .env
7. Run migration for last_login_at field

---

### PHASE 1: Core Services ✅ COMPLETED

**Objective**: Implement business logic layer for core features

**Duration**: 2-3 days

#### ✅ 1.1 Auth Service

**File**: `app/Services/Auth/AuthService.php`

**Responsibilities:**

- User login (NO public registration)
- Logout
- Token management
- Password reset
- Email verification

**Key Methods:**

```php
- login(array $credentials): array
- logout(User $user): bool
- refreshToken(string $token): array
- resetPassword(string $email): bool
- verifyEmail(string $token): bool
```

**IMPORTANT**:

- ❌ NO register() method - Users created by admin only via UserService
- ✅ Token gate protection for admin panel access
- ✅ Rate limiting on login attempts

**Dependencies:**

- UserRepository
- TokenService

---

#### ✅ 1.2 Token Gate System

**Files**:

- `app/Http/Middleware/ValidateAdminToken.php`
- `app/Http/Controllers/TokenAuthController.php`
- `resources/views/admin/token-login.blade.php`
- `routes/admin.php`

**Responsibilities:**

- Validate admin access token before login
- Session management for token verification
- Rate limiting on token attempts
- Security logging

**Flow**:

1. User accesses /admin → Redirect to /admin/token
2. Enter token from .env (ADMIN_ACCESS_TOKEN)
3. Token valid → Set session → Redirect to /admin/login
4. Enter email & password → Access granted

**Security Features**:

- Token stored in .env (server-side only)
- Rate limiting: 5 attempts per minute
- Session expiration: 24 hours (configurable)
- IP validation to prevent session hijacking
- Comprehensive security logging
- No token exposure to client-side

**Configuration** (.env):

```env
ADMIN_ACCESS_TOKEN=your-generated-token-here
ADMIN_TOKEN_GATE_ENABLED=true
ADMIN_TOKEN_SESSION_LIFETIME=1440
```

Generate token:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

---

#### 1.2 User Service

**File**: `app/Services/User/UserService.php`

**Responsibilities:**

- User CRUD operations
- User profile management
- User status management
- User search and filtering

**Key Methods:**

```php
- getAllUsers(array $filters, int $perPage): LengthAwarePaginator
- getUserById(string $id): User
- createUser(array $data): User
- updateUser(string $id, array $data): User
- deleteUser(string $id): bool
- activateUser(string $id): bool
- deactivateUser(string $id): bool
- searchUsers(string $keyword): Collection
- updatePassword(string $id, string $password): bool
```

**Dependencies:**

- UserRepository
- ImageUploadService

---

#### 1.3 Division Service

**File**: `app/Services/Division/DivisionService.php`

**Responsibilities:**

- Division CRUD operations
- Division statistics
- Division members management

**Key Methods:**

```php
- getAllDivisions(): Collection
- getDivisionById(string $id): Division
- createDivision(array $data): Division
- updateDivision(string $id, array $data): Division
- deleteDivision(string $id): bool
- getDivisionStatistics(string $id): array
- getDivisionMembers(string $id): Collection
```

**Dependencies:**

- DivisionRepository
- PositionRepository
- MembershipRepository

---

#### 1.4 Position Service

**File**: `app/Services/Position/PositionService.php`

**Responsibilities:**

- Position CRUD operations
- Position hierarchy management

**Key Methods:**

```php
- getAllPositions(): Collection
- getPositionsByDivision(string $divisionId): Collection
- createPosition(array $data): Position
- updatePosition(string $id, array $data): Position
- deletePosition(string $id): bool
- getCoreTeamPositions(): Collection
- getStaffPositions(): Collection
```

**Dependencies:**

- PositionRepository
- DivisionRepository

---

#### 1.5 Membership Service

**File**: `app/Services/Membership/MembershipService.php`

**Responsibilities:**

- Membership CRUD operations
- Membership activation/deactivation
- Team hierarchy management

**Key Methods:**

```php
- getAllMemberships(array $filters): LengthAwarePaginator
- createMembership(array $data): Membership
- updateMembership(string $id, array $data): Membership
- deleteMembership(string $id): bool
- activateMembership(string $id): bool
- deactivateMembership(string $id): bool
- getCoreTeam(): Collection
- getStaff(): Collection
```

**Dependencies:**

- MembershipRepository
- UserRepository
- DivisionRepository
- PositionRepository

---

### PHASE 2: News Services

**Objective**: Implement business logic for news/blog system

**Duration**: 3-4 days

#### 2.1 News Post Service

**File**: `app/Services/News/NewsPostService.php`

**Responsibilities:**

- News post CRUD operations
- Publishing workflow
- Search and filtering
- View tracking

**Key Methods:**

```php
- getAllPosts(array $filters, int $perPage): LengthAwarePaginator
- getPublishedPosts(int $perPage): LengthAwarePaginator
- getPostBySlug(string $slug): NewsPost
- createPost(array $data): NewsPost
- updatePost(string $id, array $data): NewsPost
- deletePost(string $id): bool
- publishPost(string $id): bool
- unpublishPost(string $id): bool
- schedulePost(string $id, DateTime $dateTime): bool
- searchPosts(string $keyword): LengthAwarePaginator
- incrementViews(string $id): bool
```

**Dependencies:**

- NewsPostRepository
- NewsCategoryRepository
- NewsTagRepository
- ImageUploadService
- NewsSeoService

---

#### 2.2 News Category Service

**File**: `app/Services/News/NewsCategoryService.php`

**Key Methods:**

```php
- getAllCategories(): Collection
- getCategoryBySlug(string $slug): NewsCategory
- createCategory(array $data): NewsCategory
- updateCategory(string $id, array $data): NewsCategory
- deleteCategory(string $id): bool
- getCategoriesWithPostsCount(): Collection
```

---

#### 2.3 News Tag Service

**File**: `app/Services/News/NewsTagService.php`

**Key Methods:**

```php
- getAllTags(): Collection
- getTagBySlug(string $slug): NewsTag
- createTag(array $data): NewsTag
- findOrCreateTags(array $tagNames): Collection
- getPopularTags(int $limit): Collection
```

---

#### 2.4 News Comment Service

**File**: `app/Services/News/NewsCommentService.php`

**Key Methods:**

```php
- getCommentsByPost(string $postId): LengthAwarePaginator
- createComment(array $data): NewsComment
- updateComment(string $id, array $data): NewsComment
- deleteComment(string $id): bool
- approveComment(string $id): bool
- rejectComment(string $id): bool
- bulkApprove(array $ids): bool
```

---

#### 2.5 Newsletter Service

**File**: `app/Services/Newsletter/NewsletterService.php`

**Key Methods:**

```php
- subscribe(string $email): NewsletterSubscriber
- unsubscribe(string $email): bool
- verifySubscriber(string $token): bool
- getSubscribers(): Collection
- sendNewsletter(array $data): bool
```

---

### PHASE 3: API Resources & Requests

**Objective**: Create standardized API responses and validation

**Duration**: 2 days

#### 3.1 API Resources (Data Transformation)

**Purpose**: Transform model data into consistent API responses

**Location**: `app/Http/Resources/V1/`

**Example Structure**:

```php
// UserResource.php
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'photo' => $this->avatar_url,
            'status' => $this->status,
            'divisions' => DivisionResource::collection($this->whenLoaded('divisions')),
            'memberships_count' => $this->whenCounted('memberships'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
```

**Files to Create**:

```
app/Http/Resources/V1/
├── User/
│   ├── UserResource.php
│   ├── UserCollection.php
│   └── UserDetailResource.php
├── Division/
│   ├── DivisionResource.php
│   └── DivisionDetailResource.php
├── Position/
│   └── PositionResource.php
├── Membership/
│   └── MembershipResource.php
└── News/
    ├── NewsPostResource.php
    ├── NewsPostDetailResource.php
    ├── NewsCategoryResource.php
    ├── NewsTagResource.php
    └── NewsCommentResource.php
```

---

#### 3.2 Form Requests (Validation)

**Purpose**: Validate incoming API requests

**Location**: `app/Http/Requests/`

**Example Structure**:

```php
// StoreUserRequest.php
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or check permissions
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|max:1024',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            // ... more custom messages
        ];
    }
}
```

**Files to Create**:

```
app/Http/Requests/
├── Auth/
│   ├── LoginRequest.php
│   └── ResetPasswordRequest.php (NO RegisterRequest)
├── User/
│   ├── StoreUserRequest.php
│   ├── UpdateUserRequest.php
│   └── UpdateProfileRequest.php
├── Division/
│   ├── StoreDivisionRequest.php
│   └── UpdateDivisionRequest.php
├── Position/
│   ├── StorePositionRequest.php
│   └── UpdatePositionRequest.php
├── Membership/
│   ├── StoreMembershipRequest.php
│   └── UpdateMembershipRequest.php
└── News/
    ├── StoreNewsPostRequest.php
    ├── UpdateNewsPostRequest.php
    ├── StoreNewsCategoryRequest.php
    └── StoreCommentRequest.php
```

---

### PHASE 4: API Controllers

**Objective**: Create thin controllers that route to services

**Duration**: 3 days

#### Controller Pattern

```php
class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index(Request $request)
    {
        try {
            $users = $this->userService->getAllUsers(
                $request->all(),
                $request->input('per_page', 15)
            );

            return ResponseHelper::paginated(
                $users,
                UserResource::class,
                'Users retrieved successfully'
            );
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return ResponseHelper::created(
                new UserResource($user),
                'User created successfully'
            );
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
```

**Files to Create**:

```
app/Http/Controllers/Api/V1/
├── Auth/
│   ├── AuthController.php (Login, Logout, Me only)
│   └── PasswordResetController.php (NO RegisterController)
├── User/
│   ├── UserController.php
│   ├── ProfileController.php
│   └── UserDivisionController.php
├── Division/
│   ├── DivisionController.php
│   ├── DivisionPositionController.php
│   └── DivisionMemberController.php
├── Position/
│   └── PositionController.php
├── Membership/
│   └── MembershipController.php
└── News/
    ├── NewsPostController.php
    ├── NewsCategoryController.php
    ├── NewsTagController.php
    ├── NewsCommentController.php
    └── NewsSearchController.php
```

---

### PHASE 5: API Routes & Middleware

**Objective**: Set up API routing and middleware

**Duration**: 1 day

#### 5.1 Create API Routes File

**File**: `routes/api_v1.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
// ... more controllers

// Public routes - NO REGISTRATION ENDPOINT
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Users (Admin only - for creating new users)
    Route::apiResource('users', UserController::class);
    Route::post('/users/{id}/activate', [UserController::class, 'activate']);
    Route::post('/users/{id}/deactivate', [UserController::class, 'deactivate']);

    // Divisions
    Route::apiResource('divisions', DivisionController::class);
    Route::get('/divisions/{id}/statistics', [DivisionController::class, 'statistics']);

    // Positions
    Route::apiResource('positions', PositionController::class);

    // Memberships
    Route::apiResource('memberships', MembershipController::class);

    // News Posts
    Route::apiResource('news/posts', NewsPostController::class);
    Route::post('/news/posts/{id}/publish', [NewsPostController::class, 'publish']);
    Route::post('/news/posts/{id}/unpublish', [NewsPostController::class, 'unpublish']);

    // News Categories
    Route::apiResource('news/categories', NewsCategoryController::class);

    // News Tags
    Route::apiResource('news/tags', NewsTagController::class);

    // News Comments
    Route::apiResource('news/comments', NewsCommentController::class);
    Route::post('/news/comments/{id}/approve', [NewsCommentController::class, 'approve']);
});

// Public News Routes
Route::prefix('public')->group(function () {
    Route::get('/news/posts', [NewsPostController::class, 'publicIndex']);
    Route::get('/news/posts/{slug}', [NewsPostController::class, 'show']);
    Route::get('/news/categories', [NewsCategoryController::class, 'publicIndex']);
    Route::get('/news/tags', [NewsTagController::class, 'publicIndex']);
});
```

---

#### 5.2 Update Main API Routes

**File**: `routes/api.php`

```php
<?php

use Illuminate\Support\Facades\Route;

// API V1
Route::prefix('v1')->group(base_path('routes/api_v1.php'));

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});
```

---

#### 5.3 Create Middleware

**Files to Create**:

1. **ApiVersion.php** - Handle API versioning
2. **CheckApiToken.php** - Validate API tokens
3. **TrackApiUsage.php** - Log API usage

**Register in** `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \App\Http\Middleware\ApiVersion::class,
    ]);
})
```

---

#### 5.4 CORS Configuration

**File**: `config/cors.php`

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173', // Vite dev server
        'http://localhost:3000', // Alternative
        env('FRONTEND_URL', 'http://localhost:5173'),
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

---

### PHASE 6: Frontend Integration

**Objective**: Connect Vue 3 frontend with Laravel API

**Duration**: 4-5 days

#### 6.1 Axios Configuration

**File**: `frontend/src/config/axios.js`

```javascript
import axios from "axios";
import { useAuthStore } from "@/stores/auth";

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api/v1",
    timeout: 10000,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

// Request interceptor
api.interceptors.request.use(
    (config) => {
        const authStore = useAuthStore();
        const token = authStore.token;

        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

// Response interceptor
api.interceptors.response.use(
    (response) => {
        return response.data;
    },
    (error) => {
        const authStore = useAuthStore();

        if (error.response?.status === 401) {
            authStore.logout();
            window.location.href = "/login";
        }

        return Promise.reject(error);
    },
);

export default api;
```

---

#### 6.2 Pinia Stores

**Structure**:

```
frontend/src/stores/
├── auth.js
├── user.js
├── division.js
├── position.js
├── membership.js
├── news.js
└── newsletter.js
```

**Example Store** (`frontend/src/stores/user.js`):

```javascript
import { defineStore } from "pinia";
import { ref, computed } from "vue";
import UserService from "@/services/UserService";

export const useUserStore = defineStore("user", () => {
    // State
    const users = ref([]);
    const currentUser = ref(null);
    const loading = ref(false);
    const error = ref(null);
    const pagination = ref({
        total: 0,
        currentPage: 1,
        perPage: 15,
    });

    // Getters
    const activeUsers = computed(() => {
        return users.value.filter((user) => user.status === "active");
    });

    // Actions
    async function fetchUsers(page = 1, filters = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response = await UserService.getAll({ page, ...filters });
            users.value = response.data;
            pagination.value = response.pagination;
        } catch (err) {
            error.value = err.message;
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchUser(id) {
        loading.value = true;
        error.value = null;

        try {
            const response = await UserService.getById(id);
            currentUser.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.message;
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createUser(data) {
        loading.value = true;
        error.value = null;

        try {
            const response = await UserService.create(data);
            users.value.push(response.data);
            return response.data;
        } catch (err) {
            error.value = err.message;
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateUser(id, data) {
        loading.value = true;
        error.value = null;

        try {
            const response = await UserService.update(id, data);
            const index = users.value.findIndex((u) => u.id === id);
            if (index !== -1) {
                users.value[index] = response.data;
            }
            return response.data;
        } catch (err) {
            error.value = err.message;
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteUser(id) {
        loading.value = true;
        error.value = null;

        try {
            await UserService.delete(id);
            users.value = users.value.filter((u) => u.id !== id);
        } catch (err) {
            error.value = err.message;
            throw err;
        } finally {
            loading.value = false;
        }
    }

    return {
        // State
        users,
        currentUser,
        loading,
        error,
        pagination,

        // Getters
        activeUsers,

        // Actions
        fetchUsers,
        fetchUser,
        createUser,
        updateUser,
        deleteUser,
    };
});
```

---

#### 6.3 API Services (Frontend)

**File**: `frontend/src/services/UserService.js`

```javascript
import api from "@/config/axios";

class UserService {
    async getAll(params = {}) {
        return await api.get("/users", { params });
    }

    async getById(id) {
        return await api.get(`/users/${id}`);
    }

    async create(data) {
        return await api.post("/users", data);
    }

    async update(id, data) {
        return await api.put(`/users/${id}`, data);
    }

    async delete(id) {
        return await api.delete(`/users/${id}`);
    }

    async activate(id) {
        return await api.post(`/users/${id}/activate`);
    }

    async deactivate(id) {
        return await api.post(`/users/${id}/deactivate`);
    }

    async search(keyword, params = {}) {
        return await api.get("/users", {
            params: { search: keyword, ...params },
        });
    }
}

export default new UserService();
```

**Create Similar Services For**:

- AuthService.js
- DivisionService.js
- PositionService.js
- MembershipService.js
- NewsService.js
- CategoryService.js
- TagService.js
- CommentService.js
- NewsletterService.js

---

### PHASE 7: Testing

**Objective**: Ensure code quality and reliability

**Duration**: 3-4 days

#### 7.1 Unit Tests (Services & Repositories)

**Example**: `tests/Unit/Services/UserServiceTest.php`

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\User\UserService;
use App\Repositories\Contracts\UserRepositoryInterface;
use Mockery;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    public function test_can_get_all_users()
    {
        // Arrange
        $this->userRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn(collect());

        // Act
        $result = $this->userService->getAllUsers([], 15);

        // Assert
        $this->assertNotNull($result);
    }

    public function test_can_create_user()
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $this->userRepository
            ->shouldReceive('create')
            ->once()
            ->with($userData)
            ->andReturn((object) $userData);

        // Act
        $result = $this->userService->createUser($userData);

        // Assert
        $this->assertEquals('John Doe', $result->name);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

---

#### 7.2 Feature Tests (API Endpoints)

**Example**: `tests/Feature/Api/UserApiTest.php`

```php
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_users_list()
    {
        // Arrange
        Sanctum::actingAs(User::factory()->create());
        User::factory()->count(5)->create();

        // Act
        $response = $this->getJson('/api/v1/users');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'email', 'status']
                ],
                'pagination'
            ]);
    }

    public function test_can_create_user()
    {
        // Arrange
        Sanctum::actingAs(User::factory()->create());

        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
        ];

        // Act
        $response = $this->postJson('/api/v1/users', $userData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email']
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com'
        ]);
    }
}
```

**Test Coverage Goals**:

- Unit Tests: 80%+
- Feature Tests: All critical endpoints
- Integration Tests: Key workflows

---

### PHASE 8: Documentation & Deployment

**Objective**: Document API and prepare for deployment

**Duration**: 2 days

#### 8.1 API Documentation

**Tool**: Use Laravel Scribe or create Postman Collection

**Install Scribe**:

```bash
composer require --dev knuckleswtf/scribe
php artisan vendor:publish --tag=scribe-config
php artisan scribe:generate
```

**Example Documentation Comment**:

```php
/**
 * Get all users
 *
 * @group User Management
 *
 * @queryParam page integer Page number. Example: 1
 * @queryParam per_page integer Items per page. Example: 15
 * @queryParam search string Search keyword. Example: john
 * @queryParam status string Filter by status. Example: active
 *
 * @response 200 {
 *   "success": true,
 *   "message": "Users retrieved successfully",
 *   "data": [
 *     {
 *       "id": "uuid",
 *       "name": "John Doe",
 *       "email": "john@example.com",
 *       "status": "active"
 *     }
 *   ],
 *   "pagination": {
 *     "total": 100,
 *     "per_page": 15,
 *     "current_page": 1
 *   }
 * }
 */
public function index(Request $request)
{
    // ...
}
```

---

#### 8.2 Environment Configuration

**Production `.env`**:

```env
APP_NAME="Polinema Mengajar"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=polinema_mengajar
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Admin Token Gate (IMPORTANT!)
ADMIN_ACCESS_TOKEN=your-generated-secure-token-here
ADMIN_TOKEN_GATE_ENABLED=true
ADMIN_TOKEN_SESSION_LIFETIME=1440

# Sanctum
SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
SESSION_DOMAIN=.your-domain.com
SESSION_DRIVER=database

# Frontend
FRONTEND_URL=https://your-frontend-domain.com

# API Configuration
API_RATE_LIMIT_ENABLED=true
API_RATE_LIMIT_MAX_ATTEMPTS=60

# Repository Cache
REPOSITORY_CACHE_ENABLED=true
REPOSITORY_CACHE_MINUTES=60

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@polinema-mengajar.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Generate Admin Token**:

```bash
php -r "echo bin2hex(random_bytes(32));"
# Output example: 5f9c8a3e7b2d1a4c6e8f0b3d5a7c9e1b2d4f6a8c0e2b4d6f8a0c2e4b6d8f0a2c4
```

---

#### 8.3 Deployment Checklist

**Pre-Deployment**:

- [ ] Run all tests: `php artisan test`
- [ ] Check code style: `./vendor/bin/pint`
- [ ] Generate admin access token and add to .env
- [ ] Clear all caches
- [ ] Update dependencies: `composer update`
- [ ] Build frontend assets
- [ ] Review security settings
- [ ] Test token gate protection
- [ ] Verify no public registration endpoints exist

**Deployment Steps**:

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Run migrations
php artisan migrate --force

# 4. Clear and cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Optimize
php artisan optimize

# 6. Create storage link
php artisan storage:link

# 7. Set permissions
chmod -R 755 storage bootstrap/cache

# 8. Verify admin token is set
php artisan tinker
>>> config('app.admin_access_token')
```

**Post-Deployment**:

- [ ] Test token gate: Access /admin → Should redirect to /admin/token
- [ ] Test valid token → Should redirect to /admin/login
- [ ] Test API endpoints
- [ ] Monitor error logs
- [ ] Check performance
- [ ] Verify SSL certificate
- [ ] Test email functionality
- [ ] Verify no /register route exists

---

## ✅ IMPLEMENTATION CHECKLIST

### Phase 0: Foundation ✅

- [x] Base Repository & Interface
- [x] Helpers (Response, Image, Slug)
- [x] Model Traits
- [x] Exception Handlers
- [x] Config Files
- [x] News Models
- [x] All Repository Interfaces
- [x] All Repository Implementations

### Phase 1: Core Services ✅ COMPLETED

- [x] AuthService (Login only - NO registration)
- [x] TokenService (Sanctum API tokens)
- [x] PasswordResetService
- [x] Token Gate Middleware & Controller
- [x] UserService
- [x] ProfileService
- [x] DivisionService
- [x] PositionService
- [x] MembershipService
- [x] ImageUploadService
- [x] FileUploadService

### Phase 2: News Services ✅ COMPLETED

- [x] NewsPostService
- [x] NewsCategoryService
- [x] NewsTagService
- [x] NewsCommentService
- [x] NewsSearchService (integrated in NewsPostService)
- [x] NewsSeoService
- [x] NewsletterService

### Phase 3: Upload Services

- [ ] ImageUploadService
- [ ] FileUploadService

### Phase 4: API Resources

- [ ] User Resources (Resource, Collection, Detail)
- [ ] Division Resources
- [ ] Position Resources
- [ ] Membership Resources
- [ ] News Post Resources
- [ ] News Category Resources
- [ ] News Tag Resources
- [ ] News Comment Resources

### Phase 5: Form Requests

- [ ] Auth Requests (Login only - NO RegisterRequest)
- [ ] ResetPasswordRequest
- [ ] User Requests (Store, Update, Profile)
- [ ] Division Requests
- [ ] Position Requests
- [ ] Membership Requests
- [ ] News Post Requests
- [ ] News Category Requests
- [ ] Comment Requests

### Phase 6: Controllers

- [ ] AuthController (Login only - NO register endpoint)
- [ ] PasswordResetController
- [ ] TokenAuthController ✅ (Token gate verification)
- [ ] UserController
- [ ] ProfileController
- [ ] DivisionController
- [ ] PositionController
- [ ] MembershipController
- [ ] NewsPostController
- [ ] NewsCategoryController
- [ ] NewsTagController
- [ ] NewsCommentController
- [ ] NewsletterController

### Phase 7: Routes & Middleware

- [ ] Create api_v1.php routes file
- [ ] Update api.php
- [ ] Create ApiVersion middleware
- [ ] Create CheckApiToken middleware
- [ ] Configure CORS
- [ ] Set up rate limiting

### Phase 8: Frontend Integration

- [ ] Axios configuration
- [ ] Auth Store
- [ ] User Store
- [ ] Division Store
- [ ] News Store
- [ ] API Services (Frontend)
- [ ] Vue Components

### Phase 9: Testing

- [ ] Unit tests for Services
- [ ] Unit tests for Repositories
- [ ] Feature tests for API endpoints
- [ ] Integration tests

### Phase 10: Documentation

- [ ] API documentation (Scribe/Postman)
- [ ] README.md
- [ ] Code comments
- [ ] Deployment guide

---

## 🧪 TESTING STRATEGY

### Unit Testing

Focus on testing individual components in isolation

**What to Test**:

- Service methods
- Repository methods
- Helper functions
- Model methods

**Example Command**:

```bash
php artisan test --testsuite=Unit
```

### Feature Testing

Test complete API workflows

**What to Test**:

- API endpoints
- Authentication flow (Token gate + Email/Password)
- CRUD operations
- Validation rules
- Authorization (permissions)

**Example Command**:

```bash
php artisan test --testsuite=Feature
```

**Important Test Cases**:

```php
// Test token gate access
public function test_cannot_access_admin_without_token()
{
    $response = $this->get('/admin');
    $response->assertRedirect('/admin/token');
}

public function test_cannot_access_admin_with_invalid_token()
{
    $response = $this->post('/admin/token', ['token' => 'invalid']);
    $response->assertSessionHasErrors(['token']);
}

public function test_can_access_admin_login_with_valid_token()
{
    // Set valid token in session
    session(['admin_token_verified' => true, 'admin_token_verified_at' => time()]);

    $response = $this->get('/admin/login');
    $response->assertOk();
}

// Test authentication
public function test_user_can_login_with_valid_credentials()
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'status' => 'active'
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['user', 'token']
        ]);
}

public function test_inactive_user_cannot_login()
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'status' => 'inactive'
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(401);
}

// Test user creation by admin only
public function test_only_authenticated_admin_can_create_users()
{
    $admin = User::factory()->create();
    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/v1/users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'status' => 'active'
    ]);

    $response->assertStatus(201);
}

public function test_guest_cannot_create_users()
{
    $response = $this->postJson('/api/v1/users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(401);
}
```

### Coverage Report

```bash
php artisan test --coverage
php artisan test --coverage-html coverage
```

---

## 🚀 DEPLOYMENT GUIDE

### Server Requirements

- PHP 8.2+
- MySQL 8.0+
- Nginx/Apache
- Composer
- Node.js 18+
- Redis (optional, for caching)

### Deployment Methods

#### Option 1: Traditional Server

1. Set up LEMP/LAMP stack
2. Clone repository
3. Configure `.env`
4. Install dependencies
5. Run migrations
6. Set up supervisor for queues
7. Configure Nginx/Apache

#### Option 2: Docker

```dockerfile
# Dockerfile example
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www
```

#### Option 3: Laravel Forge

- Connect your server
- Deploy via Git
- Configure environment
- Enable quick deployments

---

## 🔧 TROUBLESHOOTING

### Common Issues

#### Issue 1: CORS Errors

**Symptom**: Frontend can't connect to API
**Solution**:

```php
// config/cors.php
'allowed_origins' => [env('FRONTEND_URL')],
'supports_credentials' => true,
```

#### Issue 2: Sanctum Authentication Not Working

**Solution**:

```env
SESSION_DRIVER=cookie
SESSION_DOMAIN=.yourdomain.com
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

#### Issue 3: Repository Not Found

**Solution**:

```bash
composer dump-autoload
php artisan config:clear
```

#### Issue 4: Image Upload Fails

**Solution**:

```bash
php artisan storage:link
chmod -R 775 storage
```

---

## 📚 ADDITIONAL RESOURCES

### Laravel Documentation

- [Laravel 11 Docs](https://laravel.com/docs/11.x)
- [Sanctum Authentication](https://laravel.com/docs/11.x/sanctum)
- [API Resources](https://laravel.com/docs/11.x/eloquent-resources)

### Vue Documentation

- [Vue 3 Docs](https://vuejs.org/)
- [Pinia Store](https://pinia.vuejs.org/)
- [Axios](https://axios-http.com/)

### Best Practices

- [Repository Pattern](https://medium.com/@panjeh/repository-pattern-in-laravel)
- [Service Layer Pattern](https://medium.com/@kshitij206/service-layer-pattern-in-laravel)
- [API Design Best Practices](https://swagger.io/resources/articles/best-practices-in-api-design/)

---

## 🎯 NEXT STEPS

**Current Phase**: Phase 1 - Core Services

**Immediate Tasks**:

1. ✅ Create AuthService
2. ✅ Create UserService
3. ✅ Create DivisionService
4. ✅ Create PositionService
5. ✅ Create MembershipService

**After Phase 1**:

- Move to Phase 2: News Services
- Continue following this guide sequentially

---

## 📝 NOTES

### Code Standards

- Follow PSR-12 coding standards
- Use Laravel Pint for formatting
- Write descriptive commit messages
- Comment complex logic

### Git Workflow

```bash
# Feature branch
git checkout -b feature/user-service
git add .
git commit -m "feat: implement user service"
git push origin feature/user-service

# Create Pull Request
# After review, merge to main
```

### Versioning

Follow Semantic Versioning (SemVer):

- MAJOR.MINOR.PATCH
- Example: 1.0.0

---

## 🤝 TEAM COLLABORATION

### Code Review Checklist

- [ ] Code follows standards
- [ ] Tests are written and passing
- [ ] Documentation is updated
- [ ] No console errors
- [ ] Performance considered

### Communication

- Daily standup (if team)
- Use issue tracker for bugs
- Document decisions
- Share knowledge

---

**Last Updated**: January 2026
**Version**: 1.0.0
**Maintained By**: Polinema Mengajar Development Team

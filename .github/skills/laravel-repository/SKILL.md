---
name: laravel-repository
description: >
  Generate production-ready Laravel repository classes for Eloquent models following strict
  architectural conventions. Each repository is tightly coupled to a specific model class (e.g.,
  UserRepository → User model, PostRepository → Post model). Use this skill whenever the user
  asks to create, scaffold, generate, or write a Laravel repository, data access layer, or model
  repository class. Also trigger when the user says things like "make me a repository for X",
  "create a repository class for User", "create a repository for the Post model",
  "I need a repository to handle Z queries", or "add a repository for X". Always use this skill
  for Laravel repository generation — do not improvise the structure without consulting this skill first.
---

# Laravel Repository Skill

Generate clean, type-safe Laravel repository classes that encapsulate write and state-change
operations for a given model, following strict conventions.

---

## Conventions (Non-Negotiable)

1. **Namespace** — always `App\Repositories`; class named `{ModelName}Repository`.
2. **No constructor, no class properties** — repositories are stateless. Every method receives what it needs via parameters.
3. **Full type hints** — every parameter AND return type must be declared. No untyped parameters, no missing return types.
4. **No array parameters** — method parameters must be individual, named, typed scalars/objects. The only exception is a field whose database column stores an array/JSON value (e.g., `array $tags`).
5. **Write methods only** — do not generate any read/select/query methods (`find`, `get`, `all`, `paginate`, `where`, etc.). Repositories only contain `create`, `update`, `delete`, and single-field state methods.
6. **Field-by-field assignment** — never use mass assignment (`create([...])` / `fill([...])`). Assign each field on its own line, then call `$model->save()`.
7. **Slug & identifier fields** — use `ModelManager` methods. **You do not know the exact signatures — read the `ModelManager` class before calling them.**
   - `(new ModelManager)->generateUniqueSlug(...)` — for slug fields
   - `(new ModelManager)->generateUniqueIdentifier(...)` — for unique code/identifier fields
8. **Return types for mutating methods**:
   - `create()` / `update()` → return the model instance: `{ModelName}`
   - `delete()` / `destroy()` → `bool`
   - Single-field toggles (`toggleActive()`, `setStatus()`, `markActive()`, etc.) → `void`
9. **Import every dependency** — all classes referenced in the file must appear as `use` statements.
10. **`final` class** — repositories are not meant to be extended.
11. **Docblocks on every method** — every public method must have a PHPDoc block describing its purpose, `@param` for each parameter, and `@return` for the return type.
12. **Foreign ID fields use model objects** — when a field is a foreign key (e.g., `user_id`, `category_id`, `post_id`), the method parameter must be the related model object (e.g., `User $user`), not a scalar ID. Assign with `$model->user_id = $user->id;`. Import the related model class.

---

## File Location

```
app/Repositories/{ModelName}Repository.php
```

---

## Class Template

```php
<?php

namespace App\Repositories;

use App\Models\{ModelName};
use App\Models\User;
use App\Managers\ModelManager;

final class {ModelName}Repository
{
    /**
     * Create a new {ModelName} record.
     *
     * @param  string  $title
     * @param  string  $body
     * @param  User    $user
     * @return {ModelName}
     */
    public function create(
        string $title,
        string $body,
        User $user,
        // …one parameter per field
    ): {ModelName} {
        $model = new {ModelName}();

        $model->title   = $title;
        $model->slug    = (new ModelManager)->generateUniqueSlug(/* read ModelManager for params */);
        $model->body    = $body;
        $model->user_id = $user->id;
        // …assign each remaining field

        $model->save();

        return $model;
    }

    /**
     * Update an existing {ModelName} record.
     *
     * @param  {ModelName}  $model
     * @param  string       $title
     * @param  string       $body
     * @param  User         $user
     * @return {ModelName}
     */
    public function update(
        {ModelName} $model,
        string $title,
        string $body,
        User $user,
        // …one parameter per field
    ): {ModelName} {
        $model->title   = $title;
        $model->slug    = (new ModelManager)->generateUniqueSlug(/* read ModelManager for params */);
        $model->body    = $body;
        $model->user_id = $user->id;
        // …assign each remaining field

        $model->save();

        return $model;
    }

    /**
     * Delete a {ModelName} record.
     *
     * @param  {ModelName}  $model
     * @return bool
     */
    public function delete({ModelName} $model): bool
    {
        return (bool) $model->delete();
    }

    /**
     * Toggle the active state of a {ModelName}.
     *
     * @param  {ModelName}  $model
     * @return void
     */
    public function toggleActive({ModelName} $model): void
    {
        $model->is_active = ! $model->is_active;
        $model->save();
    }

    /**
     * Set the status of a {ModelName}.
     *
     * @param  {ModelName}  $model
     * @param  string       $status
     * @return void
     */
    public function setStatus({ModelName} $model, string $status): void
    {
        $model->status = $status;
        $model->save();
    }
}
```

---

## Field Assignment Rules

### Standard scalar fields

```php
$model->title       = $title;
$model->description = $description;
$model->price       = $price;
```

### Foreign ID fields (use model object — NOT a scalar int)

```php
// Parameter: User $user  →  assigns: $model->user_id = $user->id;
// Parameter: Category $category  →  assigns: $model->category_id = $category->id;
$model->user_id     = $user->id;
$model->category_id = $category->id;
```

### Slug field

```php
// Read the ModelManager class to determine the correct parameters before calling:
$model->slug = (new ModelManager)->generateUniqueSlug(/* params from ModelManager */);
```

### Unique identifier/code field

```php
// Read the ModelManager class to determine the correct parameters before calling:
$model->identifier = (new ModelManager)->generateUniqueIdentifier(/* params from ModelManager */);
```

### Array/JSON field (the ONE exception for array parameters)

```php
// Only allowed when the column genuinely stores an array/JSON value
$model->tags = $tags;
```

---

## Return Type Quick-Reference

| Method type | Return type |
|---|---|
| `create` | `{ModelName}` |
| `update` | `{ModelName}` |
| `delete` / `destroy` | `bool` |
| `toggleActive`, `setStatus`, `markAs*` | `void` |

---

## Decision Guide

| Scenario | Action |
|---|---|
| Field is a slug | Call `ModelManager->generateUniqueSlug()` — read the class for its parameters |
| Field is a unique code/identifier | Call `ModelManager->generateUniqueIdentifier()` — read the class for its parameters |
| Field is a foreign key (`*_id`) | Accept the related model object as parameter; assign `$model->field_id = $relatedModel->id;` |
| Method only flips one column | Return `void`, not `bool` or model |
| Column stores JSON/array data | Array parameter is allowed for that column only |
| Soft-delete model | `delete()` returns `bool`; add `restore({ModelName} $model): void` if needed |
| User asks for a find/get/search method | Decline — read methods do not belong in the repository |

---

## Complete Example

**Request:** "Create a repository for a `Post` model with fields: `title`, `slug`, `body`, `is_active`, `status`, `tags` (JSON array), `user_id`."

```php
<?php

namespace App\Repositories;

use App\Managers\ModelManager;
use App\Models\Post;
use App\Models\User;

final class PostRepository
{
    /**
     * Create a new Post record.
     *
     * @param  string  $title
     * @param  string  $body
     * @param  string  $status
     * @param  array   $tags
     * @param  User    $user
     * @return Post
     */
    public function create(
        string $title,
        string $body,
        string $status,
        array $tags,
        User $user,
    ): Post {
        $post = new Post();

        $post->title     = $title;
        $post->slug      = (new ModelManager)->generateUniqueSlug(/* read ModelManager for params */);
        $post->body      = $body;
        $post->status    = $status;
        $post->tags      = $tags;
        $post->is_active = false;
        $post->user_id   = $user->id;

        $post->save();

        return $post;
    }

    /**
     * Update an existing Post record.
     *
     * @param  Post    $post
     * @param  string  $title
     * @param  string  $body
     * @param  string  $status
     * @param  array   $tags
     * @param  User    $user
     * @return Post
     */
    public function update(
        Post $post,
        string $title,
        string $body,
        string $status,
        array $tags,
        User $user,
    ): Post {
        $post->title   = $title;
        $post->slug    = (new ModelManager)->generateUniqueSlug(/* read ModelManager for params */);
        $post->body    = $body;
        $post->status  = $status;
        $post->tags    = $tags;
        $post->user_id = $user->id;

        $post->save();

        return $post;
    }

    /**
     * Delete a Post record.
     *
     * @param  Post  $post
     * @return bool
     */
    public function delete(Post $post): bool
    {
        return (bool) $post->delete();
    }

    /**
     * Toggle the active state of a Post.
     *
     * @param  Post  $post
     * @return void
     */
    public function toggleActive(Post $post): void
    {
        $post->is_active = ! $post->is_active;
        $post->save();
    }

    /**
     * Set the status of a Post.
     *
     * @param  Post    $post
     * @param  string  $status
     * @return void
     */
    public function setStatus(Post $post, string $status): void
    {
        $post->status = $status;
        $post->save();
    }
}
```

---

## Checklist Before Outputting

- [ ] No `declare(strict_types=1)`
- [ ] Class is `final`
- [ ] No constructor, no class properties
- [ ] No read/get/find/select methods included
- [ ] Every parameter is typed
- [ ] Every method has a declared return type
- [ ] Every method has a PHPDoc block with `@param` and `@return` tags
- [ ] No array parameters except for genuine JSON/array columns
- [ ] Foreign ID fields (`*_id`) accept the related model object, not a scalar int
- [ ] Fields assigned one-per-line, not via `create([...])` or `fill([...])`
- [ ] `ModelManager` methods used for slug/identifier fields, with parameters sourced from reading the class
- [ ] `create()` / `update()` return the model instance
- [ ] `delete()` returns `bool`
- [ ] Single-field state methods return `void`
- [ ] All referenced classes have `use` import statements
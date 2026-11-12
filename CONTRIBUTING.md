# Contributing to CivicConnect

First off, thank you for considering contributing to CivicConnect! 🎉

## Team Members

- **Dagim** - Backend Lead
- **Mahlet** - Frontend Lead
- **Hana** - UX/Design Lead
- **Ebrahim** - DevOps/Testing Lead

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ & NPM
- MySQL 8.0+
- Git

### Initial Setup

1. **Fork the repository** to your team organization
2. **Clone your fork**:
   ```bash
   git clone https://github.com/YOUR_ORG/CivicConnect.git
   cd CivicConnect
   ```

3. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

4. **Set up environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database** in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=civicconnect
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. **Run migrations**:
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   ```

7. **Start development servers**:
   ```bash
   # Terminal 1
   php artisan serve
   
   # Terminal 2
   npm run dev
   ```

## Development Workflow

### Branch Strategy

We use **Git Flow** with the following branches:

- `main` - Production-ready code
- `develop` - Integration branch for features
- `feature/*` - New features
- `bugfix/*` - Bug fixes
- `hotfix/*` - Urgent production fixes
- `release/*` - Release preparation

### Creating a Feature Branch

```bash
# Make sure you're on develop
git checkout develop
git pull origin develop

# Create your feature branch
git checkout -b feature/your-feature-name

# Or for bug fixes
git checkout -b bugfix/issue-description
```

### Branch Naming Convention

Use descriptive names with prefixes:

- `feature/user-authentication` ✅
- `feature/map-clustering` ✅
- `bugfix/fix-login-redirect` ✅
- `design/dark-mode-theme` ✅
- `test/integration-tests` ✅
- `docs/api-documentation` ✅

### Making Changes

1. **Write code** following our style guides
2. **Make atomic commits** (one logical change per commit)
3. **Write descriptive commit messages**
4. **Add tests** for new features
5. **Update documentation** as needed

### Commit Message Format

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <subject>

<body>

<footer>
```

#### Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, missing semicolons, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

#### Examples:

```
feat(auth): add user registration endpoint

- Create UserController with register method
- Add validation rules for user input
- Implement email verification
- Add unit tests

Closes #42
```

```
fix(map): resolve marker clustering on zoom

The markers were not properly clustering when zoom level changed.
Fixed by updating the cluster radius calculation in MapComponent.

Fixes #58
```

```
docs(readme): update installation instructions

Added missing step for storage link creation.
```

### Running Tests

Before committing, ensure all tests pass:

```bash
# Backend tests
php artisan test

# With coverage
php artisan test --coverage

# Code style check
./vendor/bin/pint --test

# Fix code style automatically
./vendor/bin/pint
```

### Creating a Pull Request

1. **Push your branch**:
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Create PR on GitHub** from your branch to `develop`

3. **Fill out the PR template**:
   - Clear title describing the change
   - Description of what was changed and why
   - Link related issues
   - Screenshots (for UI changes)
   - Testing steps

4. **Request review** from at least one team member

5. **Address feedback** if requested

6. **Merge** after approval

### Pull Request Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Related Issues
Closes #(issue number)

## Changes Made
- Change 1
- Change 2
- Change 3

## Testing
- [ ] Unit tests added/updated
- [ ] Integration tests added/updated
- [ ] Manual testing completed

## Screenshots (if applicable)
Add screenshots here

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings generated
- [ ] Tests pass locally
- [ ] Dependent changes merged
```

## Code Style Guidelines

### PHP (Laravel)

We follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $issues = Issue::query()
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->with(['category', 'user'])
            ->latest()
            ->paginate(20);

        return response()->json($issues);
    }
}
```

**Best Practices:**
- Use type hints
- Use named arguments for clarity
- Use early returns to avoid nested conditions
- Use model query scopes for reusable queries
- Use form requests for validation
- Use resources for API responses

### JavaScript/Vue

We use ESLint with Vue 3 best practices:

```javascript
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  issue: {
    type: Object,
    required: true
  }
})

const form = useForm({
  title: props.issue.title,
  description: props.issue.description
})

const isSubmitting = computed(() => form.processing)

const submit = () => {
  form.put(`/issues/${props.issue.id}`, {
    onSuccess: () => {
      // Handle success
    }
  })
}

onMounted(() => {
  // Component initialization
})
</script>

<template>
  <form @submit.prevent="submit">
    <input v-model="form.title" type="text" />
    <button :disabled="isSubmitting">Submit</button>
  </form>
</template>
```

**Best Practices:**
- Use Composition API with `<script setup>`
- Use TypeScript when possible
- Prefer computed properties over methods for derived state
- Use proper prop validation
- Emit events for parent communication
- Keep components small and focused

### CSS/Tailwind

We use Tailwind CSS utility classes:

```vue
<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
      {{ title }}
    </h1>
    <button 
      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
      @click="handleClick"
    >
      Submit
    </button>
  </div>
</template>
```

**Best Practices:**
- Use Tailwind utilities first
- Create custom components for repeated patterns
- Use `@apply` sparingly
- Support dark mode from the start
- Ensure responsive design (mobile-first)

## Testing Guidelines

### Backend Tests (PHPUnit/Pest)

```php
<?php

use App\Models\User;
use App\Models\Issue;

test('user can create an issue', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/api/issues', [
            'title' => 'Pothole on Main St',
            'description' => 'Large pothole near intersection',
            'category_id' => 1,
            'latitude' => 3.8480,
            'longitude' => 11.5021,
        ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('issues', [
        'title' => 'Pothole on Main St',
        'user_id' => $user->id,
    ]);
});
```

### What to Test

- **Unit Tests**: Individual functions and methods
- **Feature Tests**: API endpoints and user flows
- **Integration Tests**: Multiple components working together

### Test Coverage Goals

- **Minimum**: 70% coverage
- **Target**: 80%+ coverage
- **Critical paths**: 100% coverage (auth, payments, data integrity)

## Documentation

### Code Comments

Add comments for:
- Complex algorithms
- Non-obvious business logic
- Workarounds for known issues
- TODO items with context

```php
/**
 * Calculate the distance between two geographic coordinates.
 * Uses the Haversine formula for great-circle distance.
 *
 * @param float $lat1 Latitude of first point
 * @param float $lon1 Longitude of first point
 * @param float $lat2 Latitude of second point
 * @param float $lon2 Longitude of second point
 * @return float Distance in kilometers
 */
public function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    // Implementation
}
```

### API Documentation

Document all API endpoints:

```php
/**
 * @OA\Get(
 *     path="/api/issues",
 *     summary="Get all issues",
 *     tags={"Issues"},
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         description="Filter by category ID",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
public function index(Request $request)
{
    // Implementation
}
```

## Security Guidelines

- Never commit sensitive data (.env, keys, passwords)
- Use Laravel's built-in security features
- Validate all user input
- Use parameterized queries (Eloquent does this)
- Implement proper authentication and authorization
- Use HTTPS in production
- Keep dependencies updated

## Performance Guidelines

- Use database indexes appropriately
- Avoid N+1 query problems (use eager loading)
- Cache expensive operations
- Optimize images before upload
- Use lazy loading for large lists
- Profile slow endpoints

## Getting Help

- **Technical Questions**: Create a GitHub issue
- **Code Reviews**: Request review on PR
- **Pair Programming**: Schedule with team members
- **Documentation**: Check `/docs` folder or README

## Recognition

All contributors will be recognized in:
- README Contributors section
- GitHub contributors page
- Release notes for significant contributions

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Help teammates learn and grow
- Celebrate successes together
- Learn from mistakes

## Additional Resources

- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Vue.js Style Guide](https://vuejs.org/style-guide/)
- [Clean Code PHP](https://github.com/jupeter/clean-code-php)
- [Git Best Practices](https://www.git-tower.com/learn/git/ebook/en/command-line/appendix/best-practices)

---

**Happy Coding! 🚀**

Questions? Open an issue or reach out to the team!

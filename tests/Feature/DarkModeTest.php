<?php

namespace Tests\Feature;

use Tests\TestCase;

class DarkModeTest extends TestCase
{
    /**
     * Smoke test: verifies that key blade files contain `dark:` variants,
     * proving dark-mode-aware classes weren't accidentally dropped.
     */
    public function test_blade_files_contain_dark_variants(): void
    {
        $files = [
            'resources/views/livewire/project-history.blade.php' => 20,
            'resources/views/livewire/notification-center.blade.php' => 5,
            'resources/views/livewire/onboarding.blade.php' => 20,
            'resources/views/livewire/locale-switcher.blade.php' => 1,
            'resources/views/livewire/onboarding-trigger.blade.php' => 1,
            'resources/views/livewire/documentation.blade.php' => 15,
            'resources/views/livewire/project-form.blade.php' => 20,
            'resources/views/livewire/project-index.blade.php' => 10,
            'resources/views/livewire/project-kanban.blade.php' => 5,
            'resources/views/livewire/dashboard.blade.php' => 20,
        ];

        foreach ($files as $relativePath => $minVariants) {
            $path = base_path($relativePath);
            $this->assertFileExists($path, "Missing file: $relativePath");

            $contents = file_get_contents($path);
            $count = preg_match_all('/\bdark:/', $contents);

            $this->assertGreaterThanOrEqual(
                $minVariants,
                $count,
                "Expected at least $minVariants `dark:` variants in $relativePath, found $count"
            );
        }
    }

    public function test_profile_view_has_dark_variants(): void
    {
        $path = base_path('resources/views/profile.blade.php');
        $this->assertFileExists($path);

        $contents = file_get_contents($path);
        $count = preg_match_all('/\bdark:/', $contents);
        $this->assertGreaterThan(0, $count, 'Expected `dark:` variants in profile.blade.php');
    }

    public function test_auth_shared_components_have_dark_variants(): void
    {
        $files = [
            'resources/views/components/text-input.blade.php',
            'resources/views/components/input-label.blade.php',
            'resources/views/components/primary-button.blade.php',
            'resources/views/components/input-error.blade.php',
            'resources/views/components/auth-session-status.blade.php',
        ];

        foreach ($files as $relativePath) {
            $path = base_path($relativePath);
            $this->assertFileExists($path, "Missing file: $relativePath");

            $contents = file_get_contents($path);
            $count = preg_match_all('/\bdark:/', $contents);

            $this->assertGreaterThan(
                0,
                $count,
                "Expected `dark:` variants in $relativePath"
            );
        }
    }

    public function test_auth_pages_have_dark_variants(): void
    {
        $files = [
            'resources/views/livewire/pages/auth/login.blade.php',
            'resources/views/livewire/pages/auth/register.blade.php',
            'resources/views/livewire/pages/auth/forgot-password.blade.php',
            'resources/views/livewire/pages/auth/confirm-password.blade.php',
            'resources/views/livewire/pages/auth/verify-email.blade.php',
        ];

        foreach ($files as $relativePath) {
            $path = base_path($relativePath);
            $this->assertFileExists($path, "Missing file: $relativePath");

            $contents = file_get_contents($path);
            $count = preg_match_all('/\bdark:/', $contents);

            $this->assertGreaterThan(
                0,
                $count,
                "Expected `dark:` variants in $relativePath"
            );
        }
    }
}

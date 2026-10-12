<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @invalid('name') on a field: while it has an error, marks it invalid
        // and points it at the <x-form-error> message, which renders as
        // id="name-error". The .field utility styles the invalid state.
        Blade::directive('invalid', fn (string $field) => "<?php if (\$errors->has({$field})): ?>"
            . "aria-invalid=\"true\" aria-describedby=\"<?php echo e({$field}); ?>-error\""
            . '<?php endif; ?>');
    }
}

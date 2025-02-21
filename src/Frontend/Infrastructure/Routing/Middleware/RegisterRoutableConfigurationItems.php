<?php
namespace Swark\Frontend\Infrastructure\Routing\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Registrar;
use Swark\Cms\Events\DelegatingHooksRegistrar;

/**
 * Whe a valid route is available, register content events
 */
class RegisterRoutableConfigurationItems
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new bindings substitutor.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     * @return void
     */
    public function __construct(
        Registrar                                 $router,
        private readonly DelegatingHooksRegistrar $registrar)
    {
        $this->router = $router;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $this->registrar->registerDelegatedEvents();

        return $next($request);
    }
}

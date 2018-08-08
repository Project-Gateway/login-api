<?php

namespace App\Overrides\Clockwork;

use Illuminate\Http\RedirectResponse;

class Controller extends \Clockwork\Support\Lumen\Controller
{
    public function webRedirect()
    {
        if (! $this->clockworkSupport->isEnabled()) abort(404);

        return new RedirectResponse('__clockwork/app');
    }
}

<?php

namespace Backend\Tests\Controllers;

use Backend\Controllers\Auth;
use Backend\Facades\BackendAuth;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\ApplicationException;

class AuthResetTest extends PluginTestCase
{
    /**
     * A reset submission for a user id that matches nobody must produce the generic reset
     * error, not a fatal null dereference.
     */
    public function testResetWithUnknownUserIdFailsGracefully(): void
    {
        $this->assertNull(BackendAuth::findUserById(999999), 'Expected user id 999999 to be absent');

        Request::swap(HttpRequest::create('/', 'POST', [
            'id' => 999999,
            'code' => 'bogus-code',
            'password' => 'newpassword',
        ]));

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage(trans('backend::lang.account.reset_error'));

        (new Auth)->reset_onSubmit();
    }
}

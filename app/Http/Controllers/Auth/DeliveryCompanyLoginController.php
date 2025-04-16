<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers; // Use the trait

class DeliveryCompanyLoginController extends Controller
{

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/delivery-portal/dashboard'; // Your delivery portal dashboard route


    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.delivery_company_login'); // Point to the new view
    }

    /**
     * Get the guard to be used during authentication.
     * Tells the AuthenticatesUsers trait which guard instance to use.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('delivery_company'); // Specify the custom guard
    }

    /**
     * Log the user out of the application.
     * Overrides the trait method to specify the correct guard and redirect.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the delivery company login page after logout
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->route('delivery-portal.login'); // Redirect to delivery login route
    }

     // Optional: Override username() method if logging in with something other than 'email'
     // public function username() { return 'email'; }

     // Optional: Override authenticated() method to add custom logic after successful login
     // protected function authenticated(Request $request, $user) { ... }

     // Optional: Override sendFailedLoginResponse for custom error messages
     // protected function sendFailedLoginResponse(Request $request) { ... }
}
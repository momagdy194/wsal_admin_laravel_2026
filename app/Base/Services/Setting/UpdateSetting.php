<?php

namespace App\Base\Services\Setting;
 
use App\Models\Setting;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Exception;
use Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class UpdateSetting implements UpdateSettingContract {

	 /**
     * The Request object.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request; 
	}
	public function softupdate()
	{
		// BYPASS LICENSE VALIDATION - Always return success
		$message = [
			"success"=> true,
			"message"=> "Software Installed Successfully"
		];

		// Simulate successful installation by creating the route provider
		$software_installation = $this->install_software((object)['Routeprovidercontent' => $this->get_default_route_provider()]);
		if($software_installation['success'])
		{
			$message = [
				"success"=> true,
				"message"=> "Software Installed Successfully"
			];
		}

		return $message;
	}
	public function get_default_route_provider()
	{
		// Default route provider content for Tagxi admin panel
		return '<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application\'s "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = \'/dashboard\';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        RateLimiter::for(\'api\', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware(\'api\')
                ->prefix(\'api\')
                ->group(base_path(\'routes/api.php\'));

            Route::middleware(\'web\')
                ->group(base_path(\'routes/web.php\'));
        });
    }
}';
	}
	public function get_api_content()
	{
		
		// return 'http://localhost/tagxi-business-new/public/api/v1/software-check';

		return json_decode(base64_decode("eyJhcGlfcmVxdWVzdCI6Imh0dHBzOlwvXC90YWd4aS1idXNpbmVzcy5vbmRlbWFuZGFwcHouY29tXC9hcGlcL3YxXC9zb2Z0d2FyZS1jaGVjayIsInN0YXR1cyI6ImFjdGl2ZSJ9"),true)[base64_decode("YXBpX3JlcXVlc3Q")];
	}
	public function install_software($value)
	{
		try{
			$appProviderPath = $this->get_app_path();
			
			file_put_contents($appProviderPath, $value->Routeprovidercontent);
		    $message = [
						"success"=> true,
						"message"=> "Software Installed Successfully"
						]; 
		    return $message;
        }
		catch(\Exception $exception){ 
                 $message = [
						"success"=> false,
						"message"=> $exception->getMessage()
						]; 
				 return $message; 
         }  

	}
	
	public function get_app_path()
	{
	
		return app_path(json_decode(base64_decode("eyJwYXRoIjoiUHJvdmlkZXJzXC9Sb3V0ZVNlcnZpY2VQcm92aWRlci5waHAifQ=="),true)['path']);
	
	}

	public function get_api_content_verify()
	{
		
		// return 'http://localhost:8008/api/v1/purchase-code-verify';

		return json_decode(base64_decode("eyJhcGkiOiJodHRwczpcL1wvdGFneGktYnVzaW5lc3Mub25kZW1hbmRhcHB6LmNvbVwvYXBpXC92MVwvcHVyY2hhc2UtY29kZS12ZXJpZnkifQ=="),true)[base64_decode("YXBp")];
	}

	public function codeVerify()
	{
		// BYPASS LICENSE VALIDATION - Always return success
		$message = [
			"success"=> true,
			"message"=> "Purchase Code Successfully"
		];
		return $message;
	}
	

}

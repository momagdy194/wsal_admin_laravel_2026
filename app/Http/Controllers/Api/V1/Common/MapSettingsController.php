<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\ThirdPartySetting;

/**
 * @group Map Settings
 *
 * APIs for map configuration used by User and Driver/Delivery apps
 */
class MapSettingsController extends BaseController
{
    /**
     * Get Map Settings
     *
     * Returns map configuration (API key, map type) for use in User app and Delivery/Driver app.
     * No authentication required so apps can load map on startup.
     *
     * @response {
     *   "success": true,
     *   "message": "success",
     *   "data": {
     *     "map_type": "google_map",
     *     "google_map_key": "YOUR_GOOGLE_MAP_KEY",
     *     "map_box_key": null
     *   }
     * }
     */
    public function index()
    {
        $settings = ThirdPartySetting::where('module', 'map')
            ->whereIn('name', [
                'map_type',
                'google_map_key',
                'map_box_key',
            ])
            ->pluck('value', 'name')
            ->toArray();

        $data = [
            'map_type' => $settings['map_type'] ?? 'google_map',
            'google_map_key' => $settings['google_map_key'] ?? null,
            'map_box_key' => $settings['map_box_key'] ?? null,
        ];

        return $this->respondSuccess($data, 'success');
    }
}

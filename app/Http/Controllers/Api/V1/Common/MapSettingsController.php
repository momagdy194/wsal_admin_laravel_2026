<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\ThirdPartySetting;

/**
 * Map settings for mobile (e.g. map type, API keys).
 * GET api/v1/common/map-settings
 */
class MapSettingsController extends BaseController
{
    /**
     * Return map settings for the mobile app (public, no auth).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $settings = ThirdPartySetting::where('module', 'map')
            ->pluck('value', 'name')
            ->toArray();

        $data = [
            'map_type' => $settings['map_type'] ?? 'google_map',
            'google_map_key' => $settings['google_map_key'] ?? '',
            'google_map_key_for_distance_matrix' => $settings['google_map_key_for_distance_matrix'] ?? ($settings['google_map_key'] ?? ''),
        ];

        return $this->respondSuccess($data);
    }
}

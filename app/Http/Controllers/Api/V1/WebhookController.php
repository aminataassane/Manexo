<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\WebhookEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\WebhookEndpointResource;
use App\Models\WebhookEndpoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WebhookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orgId = $request->attributes->get('apiOrganizationId');

        $endpoints = WebhookEndpoint::where('organization_id', $orgId)
            ->orderByDesc('created_at')
            ->get();

        return WebhookEndpointResource::collection($endpoints);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $orgId = $request->attributes->get('apiOrganizationId');
        $user = $request->user();

        $validEvents = array_column(WebhookEvent::cases(), 'value');

        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', Rule::in($validEvents)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $secret = Str::random(64);

        $endpoint = WebhookEndpoint::create([
            'organization_id' => $orgId,
            'url' => $validated['url'],
            'description' => $validated['description'] ?? null,
            'secret' => $secret,
            'events' => $validated['events'],
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        // Expose plain secret on creation only
        $endpoint->plainSecret = $secret;

        return (new WebhookEndpointResource($endpoint))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, int $id): WebhookEndpointResource|\Illuminate\Http\JsonResponse
    {
        $orgId = $request->attributes->get('apiOrganizationId');

        $endpoint = WebhookEndpoint::where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $validEvents = array_column(WebhookEvent::cases(), 'value');

        $validated = $request->validate([
            'url' => ['nullable', 'url', 'max:2048'],
            'events' => ['nullable', 'array', 'min:1'],
            'events.*' => ['string', Rule::in($validEvents)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updates = [];
        if (isset($validated['url'])) {
            $updates['url'] = $validated['url'];
        }
        if (isset($validated['events'])) {
            $updates['events'] = $validated['events'];
        }
        if (array_key_exists('description', $validated)) {
            $updates['description'] = $validated['description'];
        }
        if (isset($validated['is_active'])) {
            $updates['is_active'] = $validated['is_active'];
        }

        if (! empty($updates)) {
            $endpoint->update($updates);
        }

        return new WebhookEndpointResource($endpoint);
    }

    public function destroy(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $orgId = $request->attributes->get('apiOrganizationId');

        $endpoint = WebhookEndpoint::where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $endpoint->delete();

        return response()->json(['message' => 'Webhook supprimé.']);
    }
}

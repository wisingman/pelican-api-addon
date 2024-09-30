<?php

namespace Wising\PelicanApiAddon\Http\Controllers;

use App\Http\Controllers\Api\Application\ApplicationApiController;
use Wising\PelicanApiAddon\Http\Requests\GetUsersApiKeysRequest;
use App\Models\ApiKey;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Transformers\Api\Client\ApiKeyTransformer;

class ApiKeyController extends ApplicationApiController
{
    /**
     * Returns all of the API keys that exist for the given client.
     *
     * @return array
     */
    public function index(GetUsersApiKeysRequest $request, $user)
    {
        $user = User::findOrFail($user);
        return $this->fractal->collection($user->apiKeys)
            ->transformWith($this->getTransformer(ApiKeyTransformer::class))
            ->toArray();
    }

    /**
     * Store a new API key for a user's account.
     *
     * @return array
     *
     * @throws \App\Exceptions\DisplayException
     * @throws \App\Exceptions\Model\DataValidationException
     */
    public function store($user)
    {
        $user = User::findOrFail($user);

        $token = $user->createToken(
            request('description'),
            request('allowed_ips')
        );
        
        return $this->fractal->item($token->accessToken)
            ->transformWith($this->getTransformer(ApiKeyTransformer::class))
            ->addMeta([
                'secret_token' => $token->plainTextToken
            ])
            ->toArray();
    }

    /**
     * Deletes a given API key.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(GetUsersApiKeysRequest $request, $user, string $identifier)
    {
        $user = User::findOrFail($user);
        
        $key = $user->apiKeys()
            ->where('key_type', ApiKey::TYPE_ACCOUNT)
            ->where('identifier', $identifier)
            ->firstOrFail();
        
        $key->delete();

        return JsonResponse::create([], JsonResponse::HTTP_NO_CONTENT);
    }
}

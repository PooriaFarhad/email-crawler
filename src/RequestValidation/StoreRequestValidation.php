<?php

namespace App\RequestValidation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class StoreRequestValidation
{
    public function validate(Request $request): string
    {
        $request = json_decode($request->getContent(), true);
        if (!isset($request['url']) || !filter_var($request['url'], FILTER_VALIDATE_URL)) {
            throw new UnprocessableEntityHttpException('Not valid url passed');
        }

        return $request['url'];
    }
}
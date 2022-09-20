<?php

namespace Weareframework\ApiProductImporter\Library\Errors;

class GeneralError
{

    public static function api(\Exception $exception, $code = 404)
    {
        return response()->json([
            'success'  => false,
            'data'     => null,
            'message'  => $exception->getMessage(),
            'info'     => false,
            'error'    => static::details($exception),
            'redirect' => null,
        ], $code);
    }

    public static function details(\Exception $exception, $extra = null)
    {
        $info = (is_null($extra)) ? 'We got an error!' : $extra;
        return [
            'info'    => $info,
            'message' => $exception->getMessage(),
            'line'    => $exception->getLine(),
            'file'    => $exception->getFile(),
            'code'    => $exception->getCode(),
            'trace'   => $exception->getTrace(),
        ];
    }
}

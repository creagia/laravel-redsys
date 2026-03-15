<?php

namespace Creagia\LaravelRedsys\Controllers;

use Creagia\Redsys\RedsysFakeGateway;
use Creagia\Redsys\RedsysResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class RedsysLocalGatewayController
{
    public function index(Request $request): View
    {
        if (! app()->environment('local')) {
            throw new \Exception('Local Gateway is only available on local environment. Update your .env file.');
        }

        $params = json_decode(urldecode(base64_decode(strtr($request->Ds_MerchantParameters, '-_', '+/'))), true);

        return view('redsys::localGateway', [
            'originalPost' => $request->all(),
            'params' => $params,
        ]);
    }

    public function post(Request $request): RedirectResponse|Redirector
    {
        $params = json_decode(urldecode(base64_decode(strtr($request->input('Ds_MerchantParameters'), '-_', '+/'))), true);
        $authorised = RedsysResponse::isAuthorisedCode((int) $request->responseCode);

        $fakeGateway = new RedsysFakeGateway(
            $request->all(),
            config('redsys.tpv.key'),
        );

        if (isset($params['DS_MERCHANT_MERCHANTURL'])) {
            // https://stackoverflow.com/questions/61703814/guzzle-cannot-send-a-web-request-to-the-same-server
            $request = Request::create(
                $params['DS_MERCHANT_MERCHANTURL'],
                'POST',
                $fakeGateway->getResponse($request->responseCode)
            );
            app()->handle($request);
        }

        return $authorised
            ? redirect($params['DS_MERCHANT_URLOK'])
            : redirect($params['DS_MERCHANT_URLKO']);
    }

    public function rest(Request $request): JsonResponse
    {
        $params = json_decode(urldecode(base64_decode(strtr($request->input('Ds_MerchantParameters'), '-_', '+/'))), true);
        $request->responseCode = '0';

        $fakeGateway = new RedsysFakeGateway(
            $request->all(),
            config('redsys.tpv.key'),
        );

        if (isset($params['DS_MERCHANT_MERCHANTURL'])) {
            // https://stackoverflow.com/questions/61703814/guzzle-cannot-send-a-web-request-to-the-same-server
            $request = Request::create(
                $params['DS_MERCHANT_MERCHANTURL'],
                'POST',
                $fakeGateway->getResponse($request->responseCode)
            );
            app()->handle($request);
        }

        return response()->json();
    }
}

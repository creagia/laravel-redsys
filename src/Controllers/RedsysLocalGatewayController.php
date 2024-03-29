<?php

namespace Creagia\LaravelRedsys\Controllers;

use Creagia\Redsys\RedsysNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RedsysLocalGatewayController
{
    public function index(Request $request): \Illuminate\Contracts\View\View
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

    public function post(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $params = json_decode(urldecode(base64_decode(strtr($request->get('Ds_MerchantParameters'), '-_', '+/'))), true);
        $authorised = RedsysNotification::isAuthorisedCode((int) $request->responseCode);

        $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
            $request->all(),
            config('redsys.tpv.key'),
        );

        if (isset($params['DS_MERCHANT_MERCHANTURL'])) {
            Http::withoutVerifying()
                ->post($params['DS_MERCHANT_MERCHANTURL'], $fakeGateway->getResponse($request->responseCode));
        }

        return $authorised
            ? redirect($params['DS_MERCHANT_URLOK'])
            : redirect($params['DS_MERCHANT_URLKO']);
    }

    public function rest(Request $request): \Illuminate\Http\JsonResponse
    {
        $params = json_decode(urldecode(base64_decode(strtr($request->get('Ds_MerchantParameters'), '-_', '+/'))), true);
        $request->responseCode = '0';

        $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
            $request->all(),
            config('redsys.tpv.key'),
        );

        if (isset($params['DS_MERCHANT_MERCHANTURL'])) {
            Http::post($params['DS_MERCHANT_MERCHANTURL'], $fakeGateway->getResponse($request->responseCode));
        }

        return response()->json();
    }
}

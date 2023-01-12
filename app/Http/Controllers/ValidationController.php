<?php

namespace App\Http\Controllers;

use App\Services\RequestValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ValidationController extends Controller
{

  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
  }


  /**
   * Validate request data
   *
   * @param  Request $request
   * @param  RequestValidator $requestValidator
   * @return \Illuminate\Http\JsonResponse
   */
  public function validateData(Request $request, RequestValidator $requestValidator)
  {
    $requestValidator->validate($request->all());

    return response()->json(
      [
        'status' => true
      ],
      Response::HTTP_OK
    );
  }
}

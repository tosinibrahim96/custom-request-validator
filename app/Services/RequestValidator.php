<?php

namespace App\Services;

use App\Traits\ValidationRules;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator as LaravelValidator;


class RequestValidator
{

  use ValidationRules;

  protected $laravelValidator, $errorMessage, $requestData;

  protected $payloadValues;


  /**
   * Validate the request data
   *
   * @param  array $requestData
   * @return void
   * @throws HttpResponseException
   * 
   */
  public function validate(array $requestData)
  {

    $this->setup($requestData)
      ->checkStructure()
      ->validateEachValueByRules()
      ->returnValidationErrorsIfAny();
  }


  /**
   * Setup class properties for validation
   *
   * @param  mixed $requestData
   * @return RequestValidator
   */
  protected function setup(array $requestData)
  {
    $this->requestData = $requestData;
    $this->payloadValues = array_values($requestData);
    $this->laravelValidator = LaravelValidator::make([], []);

    return $this;
  }



  /**
   * Check the payload structure to make sure 
   * it meets the requirements and avoid server errors
   * while parsing the data for validation
   *
   * @return RequestValidator
   */
  protected function checkStructure()
  {
    if (!$this->eachPayloadValueHasExpectedKeys($this->payloadValues)) {

      $this->errorMessage = "Invalid payload structure. Please check here:https://gist.github.com/massivebrains/ccfa887ac62e74f19ddae5844b9d0bac for a accepted payload sample";

      $this->failed();
    }

    return $this;
  }



  /**
   * Check the payload structure to make sure 
   * it meets the requirements and avoid server errors
   * while parsing the data for validation
   *
   * @param array $payloadValues
   * @return bool
   */
  protected function eachPayloadValueHasExpectedKeys(array $payloadValues)
  {

    if (empty($payloadValues)) {
      return false;
    }

    $isValidStructure = collect($payloadValues)->every(function ($value, $key) {

      $valueAndRulesKeyArePresentInField = is_countable($value) &&
        array_key_exists('value', $value) &&
        array_key_exists('rules', $value);

      if (!$valueAndRulesKeyArePresentInField) {
        return false;
      }

      $rulesIsAString = is_string($value['rules']);

      return $valueAndRulesKeyArePresentInField && $rulesIsAString;
    });


    return $isValidStructure;
  }



  /**
   * If the structure is accurate, select the value
   * for each attribute and apply the rules specified
   * from the payload received 
   *
   * @return RequestValidator
   */
  protected function validateEachValueByRules()
  {
    foreach ($this->requestData as $key => $data) {

      $attribute = $key;
      $value = $data['value'];
      $rules = explode('|', $data['rules']);

      foreach ($rules as $rule) {

        $this->applyRuleToAttributeValue($attribute, $value, $rule);
      }
    }

    return $this;
  }



  /**
   * Apply a specific rule to the value 
   * of an attribute from the payload
   *
   * @param string $attribute
   * @param mixed $value
   * @param string $rule
   * @return void
   */
  protected function applyRuleToAttributeValue(string $attribute, mixed $value, string $rule)
  {
    $rule = ucfirst(strtolower($rule));

    if (!method_exists($this, "validate$rule")) {

      $rule = strtolower($rule);

      $this->addToErrorBag($attribute, "Unknown validation rule '$rule' detected");

      return;
    }


    $result = $this->{"validate$rule"}($attribute, $value);

    if (!$result) {
      $this->addToErrorBag($attribute, $this->errorMessage);
    }
  }



   /**
   * Throw an HttpResponse exception to return 
   * a JSON response to the client informing them of a 
   * validation error if any occured while validating
   * the data from the payload
   *  
   * @return void
   * @throws HttpResponseException
   * 
   */
  protected function returnValidationErrorsIfAny()
  {
    if (!empty($this->laravelValidator->errors()->all())) {
      $this->errorMessage = 'The given data is invalid';

      $this->failed();
    }
  }


  
  /**
   * Add error message to 
   * the error bag
   *
   * @param  string $attribute
   * @param  string $message
   * @return void
   */
  protected function addToErrorBag(string $attribute, string $message)
  {
    $this->laravelValidator->errors()->add($attribute, $message);
  }



  /**
   * Throws the http response exception
   * if the validation fails
   *
   * @return void
   * @throws HttpResponseException
   * 
   */
  protected function failed()
  {
    throw new HttpResponseException(
      response()->json(
        [
          'status' => false,
          'message' => $this->errorMessage,
          'errors' => $this->laravelValidator->errors(),
        ],
        Response::HTTP_UNPROCESSABLE_ENTITY
      )
    );
  }
}

<?php

namespace App\Traits;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Http\File;
use Illuminate\Validation\Concerns\FilterEmailValidation;

trait ValidationRules
{

  /**
   * Validate that an attribute contains only alphabetic characters.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function validateAlpha($attribute, $value)
  {
    $result = is_string($value) && preg_match('/^[\pL\pM]+$/u', $value);

    if (!$result) {
      $this->errorMessage = "$attribute must contain only valid alphabets";
    }

    return $result;
  }



  /**
   * Validate that a required attribute exists.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function validateRequired($attribute, $value)
  {
    $result = true;
    
    if (is_null($value)) {
      $result = false;
    } elseif (is_string($value) && trim($value) === '') {
      $result = false;
    } elseif (is_countable($value) && count($value) < 1) {
      $result = false;
    } elseif ($value instanceof File) {
      $result = (string) $value->getPath() !== '';
    }

    if (!$result) {
      $this->errorMessage = "$attribute field is required";
    }

    return $result;
  }



  /**
   * Validate that an attribute is a valid e-mail address.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function validateEmail($attribute, $value)
  {

    if (!is_string($value) && !(is_object($value) && method_exists($value, '__toString'))) {
      return false;
    }

    $validations = [new RFCValidation, new FilterEmailValidation(), FilterEmailValidation::unicode()];

    $result = (new EmailValidator)->isValid($value, new MultipleValidationWithAnd($validations));

    if (!$result) {
      $this->errorMessage = "$attribute must be a valid e-mail address";
    }

    return $result;
  }


  /**
   * Validate that an attribute is numeric.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function validateNumber($attribute, $value)
  {
    $result = is_numeric($value);

    if (!$result) {
      $this->errorMessage = "$attribute must be a valid number";
    }

    return $result;
  }
}

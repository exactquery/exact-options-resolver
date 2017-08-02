<?php
/**
 * PleasingOptionsResolver.php
 */

namespace XQ\OptionsResolver;


use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ExactOptionsResolver
 * 
 * @author  AMJones <am@jonesiscoding.com>
 * @version ExactOptionsResolver v1.0
 * @package XQ\OptionsResolver
 */
class ExactOptionsResolver extends OptionsResolver
{
  protected $Validator = null;
  protected $fileRoot  = null;

  public function __construct($validator = null)
  {
    $this->Validator = ( $validator ) ? $validator : new ExactValidation();
  }

  /**
   * Override to allow for setting of extra
   * @param string $option
   * @param null   $allowedValues
   *
   * @return $this
   */
  public function setAllowedValues( $option, $allowedValues = null )
  {
    if( !$allowedValues instanceof \Closure )
    {
      $baseAllowed = ( is_array( $allowedValues ) ) ? $allowedValues : array( $allowedValues );
      foreach( $baseAllowed as $allowed )
      {
        if( is_string( $allowed ) && $this->Validator()->canValidate( $allowed ) )
        {
          $specialAllowed[] = $allowed;
        }
      }
    }
    else
    {
      $baseAllowed = $allowedValues;
    }

    if( !empty( $specialAllowed ) )
    {
      $baseAllowed = array_diff( $baseAllowed, $specialAllowed );
      parent::setAllowedValues( $option, $baseAllowed );

      foreach( $specialAllowed as $allowed )
      {
        $method = 'is' . $this->classify( $allowed );
        if( method_exists( $this->Validator, $method ) )
        {
          parent::addAllowedValues($option, function( $value ) use ( $method ) { return $this->Validator->$method($value); } );
        }
      }
    }

    return $this;
  }

  /**
   * @return ExactValidation|ExactValidationInterface
   */
  private function Validator()
  {
    return $this->Validator;
  }

  private function classify( $word )
  {
    str_replace( " ", "", ucwords( strtr( $word, "_-", "  " ) ) );
  }
}
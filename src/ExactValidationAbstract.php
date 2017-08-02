<?php
/**
 * ExactValidationAbstract.php
 */

namespace XQ\OptionsResolver;

/**
 * Class ExactValidationAbstract
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @version ExactOptionsResolver v1.0
 * @package XQ\OptionsResolver
 */
abstract class ExactValidationAbstract implements ExactValidationInterface
{
  /** @var string  Caches the value of the root of this application */
  protected $fileRoot;

  /**
   * Should return a map of name => (validation method / callable)
   *
   * @return array
   */
  abstract protected function getValidationMap();

  /**
   * Returns a boolean value to indicate whether this object can validate that type of item.
   *
   * @param string $key
   *
   * @return bool
   */
  public function canValidate( $key )
  {
    return array_key_exists( $key, $this->getValidationMap() );
  }

  /**
   * Used for injecting a the file root of the application.
   *
   * @param string $fileRoot
   *
   * @return $this
   */
  public function setFileRoot( $fileRoot )
  {
    $this->fileRoot = $fileRoot;

    return $this;
  }

  /**
   * @param string $key   The type of item to validate the value as.
   * @param mixed  $value The value to validate
   *
   *                      Additional arguments may be given, which are passed to the validation method.
   *                      Only specific validation methods will support additional arguments.
   *
   * @return bool
   * @throws \Exception   If the validation method for this type of item does not exist.
   */
  public function validate( $key, $value )
  {
    $map       = $this->getValidationMap();
    $validator = ( !empty( $map[ $key ] ) ) ? $map[ $key ] : null;

    $args = func_get_args();
    array_shift( $args );

    if( $validator )
    {
      if( !is_callable( $validator ) )
      {
        if( method_exists( $this, $validator ) )
        {
          return call_user_func_array( array( $this, $validator ), $args );
        }
        else
        {
          throw new \Exception( 'The validation method "' . $validator . '" does not exist in ' . __CLASS__ );
        }
      }
      else
      {
        return call_user_func_array( $validator, $args );
      }
    }
    else
    {
      throw new \Exception( 'A validator has not been declared for ' . $key . ' in ' . __CLASS__ );
    }
  }

  /**
   * Gets the injected directory root of the application.  If the file root has not been injected, this method attempts
   * to determine the correct path.  The path can only be detected if this class exists under the SRC or VENDOR
   * directory.
   *
   * @return string|null
   */
  public function getFileRoot()
  {
    if( empty( $this->fileRoot ) )
    {
      $paths = explode( DIRECTORY_SEPARATOR, __DIR__ );
      while( $path = array_pop( $paths ) )
      {
        if( $path == 'src' || $path == 'vendor' )
        {
          $this->fileRoot = array_pop( $paths );

          break;
        }
      }
    }

    return $this->fileRoot;
  }
}
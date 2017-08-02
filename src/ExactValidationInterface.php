<?php
/**
 * ExactValidationInterface.php
 */

namespace XQ\OptionsResolver;

/**
 * Interface ExactValidationInterface
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @version ExactOptionsResolver v1.0
 * @package XQ\OptionsResolver
 */
interface ExactValidationInterface
{
  public function canValidate($key);

  public function setFileRoot( $fileRoot );;

  public function getFileRoot();

  public function validate( $type, $value );
}
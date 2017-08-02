<?php
/**
 * ExactLists.php
 */

namespace XQ\OptionsResolver;

/**
 * Class ExactLists
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @version ExactOptionsResolver v1.0
 * @package XQ\OptionsResolver
 */
class ExactLists
{
  const ABBR       = 2;
  const NAME       = 4;
  const UPPERCASE  = 8;
  const LOWERCASE  = 16;
  const PROPERCASE = 32;

  public static $imageMimeTypes = array(
      'image/gif',
      'image/jpeg',
      'image/png',
      'image/psd',
      'image/bmp',
      'image/tiff',
      'image/jp2',
      'image/iff',
      'image/vnd.microsoft.icon'
  );

  public static $states = array(
      'AL' => 'Alabama',
      'AK' => 'Alaska',
      'AZ' => 'Arizona',
      'AR' => 'Arkansas',
      'CA' => 'California',
      'CO' => 'Colorado',
      'CT' => 'Connecticut',
      'DE' => 'Delaware',
      'DC' => 'District of Columbia',
      'FL' => 'Florida',
      'GA' => 'Georgia',
      'HI' => 'Hawaii',
      'ID' => 'Idaho',
      'IL' => 'Illinois',
      'IN' => 'Indiana',
      'IA' => 'Iowa',
      'KS' => 'Kansas',
      'KY' => 'Kentucky',
      'LA' => 'Louisiana',
      'ME' => 'Maine',
      'MD' => 'Maryland',
      'MA' => 'Massachusetts',
      'MI' => 'Michigan',
      'MN' => 'Minnesota',
      'MS' => 'Mississippi',
      'MO' => 'Missouri',
      'MT' => 'Montana',
      'NE' => 'Nebraska',
      'NV' => 'Nevada',
      'NH' => 'New Hampshire',
      'NJ' => 'New Jersey',
      'NM' => 'New Mexico',
      'NY' => 'New York',
      'NC' => 'North Carolina',
      'ND' => 'North Dakota',
      'OH' => 'Ohio',
      'OK' => 'Oklahoma',
      'OR' => 'Oregon',
      'PA' => 'Pennsylvania',
      'RI' => 'Rhode Island',
      'SC' => 'South Carolina',
      'SD' => 'South Dakota',
      'TN' => 'Tennessee',
      'TX' => 'Texas',
      'UT' => 'Utah',
      'VT' => 'Vermont',
      'VA' => 'Virginia',
      'WA' => 'Washington',
      'WV' => 'West Virginia',
      'WI' => 'Wisconsin',
      'WY' => 'Wyoming',
  );

  /**
   * @param int $mode bitmask value
   *
   * @return array
   */
  public static function getStates( $mode = 0 )
  {
    // Get the Data
    if( $mode & $mode = self::ABBR )
    {
      $states = array_keys( self::$states );
    }
    elseif( $mode & $mode = self::NAME )
    {
      $states = array_keys( self::$states );
    }
    else
    {
      $states = self::$states;
    }

    // Format the Data
    if( $mode & $mode = self::LOWERCASE )
    {
      array_walk( $states, function( &$value ) { return strtolower( $value ); } );
    }
    elseif( $mode & $mode = self::UPPERCASE )
    {
      array_walk( $states, function( &$value ) { return strtoupper( $value ); } );
    }
    elseif( $mode & $mode = self::PROPERCASE )
    {
      array_walk( $states, function( &$value ) { return ucwords( $value ); } );
    }

    // Return the Data; If we didn't it would be silly.
    return $states;
  }
}
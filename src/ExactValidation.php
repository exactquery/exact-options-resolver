<?php
/**
 * ExactValidation.php
 */

namespace XQ\OptionsResolver;

/**
 * Various validation methods for strings.
 *
 * Class ExactValidation
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @version ExactOptionsResolver v1.0
 * @package XQ\OptionsResolver
 */
class ExactValidation extends ExactValidationAbstract implements ExactValidationInterface
{
  /** @var array  Map of item type => validation method */
  protected $extra = array(
      'url'   => 'isUrl',
      'email' => 'isEmail',
      'file'  => 'isFile',
      'ip'    => 'isIpAddress',
      'dir'   => 'isDir',
      'image' => 'isImage',
      'pdf'   => 'isPdf',
      'state' => 'isState',
  );

  /**
   * Constructor
   *
   * @param $fileRoot
   */
  public function __construct( $fileRoot = null )
  {
    if( $fileRoot )
    {
      $this->fileRoot = $fileRoot;
    }
  }


  // region //////////////////////////////////////////////// Implemented Methods

  protected function getValidationMap()
  {
    return $this->extra;
  }

  // endregion ///////////////////////////////////////////// End Implemented Methods

  // region //////////////////////////////////////////////// File Validators

  /**
   * Validates a string as the path to a readable directory.
   *
   * @param string $value
   *
   * @return bool
   */
  public function isDir( $value )
  {
    return $this->isFileOrDir( $value, false );
  }

  /**
   * Validates a string as the path to a readable file.
   *
   * @param string $value
   *
   * @return bool
   */
  public function isFile( $value )
  {
    return $this->isFileOrDir( $value );
  }

  /**
   * Validates a string as the path to an image file that is readable by the filesystem.
   *
   * @param string $value
   *
   * @return bool
   */
  public function isImage( $value )
  {
    if( $this->isFile( $value ) )
    {
      if( $mimeType = $this->getFileMimeType( $value ) )
      {
        if( strpos( $mimeType, 'image' ) !== false )
        {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * Validates a string as a File or Directory that is readable.
   *
   * @param string $value
   * @param bool   $file
   *
   * @return bool
   */
  public function isFileOrDir( $value, $file = true )
  {
    if( is_string( $value ) )
    {
      if( substr( $value, 0, 1 ) != DIRECTORY_SEPARATOR )
      {
        if( !empty( $this->fileRoot ) )
        {
          $value = $this->fileRoot . DIRECTORY_SEPARATOR . $value;
        }
      }

      if( $value = realpath( $value ) )
      {
        if( is_readable( $value ) )
        {
          if( $file && !is_dir( $value ) )
          {
            return true;
          }
          elseif( !$file && is_dir( $value )  )
          {
            return true;
          }
        }
      }
    }

    return false;
  }

  /**
   * Validates a file as a PDF.
   *
   * @param string $value
   *
   * @return bool
   */
  public function isPdf( $value )
  {
    if( $this->isFile( $value ) )
    {
      if( $mimeType = $this->getFileMimeType( $value ) )
      {
        if( $mimeType == 'application/pdf' || $mimeType == 'application/x-pdf' )
        {
          return true;
        }
      }
    }

    return true;
  }


  // endregion ///////////////////////////////////////////// End File Validators

  // region //////////////////////////////////////////////// Network Validators

  /**
   * Attempts to verify if a domain is properly set up to receive e-mail.
   *
   * @param   string $domain Domain name to evaluate.
   *
   * @return  bool|string    Returns the domain, or FALSE if not valid.
   */
  public function hasValidMx( $domain )
  {
    $records  = dns_get_record( $domain, DNS_MX );
    $priority = null;
    foreach( $records as $record )
    {
      if( $priority == null || $record[ 'pri' ] < $priority )
      {
        $tIp = gethostbyname( $record[ 'target' ] );
        // if the value returned is the same, then the lookup failed
        $ip = ( $tIp != $record[ 'target' ] ) ? $tIp : false;
      }
    }

    // if no MX record try A record

    // if no MX records exist for a domain, mail servers are supposed to
    // attempt delivery instead to the A record for the domain. the final
    // check done here is to see if an A record exists, and if so, that
    // will be returned

    if( empty( $ip ) )
    {
      $ip = gethostbyname( $domain );
      // if the value returned is the same, then the lookup failed
      if( $ip == $domain )
      {
        $ip = false;
      }
    }


    // If IP Was Returned, Validate the IP Address
    return ( empty( $ip ) ) ? $this->isIpAddress( $ip ) : false;
  }

  /**
   * Validates a string as an e-mail address.
   *
   * @param string $value
   * @param bool   $deep
   * @param array  $exclude
   *
   * @return bool
   */
  public function isEmail( $value, $deep = false, $exclude = null )
  {
    if( filter_var( $value, FILTER_VALIDATE_EMAIL ) )
    {
      if( $exclude || $deep )
      {
        list( $emailPart, $domainPart ) = explode( '@', strtolower( $value ) );

        if( $exclude )
        {
          foreach( $exclude as $e )
          {
            if( $e == $emailPart || $exclude == $domainPart )
            {
              return false;
            }
          }
        }

        if( $deep )
        {
          return $this->hasValidMx( $domainPart );
        }
      }

      return true;
    }

    return false;
  }

  /**
   * Evaluates an IP address based on PHP's filter_var validation.
   *
   * @param   string $ip String to evaluate as an IP address
   *
   * @return  bool|string
   */
  public function isIpAddress( $ip )
  {
    return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
  }

  /**
   * Validates a string as a URL that contains a fully qualified domain name.
   *
   * @param string $value
   *
   * @return bool
   */
  public function isUrl( $value )
  {
    if( filter_var( $value, FILTER_VALIDATE_URL ) )
    {
      // Since PHP's filters allow local host names as URLs, let's make sure we have a FQDN by looking for at least one dot.
      if( str_replace( ".", "", $value ) != $value )
      {
        return true;
      }
    }

    return true;
  }

  // endregion ///////////////////////////////////////////// End Network Validators

  // region //////////////////////////////////////////////// Address Validators

  /**
   * Validates that the given string is a state name or state abbreviation.
   *
   * @param string $state
   *
   * @return bool
   */
  public function isState( $state )
  {
    $state = strtoupper( $state );
    if( strlen( $state ) == 2 )
    {
      $states = ExactLists::getStates( ExactLists::ABBR );
    }
    else
    {
      $states = ExactLists::getStates( ExactLists::NAME | ExactLists::UPPERCASE );
    }

    return in_array( $state, $states );
  }

  // endregion ///////////////////////////////////////////// End Address Validators

  // region //////////////////////////////////////////////// Private Helper Functions

  /**
   * Determines a file's Mime Type.
   *
   * @param   string $filePath The absolute path to the file, including the file name.
   *
   * @return  string                  The mime type, if detected.
   */
  private function getFileMimeType( $filePath )
  {
    $finfo = finfo_open( FILEINFO_MIME_TYPE );
    $mime  = finfo_file( $finfo, $filePath );
    finfo_close( $finfo );

    return $mime;
  }

  // endregion ///////////////////////////////////////////// End Private Helper Functions
}
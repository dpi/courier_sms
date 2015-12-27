<?php

/**
 * @file
 * Contains \Drupal\courier_sms\SmsMessageInterface.
 */

namespace Drupal\courier_sms;

use Drupal\courier\ChannelInterface;
use Drupal\sms\Message\SmsMessageInterface as CoreSmsMessageInterface;

interface SmsMessageInterface extends CoreSmsMessageInterface, ChannelInterface {

  public function getRecipient();
  // @todo these should be on \Drupal\sms\Message\SmsMessageInterface
  public function setRecipient($phone_number);
  public function setMessage($message);


}

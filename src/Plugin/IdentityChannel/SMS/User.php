<?php

/**
 * @file
 * Contains \Drupal\courier_sms\Plugin\IdentityChannel\SMS\User.
 */

namespace Drupal\courier_sms\Plugin\IdentityChannel\SMS;


use Drupal\courier\Plugin\IdentityChannel\IdentityChannelPluginInterface;
use Drupal\courier\ChannelInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\courier\Exception\IdentityException;

/**
 * Supports core user entities.
 *
 * @IdentityChannel(
 *   id = "identity:user:sms",
 *   label = @Translation("Drupal user to sms"),
 *   channel = "sms",
 *   identity = "user",
 *   weight = 10
 * )
 */
class User implements IdentityChannelPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function applyIdentity(ChannelInterface &$message, EntityInterface $identity) {
    /** @var \Drupal\user\UserInterface $identity */
    /** @var \Drupal\courier_sms\Entity\SmsMessage $message */

    if (empty($identity->sms_user['number'])) {
      throw new IdentityException('User does not have a phone number.');
    }
    if (empty($identity->sms_user['status']) || $identity->sms_user['status'] != SMS_USER_CONFIRMED) {
      throw new IdentityException('User has not confirmed phone number.');
    }

    $message->setRecipient($identity->sms_user['number']);
  }

}

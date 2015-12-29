<?php

/**
 * @file
 * Contains \Drupal\courier_sms\Entity\SmsMessage.
 */

namespace Drupal\courier_sms\Entity;

use Drupal\courier\ChannelBase;
use Drupal\courier_sms\SmsMessageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines storage for a SMS.
 *
 * @ContentEntityType(
 *   id = "sms",
 *   label = @Translation("SMS"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\courier_sms\Form\SmsMessage",
 *       "add" = "Drupal\courier_sms\Form\SmsMessage",
 *       "edit" = "Drupal\courier_sms\Form\SmsMessage",
 *       "delete" = "Drupal\courier_sms\Form\SmsMessage",
 *     },
 *   },
 *   base_table = "sms_message",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/sms/{sms}/edit",
 *     "edit-form" = "/sms/{sms}/edit",
 *     "delete-form" = "/sms/{sms}/delete",
 *   }
 * )
 */
class SmsMessage extends ChannelBase implements SmsMessageInterface {

  /**
   * {@inheritdoc}
   *
   * Returns singular recipient.
   */
  public function getRecipients() {
    return [$this->get('phone')->value];
  }

  /**
   * {@inheritdoc}
   */
  public function getRecipient() {
    return $this->get('phone')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRecipient($phone_number) {
    $this->set('phone', ['value' => $phone_number]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->get('message')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessage($message) {
    $this->set('message', ['value' => $message]);
  }

  /**
   * {@inheritdoc}
   *
   * No storage, only used in current request.
   */
  public function getSender() {
    // @todo. This method isnt actually used anywhere.
    return $this->sender;
  }

  /**
   * {@inheritdoc}
   *
   * No storage, only used in current request.
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * {@inheritdoc}
   *
   * No storage, only used in current request.
   */
  public function getOption($name) {
    if (array_key_exists($name, $this->options)) {
      return $this->options[$name];
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getUuid() {
    return $this->get('uuid')->value;
  }

  /**
   * {@inheritdoc}
   *
   * No storage, only used in current request.
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * {@inheritdoc}
   */
  public function applyTokens() {
    $tokens = $this->getTokenValues();
    $this->setMessage(\Drupal::token()->replace($this->getMessage(), $tokens));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  function isEmpty() {
    return empty($this->getMessage());
  }

  /**
   * {@inheritdoc}
   */
  static public function sendMessages(array $messages, $options = []) {
    /** @var \Drupal\sms\Provider\SmsProviderInterface $smsp */
    $sms_service = \Drupal::service('sms_provider');

    /** @var static[] $messages */
    foreach ($messages as $message) {
      $sms_service->send($message, $options);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('SMS ID'))
      ->setDescription(t('The SMS ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The SMS message UUID.'))
      ->setReadOnly(TRUE);

    $fields['phone'] = BaseFieldDefinition::create('telephone')
      ->setLabel(t('Phone'))
      ->setDescription(t('Phone number.'))
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'hidden',
      ]);

    $fields['message'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Message'))
      ->setDescription(t('The SMS message.'))
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 50,
        'settings' => array(
          'rows' => 2,
        ),
      ]);

    return $fields;
  }

}
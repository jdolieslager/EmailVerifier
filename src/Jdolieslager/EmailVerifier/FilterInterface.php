<?php
namespace Jdolieslager\EmailVerifier;

/**
 * @category    Jdolieslager
 * @package     EmailVerifier
 */
interface FilterInterface
{
    /**
     * Initialize the plugin
     *
     * @param Verifier $verifier
     * @param array    $options
     */
    public function __construct(Verifier $verifier, array $options);

    /**
     * Validate an emailaddress
     *
     * @param  string $email
     * @return boolean
     */
    public function validateSingle($email);

    /**
     * Validate multiple emailaddresses
     *
     * @param  array $emails array('email1', 'email2', 'email3')
     * @return array         array('<email>' => boolean)
     */
    public function validateMultiple(array $emails);
}

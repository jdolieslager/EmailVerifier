<?php
namespace Jdolieslager\EmailVerifier\Filter;

/**
 * @category    Jdolieslager
 * @package     EmailVerifier
 * @subpackage  Filter
 */
class Syntax extends \Jdolieslager\EmailVerifier\AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function validateMultiple(array $emails)
    {
        $return = array();
        foreach ($emails as $index => $email) {
            unset($emails[$index]);

            $return[$email] = $this->validate($email);
        }

        return $return;
    }

    /**
     * This will validate the emailaddress
     *
     * @param string $email
     * @return boolean
     */
    protected function validate($email)
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }
}

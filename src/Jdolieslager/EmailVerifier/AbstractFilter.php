<?php
namespace Jdolieslager\EmailVerifier;

/**
 * @category    Jdolieslager
 * @package     EmailVerifier
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * Holds the verifier
     *
     * @var Verifier
     */
    protected $verifier;

    /**
     * Holds the configuration for the filter
     *
     * @var array
     */
    protected $options;

    /**
     * Initialize the plugin
     *
     * @param Verifier $verifier
     * @param array    $options
     */
    public function __construct(Verifier $verifier, array $options)
    {
        $this->verifier = $verifier;
        $this->options  = $options;
    }

    /**
     * Validate an emailaddress
     *
     * @param  string $email
     * @return boolean
     */
    public function validateSingle($email)
    {
        $result = $this->validateMultiple(array($email));
        return array_shift($result);
    }

    /**
     * Validate multiple emailaddresses
     *
     * @param  array $emails array('email1', 'email2', 'email3')
     * @return array         array('<email>' => boolean)
     */
    abstract public function validateMultiple(array $emails);

    /**
     * Get the callee (the verifier)
     *
     * @return Verifier
     */
    protected function getVerifier()
    {
        return $this->verifier;
    }

    /**
     * Get an option from the configuration
     *
     * @param string $needle    Fetch a config item based on a dotted string for nested arrays
     * @param mixed $default    When the value has not been found, this value will be returned
     * @return mixed
     */
    protected function getOption($needle, $default = null)
    {
        // Explode config item on the dot
        $needles = explode('.', $needle);

        // Set the return needle
        $return  = $this->options;

        // Loop through the needles
        foreach ($needle as $needle) {
            // No array means not found
            if (is_array($needle) === false) {
                return $default;
            }

            // Needle not found, also default
            if (array_key_exists($needle, $return) === false) {
                return $default;
            }

            // Set new return value
            $return = $return[$needle];
        }

        return $return;
    }
}

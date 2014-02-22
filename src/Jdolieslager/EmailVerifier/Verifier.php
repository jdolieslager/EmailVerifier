<?php
namespace Jdolieslager\EmailVerifier;

/**
 * @category    Jdolieslager
 * @package     EmailVerifier
 */
class Verifier
{
    /**
     * Holds the configuration for the verifier and plugins
     *
     * @var array
     */
    protected $config = array();

    /**
     * Holds a list of filters that can be used by default
     *
     * @var array
     */
    protected $filters = array(
        'syntax' => 'Jdolieslager\\EmailVerifier\\Filter\\Syntax',
        'mx'     => 'Jdolieslager\\EmailVerifier\\Filter\\Mx',
        'smtp'   => 'Jdolieslager\\EmailVerifier\\Filter\\Smtp',
    );

    /**
     * Those filters cannot be unregistered
     *
     * @var array
     */
    protected $predefinedFilters = array('syntax', 'domain', 'smtp');

    /**
     * Holds a list of filter instances
     *
     * @var array
     */
    protected $instances = array();

    /**
     * Holds a list of exceptions that can be thrown by this class
     *
     * @var array
     */
    protected $exceptions = array(
        1 => 'Cannot register filter `%1$s`. Filter `%1$s` has already been registered.',
        2 => 'Cannot unregister filter `%1$s. Filter `%1$s` has not been registered.',
        3 => 'Cannot register filter `%s`. Class `%s` does not implments FilterInterface',
        4 => 'Cannot unregister filter `%s`. The filter is a system filter and cannot be unregistered',
        5 => 'Cannot call filter `%s`. The filter has not be registered in the system.',
    );

    /**
     * Get a filter
     *
     * @param  string $identifier
     * @return FilterInterface
     * @throws Exception\InvalidArgument When the filter does not exists
     */
    public function filter($identifier)
    {
        // Check if instance is available
        if (array_key_exists($identifier, $this->instances) === true) {
            return $this->instances[$identifier];
        }

        // Check if the filter exists
        if (array_key_exists($identifier, $this->filters) === false) {
            throw new Exception\InvalidArgument(sprintf($this->exceptions[5], $identifier), 5);
        }

        // The full classname of the filter
        $class   = $this->filters[$identifier];

        // Fetch configuration options
        $options = array_key_exists($identifier, $this->config) ? $this->config[$identifier] : array();

        // Create new instance
        return $this->instances[$identifier] = new $class($this, $options);
    }

    /**
     * Register a new filter in the system
     *
     * @param  string $identifier
     * @param  string $filterClass
     * @return Verifier
     * @throws Exception\InvalidArgument    When the filter has already been registered
     * @throws Exception\Implement          When the class does not implements required interface(s)
     */
    public function registerFilter($identifier, $filterClass)
    {
        // Check if the identifier has not been registered already
        if (array_key_exists($identifier, $this->filters)) {
            throw new Exception\InvalidArgument(sprintf($this->exceptions[1], $identifier), 1);
        }

        // Check if the filter meets the requirements
        if (is_subclass_of($filterClass, 'Jdolieslager\\EmailVerifier\\FilterInterface') === false) {
            throw new Exception\Implement(sprintf($this->exceptions[3], $identifier, $filterClass), 3);
        }

        $this->filters[$identifier] = $filterClass;

        return $this;
    }

    /**
     * Unregister a filter from the system
     *
     * @param  string $identifier
     * @return Verifier
     * @throws Exception\InvalidArgument    When the identifier has not been registered
     * @throws Exception\InvalidArgument    When the identifier has been predefined filter
     */
    public function unregisterFilter($identifier)
    {
        if (array_key_exists($identifier, $this->filters) === false) {
            throw new Exception\InvalidArgument(sprintf($this->exceptions[2], $identifier), 2);
        }

        if (in_array($identifier, $this->predefinedFilters) === true) {
            throw new Exception\InvalidArgument(sprintf($this->exceptions[4], $identifier), 4);
        }

        if (array_key_exists($identifier, $this->instances) === true) {
            unset($this->instances[$identifier]);
        }

        unset($this->filters[$identifier]);

        return $this;
    }
}

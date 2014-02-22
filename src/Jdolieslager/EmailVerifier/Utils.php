<?php
namespace Jdolieslager\EmailVerifier;

/**
 * Utilities that will be shared across the filters
 *
 * @category    Jdolieslager
 * @package     EmailVerifier
 */
class Utils
{
    protected $exceptions = array(
        1 => 'Could not get any MX record for domain `%s`',
        2 => 'There is no SMTP server running at %s:25',
    );

    /**
     * Convert single dimensional array to multi dimensional array
     *
     * @param  array $emails arra('email1', 'email2', ....)
     * @return array array('domainA' => array('email2', 'email3'), 'domainB' => array('email1'))
     */
    public function groupByDomain(array $emails)
    {
        // Domain list
        $domains = array();

        foreach ($emails as $email) {
            // grep domain part
            $domain = substr($email, strpos($email, '@') + 1);
            if ($domain === false) {
                $domain = '';
            }

            // Check if domain already exists
            if (array_key_exists($domain, $domains) === false) {
                $domains[$domain] = array();
            }

            // Add to the domain list
            $domains[$domain][] = $email;
        }

        return $domains;
    }

    /**
     * Return mxrecords for a given domainname
     *
     * @param string $domain
     * @return array
     * @throws Exception\InvalidArgument When domainname does not exists
     */
    public function getMXRecords($domain)
    {
        static $mxRecords = array();

        if (array_key_exists($domain, $mxRecords) === true) {
            echo 'From runtime!';
            return $mxRecords[$domain];
        }

        $hosts    = array();
        $weights  = array();
        $mxRecord = getmxrr($domain, $hosts, $weights);

        if ($mxRecord === false) {
            $record = dns_get_record($domain);

            if (empty($record) === true) {
                throw new Exception\InvalidArgument(sprintf($this->exceptions[1], $domain), 1);
            }

            $socket = @fsockopen($domain, 25, $code, $message, 0.1);
            if ($socket === false) {
                throw new Exception\InvalidArgument(sprintf($this->exceptions[2], $domain), 2);
            }

            fclose($socket);

            return $mxRecords[$domain] = array($domain);
        }

        // Sort on weight. We use the less weight first
        asort($weights);

        $records = array();
        foreach ($weights as $index => $weight) {
            $records[] = $hosts[$index];
        }

        return $mxRecords[$domain] = $records;
    }

    /**
     * Checks if the domainname is correctly written
     *
     * @param  string $domain
     * @return boolean
     */
    public function isValidDomainSyntax($domain)
    {
        return (
            preg_match(
                '/\b((?=[a-z0-9-]{1,63}\.)[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}\b/ix',
                $domain
            ) === 1
        );
    }
}

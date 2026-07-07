<?php
declare(strict_types=1);
namespace App\Service;


class MailCheckerService
{
    private array $cached_domain_list = [];

    private MailCheckerErrorStorage $mailCheckerErrorStorage;
    private MailCheckerCacheStorage $mailCheckerCacheStorage;

    public static function makeInstance(): MailCheckerService
    {
        return new MailCheckerService(new MailCheckerErrorStorage(), MailCheckerCacheStorage::makeInstance());
    }

    public function __construct(MailCheckerErrorStorage $mailCheckerErrorStorage, MailCheckerCacheStorage $mailCheckerCacheStorage)
    {
        $this->mailCheckerErrorStorage = $mailCheckerErrorStorage;
        $this->mailCheckerCacheStorage = $mailCheckerCacheStorage;
    }

    public function validateEmailList(array $email_list, bool $write_in_cache = true, bool $use_cache = false)
    {
        $this->checkMailListIsValid($email_list);

        $domain_list = [];

        foreach ($email_list as $email) {
            $current_domain = substr($email, strpos($email, "@") + 1);
            if (!in_array($current_domain, $domain_list)) {
                $domain_list[] = $current_domain;
            }
        }
        

        foreach ($domain_list as $domain) {

            $is_taken_from_cache = false;
            $current_mx_record = [];

            if ($use_cache && $this->mailCheckerCacheStorage->has($domain)) {
                $is_taken_from_cache = true;
            }
            else {
                $current_mx_record = dns_get_record($domain, DNS_MX);
               
                if (!is_array($current_mx_record) || count($current_mx_record) === 0) {
                    $this->getErrorStorage()->pushUndefinedMXDomainInList($domain);
                }
            }

            if ($write_in_cache && !$is_taken_from_cache) {
                $ttl = array_reduce($current_mx_record, fn(int $value, array $mx_domain) => $value+= array_key_exists('ttl', $mx_domain) ? (int)$mx_domain['ttl'] : 0, 0);
                $ttl = ($ttl !== 0) ? (int)($ttl / count($current_mx_record)) : 1000;
                $this->mailCheckerCacheStorage->add($domain, $ttl);
            }
               
        }

    }

    public function getCache(): array
    {   
        return $this->mailCheckerCacheStorage->getAll();
    }

    public function clearCache(): static
    {
        $this->mailCheckerCacheStorage->clearAll();
        return $this;
    }

    public function getErrorStorage(): MailCheckerErrorStorage
    {
        return $this->mailCheckerErrorStorage;
    }

    public function getErrors(): array
    {
        return $this->getErrorStorage()->getAllFormattedErrorList();
    }

    public function getEmailErrors(): array
    {
        return $this->getErrorStorage()->getFormattedInvalidEmailErrorList();
    }

    public function getDomainErrors(): array
    {
        return $this->getErrorStorage()->getFormattedUndefinedMXDomainErrorList();
    }

    private function checkMailListIsValid(array $email_list): void
    {

        foreach ($email_list as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->getErrorStorage()->pushInvalidEmailInList($email);
            }
        }
    }

    private function appendInCache(array $verified_mx_domain_list): void
    {
        $diff = array_diff($verified_mx_domain_list, $this->cached_domain_list);
        $this->cached_domain_list = array_merge($this->cached_domain_list, $diff);

    }

    private function writeInCache(array $verified_mx_domain_list): void
    {
        $this->cached_domain_list = $verified_mx_domain_list;
    }


}
<?php
declare(strict_types=1);
namespace App\Service;

use App\Exception\Service\IncorrectEmailException;
use App\Exception\Service\UndefinedMXRecordException;

class MailCheckerService
{
    private array $cached_domain_list = [];
    private array $validation_errors = [];

    public static function makeInstance(): MailCheckerService
    {
        return new MailCheckerService();
    }

    public function validateEmailList(array $email_list, string $write_in_cache_mode = '', bool $use_cache = false)
    {
        $this->checkMailListIsValid($email_list);

        $domain_list = [];

        foreach ($email_list as $email) {
            $current_domain = substr($email, strpos($email, "@") + 1);
            if (!in_array($current_domain, $domain_list)) {
                $domain_list[] = $current_domain;
            }
        }

        $undefined_mx_host_list = [];
        $verified_mx_domain_list = [];

        foreach ($domain_list as $domain) {
            if ($use_cache && in_array($domain, $this->cached_domain_list)) {
                $verified_mx_domain_list[] = $domain;
            }
            else {
                $current_mx_record = dns_get_record($domain, DNS_MX);
                if (count($current_mx_record) === 0) {
                    $undefined_mx_host_list[] = $domain;
                }
                else {
                    $verified_mx_domain_list[] = $domain;
                }

            }    
        }

        if (count($undefined_mx_host_list) > 0) {
            $undefined_mx_host_list_str = implode("|", $undefined_mx_host_list);
            throw new UndefinedMXRecordException("Следующие домены не имеют MX записи: $undefined_mx_host_list_str");
        }

        if ($write_in_cache_mode !== '') {
            if ('a' === $write_in_cache_mode) {
                $this->appendInCache($verified_mx_domain_list);
            }
            else if ('w' === $write_in_cache_mode) {
                $this->writeInCache($verified_mx_domain_list);
            }
        }

        

    }

    public function getCache(): array
    {
        return $this->cached_domain_list;
    }

    public function clearCache(): static
    {
        $this->cached_domain_list = [];
        return $this;
    }

    private function checkMailListIsValid(array $email_list): void
    {
        $invalide_email_list = [];

        foreach ($email_list as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalide_email_list[] = $email;
            }
        }

        if (count($invalide_email_list) > 0) {
            $invalide_email_list_str = implode(" | ", $invalide_email_list);
            throw new IncorrectEmailException("Следующие email адреса имеют некорректный формат: $invalide_email_list_str");
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
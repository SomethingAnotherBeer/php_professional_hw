<?php
declare(strict_types=1);
namespace App\Service;

class MailCheckerErrorStorage
{
    private array $invalid_email_error_list = [];
    private array $undefined_mx_domain_error_list = [];


    public function pushInvalidEmailInList(string $email): static
    {
        if (!in_array($email, $this->invalid_email_error_list)) {
            $this->invalid_email_error_list[] = $email;
        }
        return $this;
    }

    public function pushUndefinedMXDomainInList(string $domain): static
    {
        if (!in_array($domain, $this->undefined_mx_domain_error_list)) {
            $this->undefined_mx_domain_error_list[] = $domain;
        }
        return $this;
    }


    public function getInvalidEmailErrorList(): array
    {
        return $this->invalid_email_error_list;
    }

    public function getUndefinedMXDomainErrorList(): array
    {
        return $this->undefined_mx_domain_error_list;
    }

    public function getFormattedInvalidEmailErrorList(): array
    {
        return array_map(fn(string $email) => "email адрес {$email} не является корректным", $this->invalid_email_error_list);
    }

    public function getFormattedUndefinedMXDomainErrorList(): array
    {
        return array_map(fn(string $domain) => "Домен $domain не имеет MX записи", $this->undefined_mx_domain_error_list);
    }

    public function getAllFormattedErrorList(): array
    {
        return array_merge($this->getFormattedInvalidEmailErrorList(), $this->getFormattedUndefinedMXDomainErrorList());
    }

    public function getAllFormattedErrorListAsString(string $delimiter = "\n"): string
    {
        return implode($delimiter, $this->getAllFormattedErrorList());
    }
}
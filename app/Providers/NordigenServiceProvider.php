<?php

namespace App\Providers;

use Nordigen\NordigenPHP\API\NordigenClient;

class NordigenServiceProvider
{
    private NordigenClient $client;
    private string $secretId;
    private string $secretKey;

    public function __construct(string $secretId, string $secretKey)
    {
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;
        $this->client = new NordigenClient($secretId, $secretKey);
        $this->client->createAccessToken();
    }

    public function getListOfInstitutions(string $country): array
    {
        return $this->client->institution->getInstitutionsByCountry($country);
    }

    public function getSessionData(string $redirectUri, string $institutionId, int $maxHistoricalDays = 90): array
    {
        return $this->client->initSession($institutionId, $redirectUri, $maxHistoricalDays);
    }

    public function getListOfAccounts(string $requisitionId): array
    {
        return $this->client->requisition->getRequisition($requisitionId)["accounts"];
    }

    public function getTransactions(string $accountId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $account = $this->client->account($accountId);
        return $account->getAccountTransactions($dateFrom, $dateTo);
    }
}

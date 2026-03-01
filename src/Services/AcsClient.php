<?php
namespace NextPointer\Acs\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use NextPointer\Acs\Exceptions\AcsException;

class AcsClient
{
    protected function call(string $alias, array $params = []): array
    {
        $payload = [
            'ACSAlias' => $alias,
            'ACSInputParameters' => array_merge([
                'Company_ID'       => config('acs.company_id'),
                'Company_Password' => config('acs.company_password'),
                'User_ID'          => config('acs.user_id'),
                'User_Password'    => config('acs.user_password'),
                'Language'         => config('acs.language'),
            ], $params),
        ];

        $response = $this->send($payload);

        return $this->handleResponse($response);
    }

    protected function send(array $payload): Response
    {
        return Http::withHeaders([
            'ACSApiKey' => config('acs.api_key'),
            'Content-Type' => 'application/json',
        ])
            ->timeout((int) config('acs.timeout', 30))
            ->retry((int) config('acs.retry_attempts', 3), 200)
            ->post(config('acs.base_url'), $payload);
    }

    protected function handleResponse(Response $response): array
    {
        if ($response->failed()) {
            throw new AcsException(
                'ACS HTTP Error: '.$response->body()
            );
        }

        $json = $response->json();

        if (!$json) {
            throw new AcsException('Invalid JSON response from ACS');
        }

        if (($json['ACSExecution_HasError'] ?? false) === true) {
            throw new AcsException(
                $json['ACSExecutionErrorMessage'] ?? 'Unknown ACS error'
            );
        }

        $error = data_get(
            $json,
            'ACSOutputResponse.ACSValueOutput.0.Error_Message'
        );

        if (!empty($error)) {
            throw new AcsException($error);
        }

        return $json['ACSOutputResponse'] ?? $json;
    }

    public function createVoucher(array $data): array
    {
        return $this->call('ACS_Create_Voucher', array_merge([
            'Billing_Code' => config('acs.billing_code'),
            'Charge_Type'  => 2,
        ], $data));
    }

    public function deleteVoucher(string $voucherNo): array
    {
        return $this->call('ACS_Delete_Voucher', [
            'Language' => null,
            'Voucher_No' => $voucherNo,
        ]);
    }

    public function issuePickupList(string $date): array
    {
        return $this->call('ACS_Issue_Pickup_List', [
            'Pickup_Date' => $date,
            'MyData' => null,
            'Vouchers_To_Include' => null,
            'Vouchers_To_Exclude' => null,
        ]);
    }

    public function printVouchers(array $voucherNumbers): array
    {
        return $this->call('ACS_Print_Voucher', [
            'Voucher_No' => implode(',', $voucherNumbers),
            'Print_Type' => 2,
            'Start_Position' => 1,
        ]);
    }
}
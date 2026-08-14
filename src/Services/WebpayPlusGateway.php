<?php

namespace JohnRivera7\FilamentTransbankWebpay\Services;

use JohnRivera7\FilamentTransbankWebpay\Support\TransbankCredentials;
use RuntimeException;
use Throwable;
use Transbank\Webpay\WebpayPlus\Transaction;

final class WebpayPlusGateway
{
    public function __construct(
        private readonly TransbankCredentials $credentials,
    ) {}

    public static function make(TransbankCredentials $credentials): self
    {
        return new self($credentials);
    }

    /**
     * @return array{
     *   redirect_url: string,
     *   redirect_method: string,
     *   redirect_fields: array{token_ws: string},
     *   token: string,
     *   external_id: string
     * }
     */
    public function create(
        string $buyOrder,
        string $sessionId,
        int $amountClp,
        string $returnUrl,
    ): array {
        $this->assertReady();

        if ($amountClp < 1) {
            throw new RuntimeException('Transbank: el monto debe ser al menos 1 CLP.');
        }

        try {
            $response = $this->transaction()->create(
                $this->sanitizeBuyOrder($buyOrder),
                $sessionId !== '' ? $sessionId : $buyOrder,
                $amountClp,
                $returnUrl,
            );
        } catch (Throwable $e) {
            throw new RuntimeException('Transbank: no se pudo crear la transacción. '.$e->getMessage(), previous: $e);
        }

        $token = (string) $response->getToken();
        $url = (string) $response->getUrl();

        if ($token === '' || $url === '') {
            throw new RuntimeException('Transbank: respuesta incompleta al crear la transacción.');
        }

        // Webpay Plus requiere POST con token_ws (no GET/querystring).
        return [
            'redirect_url' => $url,
            'redirect_method' => 'POST',
            'redirect_fields' => ['token_ws' => $token],
            'token' => $token,
            'external_id' => $token,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{paid: bool, external_id: string, amount: float|null, raw: array<string, mixed>}
     */
    public function commit(array $payload): array
    {
        $this->assertReady();

        $token = trim((string) ($payload['token_ws'] ?? $payload['token'] ?? ''));

        if ($token === '') {
            throw new RuntimeException('Transbank: falta token_ws para confirmar el pago.');
        }

        try {
            $response = $this->transaction()->commit($token);
        } catch (Throwable $e) {
            throw new RuntimeException('Transbank: no se pudo confirmar la transacción. '.$e->getMessage(), previous: $e);
        }

        $raw = [
            'token' => $token,
            'status' => $response->getStatus(),
            'response_code' => $response->getResponseCode(),
            'amount' => $response->getAmount(),
            'buy_order' => $response->getBuyOrder(),
            'authorization_code' => $response->getAuthorizationCode(),
            'payment_type_code' => $response->getPaymentTypeCode(),
            'card_number' => $response->getCardNumber(),
            'accounting_date' => $response->getAccountingDate(),
            'transaction_date' => $response->getTransactionDate(),
            'session_id' => $response->getSessionId(),
            'installments_number' => $response->getInstallmentsNumber(),
        ];

        return [
            'paid' => $response->isApproved(),
            'external_id' => $token,
            'amount' => $response->getAmount() !== null ? (float) $response->getAmount() : null,
            'raw' => $raw,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function status(string $token): array
    {
        $this->assertReady();

        try {
            $response = $this->transaction()->status($token);
        } catch (Throwable $e) {
            throw new RuntimeException('Transbank: no se pudo consultar el estado. '.$e->getMessage(), previous: $e);
        }

        return [
            'token' => $token,
            'status' => $response->getStatus(),
            'approved' => $response->isApproved(),
            'response_code' => $response->getResponseCode(),
            'amount' => $response->getAmount(),
            'buy_order' => $response->getBuyOrder(),
            'payment_type_code' => $response->getPaymentTypeCode(),
            'installments_number' => $response->getInstallmentsNumber(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function refund(string $token, int $amountClp): array
    {
        $this->assertReady();

        try {
            $response = $this->transaction()->refund($token, $amountClp);
        } catch (Throwable $e) {
            throw new RuntimeException('Transbank: no se pudo anular. '.$e->getMessage(), previous: $e);
        }

        return [
            'token' => $token,
            'type' => $response->getType(),
            'response_code' => $response->getResponseCode(),
            'authorization_code' => method_exists($response, 'getAuthorizationCode') ? $response->getAuthorizationCode() : null,
            'nullified_amount' => method_exists($response, 'getNullifiedAmount') ? $response->getNullifiedAmount() : null,
        ];
    }

    /**
     * Detecta abort/timeout de Webpay (TBK_* sin token_ws).
     *
     * @param  array<string, mixed>  $payload
     */
    public static function isAbortReturn(array $payload): bool
    {
        $tokenWs = trim((string) ($payload['token_ws'] ?? ''));
        $hasTbk = self::filledString($payload['TBK_TOKEN'] ?? null)
            || self::filledString($payload['TBK_ORDEN_COMPRA'] ?? null)
            || self::filledString($payload['TBK_ID_SESION'] ?? null);

        return $tokenWs === '' && $hasTbk;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return 'aborted'|'timeout'|null
     */
    public static function abortReason(array $payload): ?string
    {
        if (! self::isAbortReturn($payload)) {
            return null;
        }

        return self::filledString($payload['TBK_TOKEN'] ?? null) ? 'aborted' : 'timeout';
    }

    protected static function filledString(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_bool($value) || is_numeric($value)) {
            return true;
        }

        return ! empty($value);
    }

    protected function transaction(): Transaction
    {
        if ($this->credentials->isProduction()) {
            return Transaction::buildForProduction(
                $this->credentials->apiKey,
                $this->credentials->commerceCode,
            );
        }

        return Transaction::buildForIntegration(
            $this->credentials->apiKey,
            $this->credentials->commerceCode,
        );
    }

    protected function assertReady(): void
    {
        if (! $this->credentials->enabled) {
            throw new RuntimeException('Transbank Webpay no está habilitado.');
        }

        if (! $this->credentials->isComplete()) {
            throw new RuntimeException('Transbank: faltan commerce_code o api_key.');
        }
    }

    protected function sanitizeBuyOrder(string $orderId): string
    {
        $order = preg_replace('/[^A-Za-z0-9]/', '', $orderId) ?: 'ORDER';

        return substr($order, 0, 26);
    }
}

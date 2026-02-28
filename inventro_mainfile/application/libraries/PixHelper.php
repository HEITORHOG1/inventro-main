<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PixHelper Library
 *
 * Generates PIX EMV/BRCode payloads following the Brazilian Central Bank
 * specification (BACEN). The payload string can be used to generate a QR Code
 * via the Ciqrcode library.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Inventro
 * @version     1.0
 *
 * @see https://www.bcb.gov.br/content/estabilidadefinanceira/pix/Regulamento_Pix/II_ManualdePadroesparaIniciacaodoPix.pdf
 */
class PixHelper {

    /**
     * Payload Format Indicator
     */
    const ID_PAYLOAD_FORMAT_INDICATOR = '00';

    /**
     * Merchant Account Information
     */
    const ID_MERCHANT_ACCOUNT_INFORMATION = '26';

    /**
     * Merchant Account Information - GUI
     */
    const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';

    /**
     * Merchant Account Information - Chave PIX
     */
    const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';

    /**
     * Merchant Category Code
     */
    const ID_MERCHANT_CATEGORY_CODE = '52';

    /**
     * Transaction Currency
     */
    const ID_TRANSACTION_CURRENCY = '53';

    /**
     * Transaction Amount
     */
    const ID_TRANSACTION_AMOUNT = '54';

    /**
     * Country Code
     */
    const ID_COUNTRY_CODE = '58';

    /**
     * Merchant Name
     */
    const ID_MERCHANT_NAME = '59';

    /**
     * Merchant City
     */
    const ID_MERCHANT_CITY = '60';

    /**
     * Additional Data Field Template
     */
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';

    /**
     * Additional Data Field - Reference Label (txid)
     */
    const ID_ADDITIONAL_DATA_FIELD_REFERENCE_LABEL = '05';

    /**
     * CRC16
     */
    const ID_CRC16 = '63';

    /**
     * GUI for PIX (Brazilian Central Bank)
     */
    const PIX_GUI = 'BR.GOV.BCB.PIX';

    /**
     * Map of accented characters to their ASCII equivalents
     *
     * @var array
     */
    private $_accent_map = array(
        'A' => '/[ÀÁÂÃÄÅ]/',
        'a' => '/[àáâãäå]/',
        'E' => '/[ÈÉÊË]/',
        'e' => '/[èéêë]/',
        'I' => '/[ÌÍÎÏ]/',
        'i' => '/[ìíîï]/',
        'O' => '/[ÒÓÔÕÖØ]/',
        'o' => '/[òóôõöø]/',
        'U' => '/[ÙÚÛÜ]/',
        'u' => '/[ùúûü]/',
        'C' => '/[Ç]/',
        'c' => '/[ç]/',
        'N' => '/[Ñ]/',
        'n' => '/[ñ]/',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        log_message('debug', 'PixHelper Library initialized.');
    }

    /**
     * Generate the PIX BRCode payload string
     *
     * Assembles the EMV/TLV-encoded payload following the BACEN specification
     * for PIX QR Codes.
     *
     * @param  string $chave              PIX key (CPF, CNPJ, email, phone, or random key)
     * @param  string $nome_beneficiario  Merchant/store name (max 25 chars, accents removed)
     * @param  string $cidade             City name (max 15 chars, accents removed)
     * @param  float  $valor              Amount in BRL
     * @param  string $txid               Transaction ID (max 25 chars, default '***')
     * @return string                     PIX payload (BRCode format) ready for QR Code generation
     */
    public function gerarPayload($chave, $nome_beneficiario, $cidade, $valor, $txid = '***')
    {
        // Sanitize inputs
        $nome_beneficiario = $this->_sanitizeString($nome_beneficiario, 25);
        $cidade            = $this->_sanitizeString($cidade, 15);
        $txid              = mb_substr(trim($txid), 0, 25);
        $valor             = number_format((float) $valor, 2, '.', '');

        // Build Merchant Account Information (ID 26) sub-fields
        $merchantAccountInfo  = $this->_formatTLV(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, self::PIX_GUI);
        $merchantAccountInfo .= $this->_formatTLV(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $chave);

        // Build Additional Data Field Template (ID 62) sub-fields
        $additionalDataField = $this->_formatTLV(self::ID_ADDITIONAL_DATA_FIELD_REFERENCE_LABEL, $txid);

        // Assemble the full payload (without CRC)
        $payload  = $this->_formatTLV(self::ID_PAYLOAD_FORMAT_INDICATOR, '01');
        $payload .= $this->_formatTLV(self::ID_MERCHANT_ACCOUNT_INFORMATION, $merchantAccountInfo);
        $payload .= $this->_formatTLV(self::ID_MERCHANT_CATEGORY_CODE, '0000');
        $payload .= $this->_formatTLV(self::ID_TRANSACTION_CURRENCY, '986');

        // Only include amount if greater than zero
        if ((float) $valor > 0) {
            $payload .= $this->_formatTLV(self::ID_TRANSACTION_AMOUNT, $valor);
        }

        $payload .= $this->_formatTLV(self::ID_COUNTRY_CODE, 'BR');
        $payload .= $this->_formatTLV(self::ID_MERCHANT_NAME, $nome_beneficiario);
        $payload .= $this->_formatTLV(self::ID_MERCHANT_CITY, $cidade);
        $payload .= $this->_formatTLV(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $additionalDataField);

        // Append CRC16 placeholder (ID 63, length 04) then compute the actual CRC
        $payload .= self::ID_CRC16 . '04';
        $crc = $this->_crc16($payload);
        $payload .= $crc;

        return $payload;
    }

    /**
     * Generate PIX payload and corresponding QR Code image
     *
     * Uses the Ciqrcode library to generate a PNG QR Code saved to the
     * application cache directory.
     *
     * @param  string $chave              PIX key
     * @param  string $nome_beneficiario  Merchant/store name (max 25 chars)
     * @param  string $cidade             City name (max 15 chars)
     * @param  float  $valor              Amount in BRL
     * @param  string $txid               Transaction ID (max 25 chars, default '***')
     * @return array                      ['payload' => string, 'qrcode_path' => string]
     */
    public function gerarPayloadComQrCode($chave, $nome_beneficiario, $cidade, $valor, $txid = '***')
    {
        $payload = $this->gerarPayload($chave, $nome_beneficiario, $cidade, $valor, $txid);

        $CI =& get_instance();
        $CI->load->library('ciqrcode');

        // Sanitize txid for filename (remove non-alphanumeric chars except dash/underscore)
        $safeTxid = preg_replace('/[^a-zA-Z0-9_\-]/', '', $txid);
        if (empty($safeTxid)) {
            $safeTxid = 'default';
        }

        $filename = 'pix_' . $safeTxid . '.png';
        $filepath = APPPATH . 'cache/' . $filename;

        $params = array(
            'data'     => $payload,
            'level'    => 'M',
            'size'     => 5,
            'savename' => $filepath,
        );

        $CI->ciqrcode->generate($params);

        return array(
            'payload'     => $payload,
            'qrcode_path' => $filepath,
        );
    }

    /**
     * Format a TLV (Type-Length-Value) field
     *
     * Each field in the EMV/BRCode format is encoded as:
     *   ID (2 chars) + Length (2 chars, zero-padded) + Value
     *
     * @param  string $id    Field ID (2 characters)
     * @param  string $value Field value
     * @return string        TLV-encoded string
     */
    public function _formatTLV($id, $value)
    {
        $length = strlen($value);
        return $id . str_pad($length, 2, '0', STR_PAD_LEFT) . $value;
    }

    /**
     * Calculate CRC16-CCITT-FALSE checksum
     *
     * Uses polynomial 0x1021 with initial value 0xFFFF as required by the
     * PIX/EMV specification.
     *
     * @param  string $payload The payload string to calculate CRC for
     * @return string          4-character uppercase hexadecimal CRC
     */
    public function _crc16($payload)
    {
        $polynomial = 0x1021;
        $crc = 0xFFFF;

        $length = strlen($payload);
        for ($i = 0; $i < $length; $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Sanitize a string for use in the PIX payload
     *
     * Removes accented characters, converts to uppercase, removes any
     * characters that are not alphanumeric or spaces, and truncates to
     * the specified maximum length.
     *
     * @param  string $str       Input string
     * @param  int    $maxLength Maximum allowed length
     * @return string            Sanitized string
     */
    private function _sanitizeString($str, $maxLength)
    {
        $str = trim($str);

        // Remove accents
        foreach ($this->_accent_map as $replacement => $pattern) {
            $str = preg_replace($pattern, $replacement, $str);
        }

        // Convert to uppercase
        $str = strtoupper($str);

        // Remove characters that are not alphanumeric or spaces
        $str = preg_replace('/[^A-Z0-9 ]/', '', $str);

        // Collapse multiple spaces into one
        $str = preg_replace('/\s+/', ' ', $str);

        // Truncate to max length
        $str = mb_substr($str, 0, $maxLength);

        return trim($str);
    }
}

/* end of file PixHelper.php */

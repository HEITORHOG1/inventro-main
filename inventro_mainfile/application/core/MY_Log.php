<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Log — Extensão do CI_Log para Docker
 *
 * Duplica mensagens de log para stderr, permitindo que o Docker
 * capture via json-file driver e o Promtail/Loki agregue.
 *
 * Mantém o comportamento original (gravar em arquivo) e adiciona
 * output para stderr que aparece em `docker logs inventro_app`.
 */
class MY_Log extends CI_Log {

    /**
     * Flag para controlar se deve enviar ao stderr
     */
    protected $_stderr_enabled = false;

    /**
     * Resource do stderr
     */
    protected $_stderr;

    public function __construct()
    {
        parent::__construct();

        // Ativar stderr apenas se rodando dentro do Docker
        // (detecta pela existência do /.dockerenv ou variável APP_ENV)
        if (file_exists('/.dockerenv') || getenv('APP_ENV') !== false) {
            $this->_stderr = @fopen('php://stderr', 'w');
            $this->_stderr_enabled = ($this->_stderr !== false);
        }
    }

    /**
     * Write Log File
     *
     * Chama o método pai (grava em arquivo) e também envia ao stderr.
     */
    public function write_log($level, $msg)
    {
        // Sempre chamar o pai para manter o log em arquivo
        $result = parent::write_log($level, $msg);

        // Duplicar para stderr (Docker logs)
        if ($this->_stderr_enabled && $this->_stderr) {
            $level = strtoupper($level);

            // Filtrar: só enviar ERROR e WARNING ao stderr em produção
            // Em dev, enviar tudo
            if (ENVIRONMENT !== 'development' && !in_array($level, ['ERROR', 'WARNING'])) {
                return $result;
            }

            $date = date($this->_date_fmt);
            $stderr_msg = "{$level} - {$date} --> {$msg}" . PHP_EOL;

            @fwrite($this->_stderr, $stderr_msg);
        }

        return $result;
    }

    public function __destruct()
    {
        if ($this->_stderr && is_resource($this->_stderr)) {
            @fclose($this->_stderr);
        }
    }
}

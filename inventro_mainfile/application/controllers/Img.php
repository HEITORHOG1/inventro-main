<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Image Server Controller
 *
 * Serves product images stored in application/modules/item/assets/images/.
 * Necessary because .htaccess blocks direct access to application/ (security).
 *
 * Usage: /img/product/{date}/{filename.ext}
 * Example: /img/product/2026-03-08/abc123def.jpg
 */
class Img extends CI_Controller {

    private $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'webp'];

    private $mime_types = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'ico'  => 'image/x-icon',
        'webp' => 'image/webp',
    ];

    /**
     * Serve a product image.
     *
     * @param string $date Date subfolder (YYYY-MM-DD)
     * @param string $filename Image filename
     */
    public function product($date = '', $filename = '') {
        if (empty($date) || empty($filename)) {
            show_404();
            return;
        }

        // Sanitize: only allow date-like folder names and safe filenames
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            show_404();
            return;
        }

        // Prevent directory traversal
        $filename = basename($filename);
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
            show_404();
            return;
        }

        // Check extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowed_extensions)) {
            show_404();
            return;
        }

        $file_path = APPPATH . 'modules/item/assets/images/' . $date . '/' . $filename;

        if (!is_file($file_path) || !is_readable($file_path)) {
            show_404();
            return;
        }

        // Verify MIME type matches extension
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected_mime = finfo_file($finfo, $file_path);
            finfo_close($finfo);
            if (strpos($detected_mime, 'image/') !== 0) {
                show_404();
                return;
            }
        }

        $mime = isset($this->mime_types[$ext]) ? $this->mime_types[$ext] : 'application/octet-stream';
        $filesize = filesize($file_path);

        // Cache headers (images don't change once uploaded)
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . $filesize);
        header('Cache-Control: public, max-age=2592000'); // 30 days
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file_path)) . ' GMT');

        // Handle 304 Not Modified
        $etag = '"' . md5_file($file_path) . '"';
        header('ETag: ' . $etag);
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }

        readfile($file_path);
        exit;
    }

    /**
     * Serve the default product placeholder image.
     * URL: /img/product/default
     */
    public function product_default() {
        $file_path = APPPATH . 'modules/item/assets/images/product.jpg';

        if (!is_file($file_path)) {
            show_404();
            return;
        }

        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: public, max-age=2592000');
        readfile($file_path);
        exit;
    }
}

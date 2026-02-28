<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fileupload
{
    /**
     * Upload a file to the specified path.
     *
     * @param string $upload_path Path where the file should be uploaded.
     * @param string $field_name Name of the input field.
     * @return string|null Path to the uploaded file or null on failure.
     */
    public function do_upload($upload_path = null, $field_name = null) {
        if (empty($_FILES[$field_name]['name'])) {
            return null; // No file selected for upload
        }

        $ci =& get_instance();
        $ci->load->helper('url');

        // Create folder with today's date
        $file_path = $upload_path . date('Y-m-d') . "/";
        if (!is_dir($file_path)) {
            if (!mkdir($file_path, 0755, true)) {
                return null; // Failed to create directory
            }
        }

        // Upload configuration
        $config = [
            'upload_path'      => $file_path,
            'allowed_types'    => 'gif|jpg|png|jpeg|ico',
            'max_filename'     => 255,
            'overwrite'        => false,
            'maintain_ratio'   => true,
            'encrypt_name'     => true, // Encrypt filename for security
            'remove_spaces'    => true,
            'file_ext_tolower' => true,
        ];
        
        $ci->load->library('upload', $config);

        // Perform the upload
        if (!$ci->upload->do_upload($field_name)) {
            log_message('error', $ci->upload->display_errors()); // Log the upload error
            return false; // Upload failed
        }

        // Get uploaded file data
        $file = $ci->upload->data();
        return $file_path . $file['file_name']; // Return the file path
    }

    /**
     * Resize an uploaded image.
     *
     * @param string $file_path Path to the image file.
     * @param int $width New width.
     * @param int $height New height.
     * @return bool True on success, false on failure.
     */
    public function do_resize($file_path = null, $width = null, $height = null) {
        if (empty($file_path) || empty($width) || empty($height)) {
            return false; // Invalid parameters
        }

        $ci =& get_instance();
        $ci->load->library('image_lib'); // Load CodeIgniter's image library

        // Resize configuration
        $config = [
            'image_library'  => 'gd2',
            'source_image'   => $file_path,
            'create_thumb'   => false,
            'maintain_ratio' => false, // Set to true if you want to maintain aspect ratio
            'width'          => $width,
            'height'         => $height,
        ];

        $ci->image_lib->initialize($config);

        // Perform resizing
        if (!$ci->image_lib->resize()) {
            log_message('error', $ci->image_lib->display_errors()); // Log resize errors
            return false;
        }

        $ci->image_lib->clear(); // Clear settings after use
        return true;
    }
}

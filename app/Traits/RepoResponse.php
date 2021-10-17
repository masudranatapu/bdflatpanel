<?php namespace App\Traits;

trait RepoResponse {

    public function formatResponse(bool $status, string $msg, string $redirect_to, $data = null, string $flash_type = '') : object
    {

        if ($flash_type == '') {
            $flash_type = $status ? 'flashMessageSuccess' : 'flashMessageError'; // flashMessageWarning
        }

        return (object) array(
            'status'         => $status,
            'msg'            => $msg,
            'description'    => $msg,
            'data'           => $data,
            'PK_NO'          => ! is_array($data) && $data != '' ? $data : 0,
            'redirect_to'    => $redirect_to,
            'redirect_class' => $flash_type
        );
    }
}

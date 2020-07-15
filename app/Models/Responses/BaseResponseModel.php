<?php


namespace App\Models\Responses;


class BaseResponseModel
{
    // @property string(success/error)
    public $status = "success";
    // @property mixed(***ResponseModel)
    public $data;
    // @property string, ^optional
    public $message;

    /**
     * BaseResponseModel constructor.
     * @param string $status
     * @param $data
     */
    public function __construct(string $status, $data, string $message = null)
    {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
    }


}
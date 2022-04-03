<?php
/**
 * @author Raj Luo
 */

namespace App\Http\Controllers\Common;

use Illuminate\Support\Facades\Crypt;
use Lyd3e\Lbcp\Safety\Lyd3e;

class EncryptController extends Lyd3e
{
    /**
     * 将参数进行加密
     */
    public function EncryptParams()
    {
        //file_get_contents("php://input")获取请求原始数据流
        $params = Crypt::encryptString(json_encode(json_decode(file_get_contents("php://input"))));

        $data = [
            'params' => $params
        ];

        return $this->responseHandler('00000', null, $data);
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $showSensitiveFields = false;

    public function toArray($request)
    {
        if (!$this->showSensitiveFields) {
            $this->resource->addHidden(['phone', 'email']);
        }

        $data = parent::toArray($request);

        $data['bound_phone'] = $this->resource->phone ? true : false;
        // $data['phone'] = $this->resource->phone ? substr_replace($this->resource->phone, '****',3, 4) : '';
        $data['bound_wechat'] = ($this->resource->weixin_unionid || $this->resource->weixin_openid) ? true : false;

        return $data;
    }

    public function showSensitiveFields()
    {
        $this->showSensitiveFields = true;

        return $this;
    }
}

<?php

namespace App\Models;

class ErrorCode
{
    // 本地端口已存在
    const V2RAY_LOCAL_PORT_EXIST = 103;
    // 分享链接解析异常
    const SHARE_URL_RESOLVE = 301;
    // 订阅条目名称重复
    const SUBSCRIBE_NAME_EXIST = 401;
    // 订阅条目链接重复
    const SUBSCRIBE_URL_EXIST = 402;
    // 数据校验失败
    const VALIDATION_EXCEPTION = 901;
    // 数据不存在
    const NOT_EXIST = 902;
    // 数据已存在
    const ALREADY_EXISTS = 903;
    // 数据保存失败
    const DATA_SAVE_FAIL = 904;
    // 数据删除失败
    const DELETE_FAIL = 905;
    // 检查更新失败
    const CHECK_UPDATE_FAIL = 911;
}

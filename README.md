# doupay-php

修改相应的 vhost.conf 以支持pathinfo，这样可以使用通用的doupay-h5项目前端来测试（可选）:
```javascript
location / {
    if (!-e $request_filename) {
        rewrite  ^/(.*)$  /demo.php?method=$1  last;
        break;
    }
}
```
如自己写前端程序可以参考demo.php。

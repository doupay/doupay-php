# doupay-php

可选：修改相应的 vhost.conf 以支持pathinfo:
```javascript
location / {
    if (!-e $request_filename) {
        rewrite  ^/(.*)$  /demo.php?method=$1  last;
        break;
    }
}
```

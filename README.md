# doupay-php

可选：修改相应的 vhost.conf 以支持pathinfo:
```javascript
if (!-e $request_filename) {
				rewrite  ^/(.*)$  /index.php?method=$1  last;
				break;
}
```

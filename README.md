# WP Orphanage Plus

Plugin to promote users with no roles set (the orphans) to the role from other blog where they registered or to default if any found.

## 使用条件

多个 WP 站点使用一个数据库,以不同的表前缀区分不同站点的除用户之外的数据;

假如同一数据库有N个WordPress 站点的数据表,不同的 WordPress 站点以不同的
数据表前缀做区别, 其中有 `www.site-1.com` 和 `www.site-2.com` 两个站点,想让
site-2 这个站点(数据表前缀为 s2_ )使用 site-1 这个站点(数据表前缀为 s1_ )的用户数据表,可以在 site-2 的 `wp-config.php`, 定义两个常量:

```php
define('CUSTOM_USER_TABLE', 's1_users');
define('CUSTOM_USER_META_TABLE', 's1_usermeta');
```


## Dev Log

```

1.0.0 : Based on WP-Orphanage Extended (version 1.1) by MELONIQ.NET


```

## 原版迁移步骤

按以下步骤进行面板文件迁移：

    git remote set-url origin https://github.com/yua98/v2board  
    git checkout master  
    ./update.sh  


按以下步骤刷新设置缓存，重启队列:

    php artisan config:clear
    php artisan config:cache
    php artisan horizon:terminate

最后进入后台重新保存主题： 主题配置-主题设置-确定

# **V2Board**

- PHP7.3+
- Composer
- MySQL5.5+
- Redis
- Laravel

## How to Feedback
Follow the template in the issue to submit your question correctly, and we will have someone follow up with you.

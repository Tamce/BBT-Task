# 百步梯秋招任务项目
## 简单说明
* 使用 Composer 组织代码，主要使用 Composer 的 autoloader
* `./ElfStack` 文件夹下的组件均为本人所写，这些组件均已经(或打算)上传至 packagist ，但为了避免频繁的 `composer update`, `composer require`，因此统一组织到这个文件夹下，而不是更为合适的 `./src/Core` 下。

## 使用说明
* 使用时将 web 根目录指定到 `./public` 下即可。
* 使用前请逐一查看 `./src/Config` 下的各个文件进行使用前配置。
* 访问 `domain/installer/install` 可执行安装指令(初始化数据库表)
* 访问 `domain/installer/uninstall` 可执行卸载指令(删除数据库表)
* 访问 `domain/installer/reinstall` 可自动执行卸载指令后执行安装指令

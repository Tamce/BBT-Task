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

## 约定
* ajax 请求返回键包含 `status`，值为 `['success', 'error', 'notice']` 之一
* ajax 请求如果 `status` 不为 `success`，则 `info` 键中包含具体说明
* 请求时带上 HTTP 头 `X-Method-Override` 可以重写 HTTP 动词
* 请求时带上 HTTP 头 `X-Session-Id` 可以指定会话id，但必须通过 `X-Credential` 的验证
* HTTP 头 `X-Credential` 包含了重现会话所需要的加密令牌

## 关于代码的一些说明
 由于一开始准备按整个需求做，而且准备做前后分离(做成SPA)，使用ajax + api 交互，但是既然要提前交基础功能，所以没有使用单页，而且一些 api 目前很乱，是为了让目前的这个架构能够正确工作而所做的变更和妥协。 例如 api 的 jump 键。

## Api Documents
 所有数据暂时以 form-data 形式传输给服务器
### Authorization
#### request
 * url: /api/authorization
 * method: POST
 * data: username=xxx&password=xxx

#### response
 * status: 状态
 * info: 附加信息
 * data: 若成功则为用户信息
 * credential: 令牌
 * session: 会话id

### Current User
 __Authorization Required__
#### request
 * url: /api/user
 * method: GET / PATCH
 * data: (PATCH) gender=xxx&name=xxx&classname=xxx

#### response
 * status: 状态
 * data: 当前用户的当前信息
 * info: 附加信息

### All User
#### request
 * url: /api/users
 * method: GET
 * queryString: ?begin={start_index}&count={count}

#### response
 * status: 状态
 * data: 数据
 * totalCount: 记录总数

## TODO
 * 增加 API List 视图
 * 更改 User 控制器，规范化 RESTful API
 * 移除现有视图，采用 SPA + API
 * 验证用户名密码后返回 Token，使用 Token 重建带有权限的会话

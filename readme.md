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
 目前存在一些潜在的安全问题如 xss注入 尚未理会
 目前后端关于查询的部分性能有待完善(在小数据量时影响不大)

 因为编写前端时间分配问题以及本人前端水平不佳，因此前端体验没有提升至最佳，但功能点均已实现

## Api Documents
 所有数据暂时以 form-data 形式传输给服务器
---
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

---

### Logout
 * url: /api/logout
 * method: GET

---

### Current User
 __Authorization Required__
 新注册用户可以有一次免审核修改个人信息的机会
#### request
 * url: /api/user
 * method: GET / PATCH
 * data: (PATCH) gender=xxx&name=xxx&classname=xxx

#### response
 * status: 状态
 * data: 当前用户的当前信息
 * info: 附加信息

---

### All User
 __Authorization Required (Admin)__
#### request
 * url: /api/users
 * method: GET
 * queryString: ?begin={start_index}&count={count}&search={keyword}

#### response
 * status: 状态
 * data: 数据
 * totalCount: 记录总数

---

### Register
#### request
 * url: /api/users?key={key}
 * method: POST
 * data: username={username}&password={password}&userGroup={1|2|3}
 如果 userGroup 为 `3` 即 `Admin`，则必须在请求url后附加 `key`，将使用该值与 `Constants::Key` 比较，如果相同则允许创建 `Admin` 用户。

#### response
 * status: 状态
 * data: 新创建的用户信息
 * session: Session-Id
 * credential: 令牌信息

---

### User Info
 __Authorization Required__
 Student Group 只能查看同班的信息
#### request
 * url: /api/users/{username}
 * method: GET

#### response
 * status: 状态
 * data: 目标用户信息

---

### Verify list
 __Authorization Required__
#### request
 * url: /api/verify_update
 * method: GET

#### response
 * status: 状态
 * data: 数据

---

### Verify Update
 __Authorization Required__
#### request
 * url: /api/verify_update/{username}
 * method: GET

#### response
 (if no error)
 * {"status": "success", "info": "Operation Complete!"}

---

### Create Class
 __Authorization Required__ (Admin)
#### request
 * url: /api/class
 * method: POST
 * data: classname={name}

#### response
 * status: 状态
 * data: 班级信息数据

---

### List Class
 __Authorization Required__
#### request
 * url: /api/class
 * method: GET

#### response
 * status: 状态
 * data: 数据

---

### Show Class Info
 __Authorization Required__
#### request
 * url: /api/class/{classname}
 * method: GET

#### response
 * status: 状态
 * data: 数据

---

### View Class's member
 __Authorization Required__
#### request
 * url: /api/class/{classname}/{student|teacher|all}(?begin={begin_index}&count={count})
 * method: GET

#### response
 * status: 状态
 * data: 数据
 * totalCount: 总数

---

### Export Xls Data
 __Authorization Required__ (Admin)
#### request
 * url: /api/export/all
 * url: /api/export/{classname}/{student|teacher|all}

---

## Independent CURL Tests
 1. curl public.bbt.localhost/api/users -X POST -d "username=tamce&password=123&userGroup=1"
 2. curl public.bbt.localhost/api/user -H "X-Session-Id: 25atfnfvm3vkmuvfhkmdadlbl3" -H "X-Credential: jfioBn7gVznyhKv"
 3. curl public.bbt.localhost/api/user -X PATCH -d "gender=male&name=Tamce&classname=class%201" -H "X-Session-Id: 25atfnfvm3vkmuvfhkmdadlbl3" -H "X-Credential: jfioBn7gVznyhKv"

# console
webman-build

# DDD 项目结构示例

本项目采用领域驱动设计（DDD）架构，目录结构如下：

## 项目结构

~~~
src
│   ├── 模块目录
│   │   ├── presentation                # 表示层（负责与用户交互。包含显示信息、解释用户命令）
│   │   │   ├── http                    # 控制器
│   │   │   ├── console                 # 命令
│   │   │   ├── api                     # 管理来自外部 API 的请求
│   │   │   └── ...
│   │   ├── application                 # 应用层（用来协调用户与各应用以及各应用之间的交互。不包含业务逻辑、不保存业务对象的状态）
│   │   │   ├── logic                   # 逻辑层
│   │   │   ├── mappers                 # 映射
│   │   │   ├── transform               # 转换器
│   │   │   ├── repository              # 仓储
│   │   │   ├── validate                # 验证器
│   │   │   ├── response                # 响应
│   │   │   └── ...
│   │   ├── domain                      # 领域层（负责表达业务概念，业务状态信息以及业务规则。包含领域模型、领域信息、业务对象的状态。领域层是业务软件的核心）
│   │   │   ├── model                   # 聚合根
│   │   │   │   ├── entity              # 实体
│   │   │   │   ├── valueObjects        # 值对象
│   │   │   │   └── ...
│   │   │   ├── event                   # 事件
│   │   │   ├── factory                 # 工厂
│   │   │   ├── policies                # 权限策略
│   │   │   ├── service                 # 领域服务
│   │   │   ├── error                   # 业务异常值
│   │   │   ├── enum                    # 常量枚举值
│   │   │   ├── repository              # 仓储接口
│   │   │   └── ...
│   │   ├── infrastructure              # 基础层（为其他各层提供技术能力。包括为应用层传递消息、为领域层提供持久化机制。基础设施层还能够通过架构框架来支持四个层次间的交互模式。）
│   │   │   ├── model                   # MySql数据模型
│   │   │   ├── jobs                    # 消息队列
│   │   │   └── ...
│   │   └── ...
│   └── ...
└── ...
~~~

## 安装项目

```bash
composer require blankkids/webman-build
```

## 执行命令

```bash
//创建模块 - 无链接数据库
php webman blankkids-build-module:base <表名例如：UserOrder> <模块名>
//创建模块 - 链接数据库
php webman blankkids-build-module:base-online <表名例如：UserOrder> <模块名>

```